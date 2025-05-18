{{-- resources/views/admin/product/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detalles del Producto')

@section('page_header')
    Detalles del Producto: <span class="text-muted" id="productNameHeader">{{ $product->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- El título principal ya está en @page_header --}}
                <h5 class="mb-0">Información Detallada <small class="text-white-50" id="productNameShowCardHeader">({{ $product->name }})</small></h5>
                <div>
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <dl class="row">
                            <dt class="col-sm-4">ID</dt>
                            <dd class="col-sm-8" id="productId">{{ $product->id }}</dd>

                            <dt class="col-sm-4">Nombre</dt>
                            <dd class="col-sm-8">{{ $product->name }}</dd> {{-- El nombre ya está en el @page_header y en el card-header --}}

                            <dt class="col-sm-4">Código</dt>
                            <dd class="col-sm-8" id="productCode">{{ $product->code }}</dd>

                            <dt class="col-sm-4">Categoría</dt>
                            <dd class="col-sm-8" id="productCategory">{{ $product->category->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Proveedor</dt>
                            <dd class="col-sm-8" id="productProvider">{{ $product->provider->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Stock</dt>
                            <dd class="col-sm-8" id="productStock">{{ $product->stock }} unidades</dd>

                            <dt class="col-sm-4">Precio Venta</dt>
                            <dd class="col-sm-8" id="productSellPrice">{{ number_format($product->sell_price, 2, ',', '.') }} €</dd>

                            <dt class="col-sm-4">Estado</dt>
                            <dd class="col-sm-8" id="productStatus">
                                @if($product->status == 'ACTIVE')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4 mt-3">Código de Barras</dt>
                            <dd class="col-sm-8 mt-3" id="productBarcodeHtmlContainer">
                                {!! $barcodeHtml !!} {{-- Se mostrará en HTML, pero en PDF será texto o nada si es SVG puro --}}
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($product->image)
                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" style="max-height: 250px; object-fit: contain;" id="productImageElement">
                        @else
                            <p class="text-muted text-center mt-5">Sin imagen</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning fw-semibold me-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-semibold">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal for PDF Export Options --}}
    <div class="modal fade" id="pdfDetailExportModal" tabindex="-1" aria-labelledby="pdfDetailExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfDetailExportModalLabel">Exportar Detalles a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfDetailFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfDetailFilenameInput" placeholder="nombre_archivo.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfDetailExportBtn">Confirmar y Exportar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfDetailModalEl = document.getElementById('pdfDetailExportModal');
    const pdfDetailModal = pdfDetailModalEl ? new bootstrap.Modal(pdfDetailModalEl) : null;
    const pdfDetailFilenameInput = document.getElementById('pdfDetailFilenameInput');

    function exportProductDetailsToPdf(filename) {
        try {
            const { jsPDF } = window.jspdf;
            if (!jsPDF) { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }

            const doc = new jsPDF();
            let yPos = 15;

            const productName = document.getElementById('productNameHeader')?.innerText || // Del nuevo @page_header
                                document.getElementById('productNameShowCardHeader')?.innerText.match(/\(([^)]+)\)/)?.[1] || // Del card-header (extraer de paréntesis)
                                '{{ $product->name }}'; // Fallback directo
            const productId = document.getElementById('productId')?.innerText;
            const defaultFilename = `detalle_producto_${(productName || 'Producto').replace(/[^a-z0-9]/gi, '_')}_${(productId || 'N_A').replace(/[^a-z0-9]/gi, '_')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles del Producto: ${productName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                let value = valueElement ? valueElement.innerText.trim() : 'N/A';
                if (valueId === 'productBarcodeHtmlContainer' && valueElement && valueElement.querySelector('svg')) {
                    value = '[Código de Barras Gráfico]'; // Indicador para el PDF
                }
                if (valueId === 'productStatus' && valueElement && valueElement.querySelector('.badge')) {
                    value = valueElement.querySelector('.badge').innerText.trim(); // Obtener texto del badge
                }
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "productId");
            // El nombre ya está en el título
            addDetail("Código", "productCode");
            addDetail("Categoría", "productCategory");
            addDetail("Proveedor", "productProvider");
            addDetail("Stock", "productStock");
            addDetail("Precio Venta", "productSellPrice");
            addDetail("Estado", "productStatus");
            addDetail("Código de Barras (Texto)", "productBarcodeHtmlContainer"); // Se indica que es texto

            // La lógica para añadir la imagen al PDF (comentada en el original) se mantiene comentada.
            // Si se descomenta, asegurarse de que yPos se ajuste correctamente.

            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles del producto:", error);
            alert("Error al generar PDF de detalles del producto. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const productId = document.getElementById('productId')?.innerText || 'N_A';
            const productName = (document.getElementById('productNameHeader')?.innerText || '{{ $product->name }}').replace(/[^a-z0-9]/gi, '_').substring(0,30) || 'producto';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            
            let defaultFilename = `detalle_${productName}_${productId}_${todayForFilename}.pdf`;
            // Reemplazar caracteres no válidos para nombres de archivo
            defaultFilename = defaultFilename.replace(/[^a-z0-9_.-]/gi, '_').replace(/__+/g, '_');

            pdfDetailFilenameInput.value = defaultFilename;
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportProductDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });

});
</script>
@endpush

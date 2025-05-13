{{-- resources/views/admin/product/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Producto: <span id="productNameShow">{{ $product->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButton" class="btn btn-sm btn-info me-2">
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
                            <dd class="col-sm-8">{{ $product->name }}</dd> {{-- Ya capturado en productNameShow --}}

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
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning fw-semibold">
                    <i class="bi bi-pencil"></i> Editar
                </a>
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
    const exportButton = document.getElementById('exportDetailPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const productName = document.getElementById('productNameShow')?.innerText || 'Producto';
            const productId = document.getElementById('productId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles del Producto: ${productName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                // Para el código de barras, si es SVG, innerText podría ser vacío.
                // Tomamos el texto o indicamos si es un elemento complejo.
                let value = valueElement ? valueElement.innerText.trim() : 'N/A';
                if (valueId === 'productBarcodeHtmlContainer' && valueElement && valueElement.querySelector('svg')) {
                    value = '[Código de Barras Gráfico]'; // Indicador para el PDF
                }
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "productId");
            addDetail("Código", "productCode");
            addDetail("Categoría", "productCategory");
            addDetail("Proveedor", "productProvider");
            addDetail("Stock", "productStock");
            addDetail("Precio Venta", "productSellPrice");
            addDetail("Estado", "productStatus");
            addDetail("Código de Barras", "productBarcodeHtmlContainer");

            // Añadir la imagen al PDF es más complejo y requiere convertirla a dataURL.
            // const imgElement = document.getElementById('productImageElement');
            // if (imgElement && imgElement.complete && imgElement.naturalHeight !== 0) {
            //     try {
            //         const canvas = document.createElement('canvas');
            //         canvas.width = imgElement.naturalWidth;
            //         canvas.height = imgElement.naturalHeight;
            //         const ctx = canvas.getContext('2d');
            //         ctx.drawImage(imgElement, 0, 0);
            //         const imgData = canvas.toDataURL('image/jpeg'); // o image/png
            //         doc.addImage(imgData, 'JPEG', 150, 20, 50, 50); // Ajustar posición y tamaño
            //         yPos = Math.max(yPos, 20 + 50 + 10); // Ajustar yPos si la imagen es alta
            //     } catch (e) {
            //         console.error("Error al añadir imagen al PDF:", e);
            //         doc.text("Error al cargar imagen.", 150, 20);
            //     }
            // } else if (imgElement) {
            //     doc.text("[Imagen no disponible para PDF]", 150, 20);
            // }

            doc.save(`detalle_producto_${productId || 'N_A'}.pdf`);
        });
    }
});
</script>
@endpush

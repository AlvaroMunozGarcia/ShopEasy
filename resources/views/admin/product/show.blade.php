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
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center"> 
                <h5 class="mb-2 mb-md-0">Información Detallada <small class="text-white-50" id="productNameShowCardHeader">({{ $product->name }})</small></h5>
                <div class="mt-2 mt-md-0">
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
                            <dd class="col-sm-8">{{ $product->name }}</dd> 

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

                        </dl>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" style="max-height: 250px; object-fit: contain;" id="productImageElement">
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
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);
        if (userFilename === null) {
            return null;
        }
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }
        return finalFilename;
    }

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', function () {
        exportProductDetailsToPDF();
    });
});

function exportProductDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const productName = document.getElementById('productNameHeader').innerText;
    const productData = [
        ['ID', document.getElementById('productId').innerText],
        ['Nombre', productName],
        ['Código', document.getElementById('productCode').innerText],
        ['Categoría', document.getElementById('productCategory').innerText],
        ['Proveedor', document.getElementById('productProvider').innerText],
        ['Stock', document.getElementById('productStock').innerText],
        ['Precio Venta', document.getElementById('productSellPrice').innerText],
        ['Estado', document.getElementById('productStatus').innerText.trim()]
    ];

    doc.setFontSize(14);
    doc.text(`Detalles del Producto: ${productName}`, 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: productData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] }
    });
     const productImageElement = document.getElementById('productImageElement');
     if (productImageElement && productImageElement.src && !productImageElement.src.endsWith('Sin%20imagen') && !productImageElement.src.includes('placeholder')) { // Evitar añadir si es placeholder
         try {
            const imgData = productImageElement.src; 
            const imgProps = doc.getImageProperties(imgData);
            const imgWidth = 50; 
            const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
            const xPosition = (doc.internal.pageSize.getWidth() - imgWidth) / 2; 
            let yPosition = doc.lastAutoTable.finalY + 10; 

            if (yPosition + imgHeight > doc.internal.pageSize.getHeight() - 10) { 
                doc.addPage();
                yPosition = 20;
            }
             const imgType = imgData.toLowerCase().endsWith('.png') ? 'PNG' : 'JPEG'; 
             doc.addImage(imgData, imgType, xPosition, yPosition, imgWidth, imgHeight);
         } catch (e) {
            console.error("Error al añadir la imagen al PDF:", e);
         }
     }
    const filename = getCustomFilename(`producto_${productName.replace(/\s+/g, '_')}`, 'pdf');
    if (filename) {
        doc.save(filename);
    } 
}
</script>
@endpush

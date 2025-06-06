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

                        </dl>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($product->image)
                            {{-- Corregido para usar el storage link --}}
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
    // Función para obtener el nombre de archivo personalizado
    // MOVIDA FUERA DEL LISTENER DOMContentLoaded para que sea accesible globalmente
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        // Formato de fecha YYYY-MM-DD
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;

        // Mostrar prompt al usuario
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);

        // Si el usuario cancela, devuelve null
        if (userFilename === null) {
            return null;
        }

        // Usar el nombre del usuario si no está vacío, de lo contrario usar el por defecto
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();

        // Asegurarse de que la extensión esté presente
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }
        return finalFilename;
    }

document.addEventListener('DOMContentLoaded', function () {
    // Función para obtener el nombre de archivo personalizado

    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', function () {
        exportProductDetailsToPDF();
    });
});

function exportProductDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Obtener los datos del producto
    const productName = document.getElementById('productNameHeader').innerText;
    const productData = [
        ['ID', document.getElementById('productId').innerText],
        ['Nombre', productName],
        ['Código', document.getElementById('productCode').innerText],
        ['Categoría', document.getElementById('productCategory').innerText],
        ['Proveedor', document.getElementById('productProvider').innerText],
        ['Stock', document.getElementById('productStock').innerText],
        ['Precio Venta', document.getElementById('productSellPrice').innerText],
        ['Estado', document.getElementById('productStatus').innerText.trim()] // .trim() para quitar espacios extra si el badge los añade
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

     // Intentar añadir la imagen del producto si existe
     const productImageElement = document.getElementById('productImageElement');
     if (productImageElement && productImageElement.src && !productImageElement.src.endsWith('Sin%20imagen') && !productImageElement.src.includes('placeholder')) { // Evitar añadir si es placeholder
         try {
            const imgData = productImageElement.src; // jsPDF puede manejar URLs de imágenes directamente en muchos casos
            // Calcular la posición y tamaño de la imagen
            // Esto es un ejemplo, podrías necesitar ajustar las dimensiones y posición
            const imgProps = doc.getImageProperties(imgData);
            const imgWidth = 50; // Ancho deseado para la imagen en el PDF
            const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
            const xPosition = (doc.internal.pageSize.getWidth() - imgWidth) / 2; // Centrar imagen
            let yPosition = doc.lastAutoTable.finalY + 10; // Debajo de la tabla

            if (yPosition + imgHeight > doc.internal.pageSize.getHeight() - 10) { // Si no cabe, añadir nueva página
                doc.addPage();
                yPosition = 20;
            }
             // Añadir la imagen. Asegúrate de que el tipo de imagen sea correcto (JPEG, PNG, etc.)
             const imgType = imgData.toLowerCase().endsWith('.png') ? 'PNG' : 'JPEG'; // Simple guess based on extension
             doc.addImage(imgData, imgType, xPosition, yPosition, imgWidth, imgHeight);
         } catch (e) {
            console.error("Error al añadir la imagen al PDF:", e);
            // Podrías añadir un texto al PDF indicando que la imagen no se pudo cargar
         }
     }
    const filename = getCustomFilename(`producto_${productName.replace(/\s+/g, '_')}`, 'pdf');
    if (filename) {
        doc.save(filename);
    } // Asegúrate de que esta llave de cierre esté presente
}
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Detalles de la Categoría')

@section('page_header')
    Detalles de la Categoría: <span class="text-muted" id="categoryNameHeader">{{ $category->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="mb-2 mb-md-0">Información Detallada <small class="text-white-50" id="categoryNameCardHeader">({{ $category->name }})</small></h5>
                <div class="mt-2 mt-md-0">
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Columna de información --}}
                    <div class="col-md-12">
                        <dl class="row">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9" id="categoryId">{{ $category->id }}</dd>

                            <dt class="col-sm-3">Nombre:</dt>
                            <dd class="col-sm-9">{{ $category->name }}</dd> {{-- El nombre ya está en el @page_header y en el card-header --}}

                            <dt class="col-sm-3">Descripción:</dt>
                            <dd class="col-sm-9" id="categoryDescription">{{ $category->description ?: 'No especificada' }}</dd>

                            <dt class="col-sm-3">Fecha de Creación:</dt>
                            <dd class="col-sm-9" id="categoryCreatedAt">{{ $category->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-3">Última Actualización:</dt>
                            <dd class="col-sm-9" id="categoryUpdatedAt">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                 <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta categoría?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-semibold">
                        <i class="bi bi-trash me-1"></i> Eliminar
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
        exportCategoryDetailsToPDF();
    });
});

function exportCategoryDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const categoryData = [
        ['ID', document.getElementById('categoryId').innerText],
        ['Nombre', document.getElementById('categoryNameHeader').innerText],
        ['Descripción', document.getElementById('categoryDescription').innerText],
        ['Fecha de Creación', document.getElementById('categoryCreatedAt').innerText],
        ['Última Actualización', document.getElementById('categoryUpdatedAt').innerText]
    ];

    doc.setFontSize(14);
    doc.text('Detalles de la Categoría', 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: categoryData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] }
    });

    const categoryName = document.getElementById('categoryNameHeader')?.innerText ?? 'Categoria';
    const filename = getCustomFilename(`categoria_${categoryName.replace(/\s+/g, '_')}`, 'pdf');
    if (filename) {
        doc.save(filename);
    }
}
</script>
@endpush

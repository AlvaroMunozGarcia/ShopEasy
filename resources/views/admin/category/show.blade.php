@extends('layouts.admin')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles de la Categoría: <span id="categoryName">{{ $category->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButton" class="btn btn-sm btn-info me-2">
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
                            <dd class="col-sm-9">{{ $category->name }}</dd> {{-- Ya capturado arriba --}}

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
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportDetailPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const categoryName = document.getElementById('categoryName')?.innerText || 'Categoría';
            const categoryId = document.getElementById('categoryId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles de Categoría: ${categoryName}`, 14, yPos);
            yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const value = document.getElementById(valueId)?.innerText || 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "categoryId");
            addDetail("Descripción", "categoryDescription");
            addDetail("Fecha de Creación", "categoryCreatedAt");
            addDetail("Última Actualización", "categoryUpdatedAt");

            // Note: This view doesn't show related products in a table in the provided code,
            // so autoTable is not used here. If you add a table of products,
            // you would add autoTable similar to the purchase/sale show views.

            doc.save(`detalle_categoria_${categoryId || 'N_A'}.pdf`);
        });
    }
});
</script>
@endpush

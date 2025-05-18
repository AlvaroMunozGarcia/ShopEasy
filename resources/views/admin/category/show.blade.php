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
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- El título principal ya está en @page_header --}}
                <h5 class="mb-0">Información Detallada <small class="text-white-50" id="categoryNameCardHeader">({{ $category->name }})</small></h5>
                <div>
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

    function exportCategoryDetailsToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;
            // Intentar obtener el nombre de la categoría de varias fuentes
            const categoryName = document.getElementById('categoryNameHeader')?.innerText || // Del nuevo @page_header
                                 document.getElementById('categoryNameCardHeader')?.innerText.match(/\(([^)]+)\)/)?.[1] || // Del card-header (extraer de paréntesis)
                                 '{{ $category->name }}'; // Fallback directo
            const categoryId = document.getElementById('categoryId')?.innerText;
            const defaultFilename = `detalle_categoria_${(categoryName || 'Categoria').replace(/[^a-z0-9]/gi, '_')}_${(categoryId || 'N_A').replace(/[^a-z0-9]/gi, '_')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles de Categoría: ${categoryName}`, 14, yPos);
            yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const element = document.getElementById(valueId);
                const value = element ? element.innerText.trim() : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "categoryId");
            // El nombre ya está en el título
            addDetail("Descripción", "categoryDescription");
            addDetail("Fecha de Creación", "categoryCreatedAt");
            addDetail("Última Actualización", "categoryUpdatedAt");

            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles de categoría:", error);
            alert("Error al generar PDF de detalles de categoría. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const categoryId = document.getElementById('categoryId')?.innerText || 'N_A';
            const categoryName = (document.getElementById('categoryNameHeader')?.innerText || // Del nuevo @page_header
                                  document.getElementById('categoryNameCardHeader')?.innerText.match(/\(([^)]+)\)/)?.[1] || // Del card-header
                                  '{{ $category->name }}').replace(/[^a-z0-9]/gi, '_').substring(0,30) || 'categoria';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_${categoryName}_${categoryId}_${todayForFilename}.pdf`.replace(/[^a-z0-9_.-]/gi, '_').replace(/__+/g, '_');
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportCategoryDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush

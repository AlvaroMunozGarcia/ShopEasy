{{-- resources/views/admin/provider/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detalles del Proveedor')

@section('page_header')
    Detalles del Proveedor: <span class="text-muted" id="providerNameHeader">{{ $provider->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('providers.index') }}">Proveedores</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- El título principal ya está en @page_header --}}
                <h5 class="mb-0">Información Detallada <small class="text-white-50" id="providerNameCardHeader">({{ $provider->name }})</small></h5>
                <div>
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('providers.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9" id="providerId">{{ $provider->id }}</dd>

                    <dt class="col-sm-3">Nombre</dt> {{-- El nombre ya está en el @page_header y en el card-header --}}
                    <dd class="col-sm-9">{{ $provider->name }}</dd> {{-- Ya capturado en providerName --}}

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9" id="providerEmail">{{ $provider->email ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Número RUC</dt>
                    <dd class="col-sm-9" id="providerRucNumber">{{ $provider->ruc_number ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Teléfono</dt>
                    <dd class="col-sm-9" id="providerPhone">{{ $provider->phone ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Dirección</dt>
                    <dd class="col-sm-9" id="providerAddress">{{ $provider->address ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Sitio Web</dt> {{-- Añadido si existe en tu modelo Provider --}}
                    <dd class="col-sm-9" id="providerWebsite">{{ $provider->website ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Creado</dt>
                    <dd class="col-sm-9" id="providerCreatedAt">{{ $provider->created_at->format('d/m/Y H:i:s') }}</dd>

                    <dt class="col-sm-3">Actualizado</dt>
                    <dd class="col-sm-9" id="providerUpdatedAt">{{ $provider->updated_at->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>

            {{-- Nueva sección para acciones de productos --}}
            <div class="card-body border-top">
                <h5 class="mb-3 pt-3">Productos Relacionados con {{ $provider->name }}</h5>
                <div class="mb-2">
                    <a href="{{ route('products.create', ['provider_id' => $provider->id]) }}" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle"></i> Añadir Producto para este Proveedor
                    </a>
                    <a href="{{ route('products.index', ['provider_id' => $provider->id]) }}" class="btn btn-info">
                        <i class="bi bi-list-ul"></i> Ver Productos de este Proveedor
                    </a>
                </div>
            </div>
            {{-- Fin de la nueva sección --}}

            <div class="card-footer text-end">
                <a href="{{ route('providers.edit', $provider) }}" class="btn btn-warning fw-semibold me-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este proveedor?');">
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

    function exportProviderDetailsToPdf(filename) {
        try {
            const { jsPDF } = window.jspdf;
            if (!jsPDF) { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const doc = new jsPDF();
            let yPos = 15;
            // Intentar obtener el nombre del proveedor de varias fuentes
            const providerName = document.getElementById('providerNameHeader')?.innerText || // Nuevo ID en page_header
                                 document.getElementById('providerNameCardHeader')?.innerText.match(/\(([^)]+)\)/)?.[1] || // Del card-header (extraer de paréntesis)
                                 '{{ $provider->name }}'; // Fallback directo
            const providerId = document.getElementById('providerId')?.innerText;
            const defaultFilename = `detalle_proveedor_${(providerId || 'N_A').replace(/[^a-z0-9]/gi, '_')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles del Proveedor: ${providerName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                const value = valueElement ? valueElement.innerText.trim() : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "providerId");
            // El nombre ya está en el título, pero si quieres repetirlo:
            // doc.text(`Nombre: ${providerName}`, 14, yPos); yPos += 7;
            addDetail("Email", "providerEmail");
            addDetail("Número RUC", "providerRucNumber");
            addDetail("Teléfono", "providerPhone");
            addDetail("Dirección", "providerAddress");
            addDetail("Sitio Web", "providerWebsite"); // Asegúrate que este ID exista si tienes el campo
            addDetail("Fecha de Creación", "providerCreatedAt");
            addDetail("Última Actualización", "providerUpdatedAt");

            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles del proveedor:", error);
            alert("Error al generar PDF de detalles del proveedor. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const providerId = document.getElementById('providerId')?.innerText || 'N_A';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_proveedor_${providerId.replace(/[^a-z0-9]/gi, '_')}_${todayForFilename}.pdf`;
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportProviderDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush

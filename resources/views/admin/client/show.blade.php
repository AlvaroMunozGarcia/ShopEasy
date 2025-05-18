@extends('layouts.admin') {{-- Usar tu layout personalizado --}}

@section('title', 'Detalles del Cliente')

@section('page_header')
    Detalles del Cliente: <span class="text-muted" id="clientNameHeader">{{ $client->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content') {{-- Contenido principal para el @yield('content') --}}
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- El título principal ya está en @page_header, pero este H5 puede servir como título de la tarjeta --}}
                <h5 class="mb-0">Información Detallada <small class="text-white-50">({{ $client->name }})</small></h5>
                <div>
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9" id="clientId">{{ $client->id }}</dd>

                    <dt class="col-sm-3">Nombre</dt> {{-- El nombre ya está en el @page_header y en el card-header --}}
                    <dd class="col-sm-9">{{ $client->name }}</dd> {{-- Ya capturado en clientName --}}

                    <dt class="col-sm-3">DNI</dt>
                    <dd class="col-sm-9" id="clientDni">{{ $client->dni }}</dd>

                    <dt class="col-sm-3">RUC</dt>
                    <dd class="col-sm-9" id="clientRuc">{{ $client->ruc ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Dirección</dt>
                    <dd class="col-sm-9" id="clientAddress">{{ $client->address ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Teléfono</dt>
                    <dd class="col-sm-9" id="clientPhone">{{ $client->phone ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9" id="clientEmail">{{ $client->email ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Fecha de Creación</dt>
                    <dd class="col-sm-9" id="clientCreatedAt">{{ $client->created_at ? $client->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Última Actualización</dt>
                    <dd class="col-sm-9" id="clientUpdatedAt">{{ $client->updated_at ? $client->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning fw-semibold me-2">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                 <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">
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

    function exportClientDetailsToPdf(filename) {
        const { jsPDF } = window.jspdf;
        try {
            const doc = new jsPDF();
            let yPos = 15;

            const clientName = document.getElementById('clientName')?.innerText || 'Cliente';
            const clientId = document.getElementById('clientId')?.innerText;
            const defaultFilename = `detalle_cliente_${(clientId || 'N_A').replace(/[^a-z0-9]/gi, '_')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles del Cliente: ${clientName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const element = document.getElementById(valueId);
                const value = element ? element.innerText.trim() : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "clientId");
            // El nombre ya está en el título, pero si quieres repetirlo:
            // doc.text(`Nombre: ${clientName}`, 14, yPos); yPos += 7;
            addDetail("DNI", "clientDni");
            addDetail("RUC", "clientRuc");
            addDetail("Dirección", "clientAddress");
            addDetail("Teléfono", "clientPhone");
            addDetail("Email", "clientEmail");
            addDetail("Fecha de Creación", "clientCreatedAt");
            addDetail("Última Actualización", "clientUpdatedAt");
            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles:", error);
            alert("Error al generar PDF de detalles. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const clientId = document.getElementById('clientId')?.innerText || 'N_A';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_cliente_${clientId.replace(/[^a-z0-9]/gi, '_')}_${todayForFilename}.pdf`;
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportClientDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush

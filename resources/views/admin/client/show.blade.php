@extends('layouts.admin') {{-- Usar tu layout personalizado --}}

@section('content') {{-- Contenido principal para el @yield('content') --}}
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Cliente: <span id="clientName">{{ $client->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButton" class="btn btn-sm btn-info me-2">
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

                    <dt class="col-sm-3">Nombre</dt>
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

            const clientName = document.getElementById('clientName')?.innerText || 'Cliente';
            const clientId = document.getElementById('clientId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles del Cliente: ${clientName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const value = document.getElementById(valueId)?.innerText || 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "clientId");
            addDetail("DNI", "clientDni");
            addDetail("RUC", "clientRuc");
            addDetail("Dirección", "clientAddress");
            addDetail("Teléfono", "clientPhone");
            addDetail("Email", "clientEmail");
            addDetail("Fecha de Creación", "clientCreatedAt");
            addDetail("Última Actualización", "clientUpdatedAt");

            doc.save(`detalle_cliente_${clientId || 'N_A'}.pdf`);
        });
    }
});
</script>
@endpush

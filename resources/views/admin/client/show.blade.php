@extends('layouts.admin') 

@section('title', 'Detalles del Cliente')

@section('page_header')
    Detalles del Cliente: <span class="text-muted" id="clientNameHeader">{{ $client->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content') 
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="mb-2 mb-md-0">Información Detallada <small class="text-white-50">({{ $client->name }})</small></h5>
                <div class="mt-2 mt-md-0">
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

                    <dt class="col-sm-3">Nombre</dt> 
                    <dd class="col-sm-9">{{ $client->name }}</dd> 

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
        exportClientDetailsToPDF();
    });
});

function exportClientDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const clientData = [
        ['ID', document.getElementById('clientId').innerText],
        ['Nombre', document.getElementById('clientNameHeader').innerText],
        ['DNI', document.getElementById('clientDni').innerText],
        ['RUC', document.getElementById('clientRuc').innerText],
        ['Dirección', document.getElementById('clientAddress').innerText],
        ['Teléfono', document.getElementById('clientPhone').innerText],
        ['Email', document.getElementById('clientEmail').innerText],
        ['Fecha de Creación', document.getElementById('clientCreatedAt').innerText],
        ['Última Actualización', document.getElementById('clientUpdatedAt').innerText]
    ];

    doc.setFontSize(14);
    doc.text('Detalles del Cliente', 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: clientData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] }
    });

    const clientName = document.getElementById('clientNameHeader')?.innerText ?? 'Cliente';
    const filename = getCustomFilename(`cliente_${clientName.replace(/\s+/g, '_')}`, 'pdf');
    if (filename) {
        doc.save(filename);
    }
}
</script>
@endpush

{{-- resources/views/admin/provider/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Proveedor: <span id="providerName">{{ $provider->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButton" class="btn btn-sm btn-info me-2">
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

                    <dt class="col-sm-3">Nombre</dt>
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

            const providerName = document.getElementById('providerName')?.innerText || 'Proveedor';
            const providerId = document.getElementById('providerId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles del Proveedor: ${providerName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                const value = valueElement ? valueElement.innerText : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "providerId");
            addDetail("Email", "providerEmail");
            addDetail("Número RUC", "providerRucNumber");
            addDetail("Teléfono", "providerPhone");
            addDetail("Dirección", "providerAddress");
            addDetail("Sitio Web", "providerWebsite"); // Asegúrate que este ID exista si tienes el campo
            addDetail("Creado", "providerCreatedAt");
            addDetail("Actualizado", "providerUpdatedAt");

            doc.save(`detalle_proveedor_${providerId || 'N_A'}.pdf`);
        });
    }
});
</script>
@endpush

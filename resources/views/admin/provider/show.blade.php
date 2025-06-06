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
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
    // Función para obtener el nombre de archivo personalizado
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
    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', exportProviderDetailsToPDF);
});

function exportProviderDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const providerData = [
        ['ID', '{{ $provider->id }}'],
        ['Nombre', '{{ $provider->name }}'],
        ['Email', '{{ $provider->email ?? "N/A" }}'],
        ['Número RUC', '{{ $provider->ruc_number ?? "N/A" }}'],
        ['Teléfono', '{{ $provider->phone ?? "N/A" }}'],
        ['Dirección', `{!! str_replace(["\n", "\r"], ['\\n', ''], e($provider->address ?? "N/A")) !!}`],
        ['Sitio Web', '{{ $provider->website ?? "N/A" }}'],
        ['Creado', '{{ $provider->created_at->format("d/m/Y H:i:s") }}'],
        ['Actualizado', '{{ $provider->updated_at->format("d/m/Y H:i:s") }}']
    ];

    doc.setFontSize(14);
    doc.text('Detalles del Proveedor', 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: providerData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] },
        columnStyles: {
            0: { fontStyle: 'bold', cellWidth: 50 },
            1: { cellWidth: 130 }
        }
    });

    const providerName = document.getElementById('providerNameHeader')?.innerText ?? 'Proveedor';
    const filename = getCustomFilename(`proveedor_${providerName.replace(/\s+/g, '_')}`, 'pdf');
    if (filename) {
        doc.save(filename);
    }
}
</script>
@endpush

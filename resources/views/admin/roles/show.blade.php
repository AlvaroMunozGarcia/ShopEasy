@extends('layouts.admin')

@section('title', 'Detalles del Rol: ' . $role->name)

@section('page_header')
    Detalles del Rol: <span class="text-muted">{{ $role->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información Detallada <small class="text-white-50">({{ $role->name }})</small></h5>
                <div>
                    <button type="button" id="exportDetailRolePdfButtonTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light text-primary fw-semibold btn-sm">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID del Rol:</dt>
                    <dd class="col-sm-9">{{ $role->id }}</dd>

                    <dt class="col-sm-3">Nombre del Rol:</dt>
                    <dd class="col-sm-9">{{ $role->name }}</dd>

                    <dt class="col-sm-3">Guard Name:</dt>
                    <dd class="col-sm-9">{{ $role->guard_name }}</dd>

                    <dt class="col-sm-3">Permisos Asignados:</dt>
                    <dd class="col-sm-9">
                        @if ($role->permissions->isNotEmpty())
                            @foreach ($role->permissions as $permission)
                                <span class="badge bg-info text-dark me-1 mb-1">{{ $permission->name }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">Sin permisos asignados</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Fecha de Creación:</dt>
                    <dd class="col-sm-9">{{ $role->created_at ? $role->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Última Actualización:</dt>
                    <dd class="col-sm-9">{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning fw-semibold me-2">
                    <i class="bi bi-pencil-square me-1"></i> Editar Rol
                </a>
            </div>
        </div>
    </div>

    <!-- Modal para Exportar PDF -->
    <div class="modal fade" id="pdfDetailRoleExportModal" tabindex="-1" aria-labelledby="pdfDetailRoleExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfDetailRoleExportModalLabel">Exportar Detalles del Rol a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfDetailRoleFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfDetailRoleFilenameInput" placeholder="detalle_rol.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfDetailRoleExportBtn">Confirmar y Exportar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.9em;
        padding: 0.4em 0.6em;
    }
    .card-footer .btn {
        min-width: 120px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfDetailRoleModalEl = document.getElementById('pdfDetailRoleExportModal');
    const pdfDetailRoleModal = pdfDetailRoleModalEl ? new bootstrap.Modal(pdfDetailRoleModalEl) : null;
    const pdfDetailRoleFilenameInput = document.getElementById('pdfDetailRoleFilenameInput');

    function slugify(text) {
        if (typeof _ !== 'undefined' && typeof _.kebabCase === 'function') {
            return _.kebabCase(text);
        }
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    function exportRoleDetailsToPdf(filename) {
        if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
            console.error("jsPDF no está cargado.");
            alert("Error: jsPDF no está cargado.");
            return;
        }
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let fileName = filename || `rol_${slugify("{{ $role->name }}")}_{{ date('Ymd_His') }}.pdf`;
         if (!fileName.toLowerCase().endsWith('.pdf')) {
            fileName += '.pdf';
        }

        const roleName = "{{ $role->name }}";
        const roleId = "{{ $role->id }}";
        const guardName = "{{ $role->guard_name }}";
        const permissions = @json($role->permissions->pluck('name')->toArray());
        const createdAt = "{{ $role->created_at ? $role->created_at->format('d/m/Y H:i:s') : 'N/A' }}";
        const updatedAt = "{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i:s') : 'N/A' }}";

        doc.setFontSize(18);
        doc.text(`Detalles del Rol: ${roleName}`, 14, 22);
        doc.setFontSize(10);
        doc.text("Fecha de Exportación: " + new Date().toLocaleDateString() + " " + new Date().toLocaleTimeString(), 14, 28);

        let yPos = 40;
        doc.setFontSize(12);

        function addDetail(label, value) {
            doc.setFont(undefined, 'bold');
            doc.text(`${label}:`, 14, yPos);
            doc.setFont(undefined, 'normal');
            doc.text(String(value), 55, yPos);
            yPos += 8;
        }

        addDetail("ID", roleId);
        addDetail("Nombre", roleName);
        addDetail("Guard", guardName);
        addDetail("Fecha de Creación", createdAt);
        addDetail("Última Actualización", updatedAt);

        yPos += 2;
        doc.setFont(undefined, 'bold');
        doc.text("Permisos Asignados:", 14, yPos);
        yPos += 8;
        doc.setFont(undefined, 'normal');
        doc.setFontSize(10);

        if (permissions.length > 0) {
             const permissionsText = permissions.join(', ');
             const splitText = doc.splitTextToSize(permissionsText, doc.internal.pageSize.getWidth() - 28);
             doc.text(splitText, 14, yPos);
        } else {
            doc.text("Sin permisos asignados.", 14, yPos);
        }

        doc.save(fileName);
        if (pdfDetailRoleModal) {
            pdfDetailRoleModal.hide();
        }
    }

    document.getElementById('exportDetailRolePdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailRoleModal && pdfDetailRoleFilenameInput) {
            const roleNameSlug = slugify("{{ $role->name }}");
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailRoleFilenameInput.value = `detalle_rol_${roleNameSlug}_${todayForFilename}.pdf`;
            pdfDetailRoleModal.show();
        }
    });

    document.getElementById('confirmPdfDetailRoleExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailRoleFilenameInput ? pdfDetailRoleFilenameInput.value.trim() : null;
        exportRoleDetailsToPdf(filename);
    });
});
</script>
@endpush

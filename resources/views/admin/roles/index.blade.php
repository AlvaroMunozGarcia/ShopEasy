@extends('layouts.admin')

@section('title', 'Gestión de Roles')

@section('page_header', 'Gestión de Roles')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Roles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Roles</h5>
                <div>
                    <button type="button" id="exportPdfButtonListRolesTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-light text-primary fw-semibold btn-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Crear Nuevo Rol
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rolesTable" class="table table-bordered table-hover mb-0 align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Guard</th>
                                <th>Permisos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="text-center">{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td class="text-center">{{ $role->guard_name }}</td>
                                    <td>
                                        @if ($role->permissions->isNotEmpty())
                                            @foreach ($role->permissions as $permission)
                                                <span class="badge bg-info text-dark me-1 mb-1">{{ $permission->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">Sin permisos asignados</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-outline-info me-1" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        {{-- <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Está seguro de que desea eliminar este rol? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay roles registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Exportar PDF -->
<div class="modal fade" id="pdfExportModalListRoles" tabindex="-1" aria-labelledby="pdfExportModalListRolesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalListRolesLabel">Exportar Listado de Roles a PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="pdfListRolesFilenameInput" class="form-label">Nombre del archivo:</label>
                    <input type="text" class="form-control" id="pdfListRolesFilenameInput" placeholder="listado_roles.pdf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmPdfListRolesExportBtn">Confirmar y Exportar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Si Bootstrap Icons no está globalmente, añádelo aquí o en layouts.admin --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> --}}
<style>
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfModalListRolesEl = document.getElementById('pdfExportModalListRoles');
    const pdfModalListRoles = pdfModalListRolesEl ? new bootstrap.Modal(pdfModalListRolesEl) : null;
    const pdfListRolesFilenameInput = document.getElementById('pdfListRolesFilenameInput');

    function exportRolesListToPdf(filename) {
        if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
            console.error("jsPDF no está cargado.");
            alert("Error: jsPDF no está cargado.");
            return;
        }
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let fileName = filename || 'listado_roles_{{ date('Ymd_His') }}.pdf';
        if (!fileName.toLowerCase().endsWith('.pdf')) {
            fileName += '.pdf';
        }

        doc.text("Listado de Roles - ShopEasy", 14, 16);
        doc.setFontSize(10);
        doc.text("Fecha: " + new Date().toLocaleDateString(), 14, 22);

        // Columnas a incluir en el PDF (excluyendo 'Acciones')
        const headers = [['ID', 'Nombre del Rol', 'Guard', 'Permisos']];
        const data = [];
        const table = document.getElementById('rolesTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const rowData = [];
            // ID
            rowData.push(row.cells[0].innerText);
            // Nombre del Rol
            rowData.push(row.cells[1].innerText);
            // Guard
            rowData.push(row.cells[2].innerText);
            // Permisos
            const permissionBadges = row.cells[3].querySelectorAll('.badge');
            let permissionsText = Array.from(permissionBadges).map(badge => badge.innerText).join(', ');
            if (!permissionsText) permissionsText = row.cells[3].innerText; // En caso de "Sin permisos asignados"
            rowData.push(permissionsText);

            data.push(rowData);
        });

        if (data.length === 0) {
            doc.text("No hay roles para exportar.", 14, 30);
        } else {
            doc.autoTable({
                head: headers,
                body: data,
                startY: 28,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133] }, // Un color verde azulado para la cabecera
                styles: { fontSize: 8, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 15 }, // ID
                    1: { cellWidth: 50 }, // Nombre del Rol
                    2: { cellWidth: 25 }, // Guard
                    3: { cellWidth: 'auto' } // Permisos
                }
            });
        }

        doc.save(fileName);
        if (pdfModalListRoles) {
            pdfModalListRoles.hide();
        }
    }

    document.getElementById('exportPdfButtonListRolesTrigger')?.addEventListener('click', function () {
        if (pdfModalListRoles && pdfListRolesFilenameInput) {
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfListRolesFilenameInput.value = `listado_roles_${todayForFilename}.pdf`;
            pdfModalListRoles.show();
        }
    });

    document.getElementById('confirmPdfListRolesExportBtn')?.addEventListener('click', function () {
        const filename = pdfListRolesFilenameInput ? pdfListRolesFilenameInput.value.trim() : null;
        exportRolesListToPdf(filename);
    });

});
</script>
@endpush

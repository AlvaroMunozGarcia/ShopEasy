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
                    <button type="button" id="exportExcelButtonListRolesTrigger" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
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

<!-- Modal para Exportar Excel -->
<div class="modal fade" id="excelExportModalListRoles" tabindex="-1" aria-labelledby="excelExportModalListRolesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excelExportModalListRolesLabel">Exportar Listado de Roles a Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="excelListRolesFilenameInput" class="form-label">Nombre del archivo:</label>
                    <input type="text" class="form-control" id="excelListRolesFilenameInput" placeholder="listado_roles.xlsx">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmExcelListRolesExportBtn">Confirmar y Exportar</button>
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
{{-- jQuery (necesario para DataTables, aunque no se use para exportar aquí) --}}
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
{{-- DataTables Bootstrap 5 JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar DataTables para la tabla de roles (para paginación, búsqueda, etc.)
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $('#rolesTable').length > 0) {
        try {
            $('#rolesTable').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "searchable": false, "targets": -1 } // La última columna (Acciones)
                ]
            });
        } catch (e) {
            console.error("Error inicializando DataTables para #rolesTable:", e);
        }
    }

    const pdfModalListRolesEl = document.getElementById('pdfExportModalListRoles');
    const pdfModalListRoles = pdfModalListRolesEl ? new bootstrap.Modal(pdfModalListRolesEl) : null;
    const pdfListRolesFilenameInput = document.getElementById('pdfListRolesFilenameInput');

    const excelModalListRolesEl = document.getElementById('excelExportModalListRoles');
    const excelModalListRoles = excelModalListRolesEl ? new bootstrap.Modal(excelModalListRolesEl) : null;
    const excelListRolesFilenameInput = document.getElementById('excelListRolesFilenameInput');

    function getRolesTableData() {
        const headers = ['ID', 'Nombre', 'Guard', 'Permisos'];
        const body = [];
        const table = document.getElementById('rolesTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (row.querySelectorAll('td').length === 1 && row.querySelector('td[colspan]')) { // Fila de "No hay roles"
                return;
            }
            const rowData = [];
            rowData.push(row.cells[0].innerText.trim()); // ID
            rowData.push(row.cells[1].innerText.trim()); // Nombre
            rowData.push(row.cells[2].innerText.trim()); // Guard
            
            const permissionBadges = row.cells[3].querySelectorAll('.badge');
            let permissionsText = Array.from(permissionBadges).map(badge => badge.innerText.trim()).join(', ');
            if (!permissionsText && row.cells[3].innerText.trim() === 'Sin permisos asignados') {
                permissionsText = 'Sin permisos asignados';
            }
            rowData.push(permissionsText);
            body.push(rowData);
        });
        return { headers, body };
    }


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

        const { headers, body: data } = getRolesTableData();

        if (data.length === 0) {
            doc.text("No hay roles para exportar.", 14, 30);
        } else {
            doc.autoTable({
                head: [headers], // autoTable espera un array de arrays para head
                body: data,
                startY: 28,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133] }, 
                styles: { fontSize: 8, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 15, halign: 'center' }, // ID
                    1: { cellWidth: 50 }, // Nombre del Rol
                    2: { cellWidth: 25, halign: 'center' }, // Guard
                    3: { cellWidth: 'auto' } // Permisos (dejar que autoTable ajuste)
                }
            });
        }

        doc.save(fileName);
        if (pdfModalListRoles) {
            pdfModalListRoles.hide();
        }
    }

    function exportRolesListToExcel(filename) {
        if (typeof XLSX === 'undefined') {
            alert("Error: Librería XLSX no cargada.");
            return;
        }
        let fileName = filename || 'listado_roles_{{ date('Ymd_His') }}.xlsx';
        if (!fileName.toLowerCase().endsWith('.xlsx')) {
            fileName += '.xlsx';
        }

        const { headers, body: dataRows } = getRolesTableData();
        if (dataRows.length === 0) {
            alert("No hay roles para exportar.");
            return;
        }

        const aoaData = [headers];
        dataRows.forEach(row => aoaData.push(row));

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoaData);

        // Estilos y anchos de columna
        const colWidths = headers.map((header, index) => {
            let maxWidth = header.length;
            dataRows.forEach(row => {
                const cellValue = row[index] ? String(row[index]) : '';
                maxWidth = Math.max(maxWidth, cellValue.length);
            });
            if (index === 0) return { wch: Math.max(5, maxWidth + 2) }; // ID
            if (index === 1) return { wch: Math.max(20, maxWidth + 2) }; // Nombre
            if (index === 2) return { wch: Math.max(10, maxWidth + 2) }; // Guard
            return { wch: Math.max(30, maxWidth + 2) }; // Permisos
        });
        ws['!cols'] = colWidths;

        // Estilo para cabeceras
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const cell_ref = XLSX.utils.encode_cell({ c: C, r: 0 }); // Primera fila (cabeceras)
            if (ws[cell_ref]) {
                if (!ws[cell_ref].s) ws[cell_ref].s = {};
                ws[cell_ref].s.font = { bold: true };
                ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } };
                ws[cell_ref].s.alignment = { horizontal: "center", vertical: "center" };

                // Centrar columna ID y Guard
                if (C === 0 || C === 2) { // ID o Guard
                     for (let R = range.s.r + 1; R <= range.e.r; ++R) {
                        const data_cell_ref = XLSX.utils.encode_cell({c: C, r: R});
                        if(ws[data_cell_ref]) {
                            if(!ws[data_cell_ref].s) ws[data_cell_ref].s = {};
                            ws[data_cell_ref].s.alignment = { horizontal: "center" };
                        }
                     }
                }
            }
        }
        // Formato para columna ID como número
        const idColIndex = headers.findIndex(h => h.toLowerCase() === 'id');
        if (idColIndex !== -1) {
            for (let R = range.s.r + 1; R <= range.e.r; ++R) {
                const cell_ref = XLSX.utils.encode_cell({ c: idColIndex, r: R });
                if (ws[cell_ref] && !isNaN(Number(ws[cell_ref].v))) {
                    ws[cell_ref].t = 'n'; // Asegurar que es tipo número
                    if (!ws[cell_ref].s) ws[cell_ref].s = {};
                    ws[cell_ref].s.numFmt = "0";
                }
            }
        }


        XLSX.utils.book_append_sheet(wb, ws, "Roles");
        XLSX.writeFile(wb, fileName);

        if (excelModalListRoles) {
            excelModalListRoles.hide();
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

    document.getElementById('exportExcelButtonListRolesTrigger')?.addEventListener('click', function () {
        if (excelModalListRoles && excelListRolesFilenameInput) {
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            excelListRolesFilenameInput.value = `listado_roles_${todayForFilename}.xlsx`;
            excelModalListRoles.show();
        }
    });

    document.getElementById('confirmExcelListRolesExportBtn')?.addEventListener('click', function () {
        const filename = excelListRolesFilenameInput ? excelListRolesFilenameInput.value.trim() : null;
        exportRolesListToExcel(filename);
    });

});
</script>
@endpush

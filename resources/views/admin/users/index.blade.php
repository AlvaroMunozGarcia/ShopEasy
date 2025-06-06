@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Gestión de Usuarios')

@section('page_header', 'Gestión de Usuarios')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- El @page_header ya muestra el título principal de la página. --}}

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- Este encabezado de tarjeta puede mantenerse --}}
                <h5 class="mb-0">Gestión de Usuarios</h5>
                <div>
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-person-plus-fill me-1"></i> Crear Nuevo Usuario
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-bordered table-hover mb-0 align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- La paginación de Laravel se elimina o comenta, DataTables la manejará --}}
            {{-- @if ($users->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery (necesario para DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
{{-- DataTables Bootstrap 5 JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

{{-- Librerías para exportación --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
 $(document).ready(function () {
        $('#usersTable').DataTable({
            language: {
                processing:"Procesando...",
                search:"Buscar",
                lengthMenu: "Mostrar <select class='form-select form-select-sm'>"+
                            "<option value='10'>10</option>"+
                            "<option value='25'>25</option>"+
                            "<option value='50'>50</option>"+
                            "<option value='100'>100</option>"+
                            "<option value='-1'>Todos</option>"+
                            "</select> registros",
                info:"Mostrando desde _START_ hasta _END_ de _TOTAL_ registros",
                infoEmpty:"Mostrando ningún registro",
                infoFiltered:"(filtrado de _MAX_ registros totales)",
                infoPostFix:"",
                loadingRecords:"Cargando registros...",
                zeroRecords:"No se encontraron registros",
                emptyTable:"No hay datos disponibles en la tabla",
                paginate:{
                    first:"Primero",
                    previous:"Anterior",
                    next:"Siguiente",
                    last:"Último"
                },
                aria:{
                    sortAscending:"Ordenar columna de manera ascendente",
                    sortDescending:"Ordenar columna de manera descendente"
                },

            }
        });
    });



    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportPdfButtonListTrigger').addEventListener('click', function () {
            pdfExport();
        });
    });
   function pdfExport() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const table = document.getElementById('usersTable');
    const headers = [];
    const rows = [];
    const ths = table.querySelectorAll('thead th');
    ths.forEach((th, index) => {
        if (index < ths.length - 1) { 
            headers.push(th.innerText.trim());
        }
    });
    table.querySelectorAll('tbody tr').forEach(tr => {
        const tds = tr.querySelectorAll('td');
        const row = [];

        tds.forEach((td, index) => {
            if (index < tds.length - 1) { 
                row.push(td.innerText.trim());
            }
        });

        if (row.length > 0) rows.push(row);
    });
    doc.autoTable({
        head: [headers],
        body: rows,
        startY: 20,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185] }
    });

    const filename = getCustomFilename('usuarios', 'pdf');
    if (filename) {
        doc.save(filename);
    }
}

 document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportExcelButtonList').addEventListener('click', function () {
            excelExport();
        });
    });
function excelExport() {
    const table = document.getElementById('usersTable');
    const wb = XLSX.utils.book_new();

    // Clonar la tabla y eliminar la última columna (Acciones) antes de convertir
    const tableClone = table.cloneNode(true);
    Array.from(tableClone.querySelectorAll('tr')).forEach(row => {
        row.deleteCell(-1); // Elimina la última celda de cada fila (th o td)
    });

    const ws = XLSX.utils.table_to_sheet(tableClone, { // Usar la tabla clonada
        sheet: "Usuarios",
        raw: true,
    });

    XLSX.utils.book_append_sheet(wb, ws, "Usuarios");
    const filename = getCustomFilename('usuarios', 'xlsx');
    if (filename) {
        XLSX.writeFile(wb, filename);
    }
}
</script>
@endpush

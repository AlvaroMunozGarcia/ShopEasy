@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Gestión de Proveedores')

@section('page_header', 'Gestión de Proveedores')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- El @page_header ya muestra el título principal de la página. --}}

        {{-- Mensajes Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Card Principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="mb-2 mb-md-0">Lista de Proveedores</h5>
                <div class="mt-2 mt-md-0">
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('providers.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-person-plus-fill me-1"></i> Añadir Proveedor
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="providersTable" class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>RUC</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($providers as $provider)
                                <tr>
                                    <td class="text-center">{{ $provider->id }}</td>
                                    <td>{{ $provider->name }}</td>
                                    <td>{{ $provider->email ?? 'N/A' }}</td>
                                    <td>{{ $provider->ruc_number ?? 'N/A' }}</td>
                                    <td>{{ $provider->phone ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('providers.show', $provider) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('providers.edit', $provider) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este proveedor?');">
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
                                    <td colspan="6" class="text-center text-muted">No hay proveedores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- La paginación de Laravel se elimina o comenta, DataTables la manejará --}}
            {{-- @if(method_exists($providers, 'links'))
                <div class="card-footer d-flex justify-content-center">
                    {{ $providers->links() }}
                </div>
            @endif --}}
        </div>
    </div>

    {{-- Modal for Excel Export Options --}}
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

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
        $('#providersTable').DataTable({
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
    const table = document.getElementById('providersTable');
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

    const filename = getCustomFilename('proveedores', 'pdf');
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
    const table = document.getElementById('providersTable');
    const wb = XLSX.utils.book_new();

    const ws = XLSX.utils.table_to_sheet(table, {
        sheet: "Proveedores",
        raw: true,
       
    });

    XLSX.utils.book_append_sheet(wb, ws, "Proveedores");

    const filename = getCustomFilename('proveedores', 'xlsx'); // Corregido de 'proveed_' a 'proveedores_'
    if (filename) {
    XLSX.writeFile(wb, filename);
}
}
</script>
@endpush

@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Listado de Ventas')

@section('page_header', 'Gestión de Ventas')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Ventas</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="mb-2 mb-md-0">Listado de Ventas</h5>
                <div class="mt-2 mt-md-0">
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('sales.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-plus-circle-fill me-1"></i> Registrar Nueva Venta
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="salesTable" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th class="text-end">Total (€)</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="text-center">{{ $sale->id }}</td>
                                    <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($sale->total, 2, ',', '.') }} €</td>
                                    <td>
                                        @switch($sale->status)
                                            @case('VALID')
                                                <span class="badge bg-success">Válida</span>
                                                @break
                                            @case('CANCELLED')
                                                <span class="badge bg-danger">Anulada</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning text-dark">{{ Str::title(str_replace('_', ' ', $sale->status)) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No se encontraron ventas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
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
  
 $(document).ready(function () {
        $('#salesTable').DataTable({
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
    const table = document.getElementById('salesTable');
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

    const filename = getCustomFilename('ventas', 'pdf');
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
    const table = document.getElementById('salesTable');
    const wb = XLSX.utils.book_new();
    const tableClone = table.cloneNode(true);
    Array.from(tableClone.querySelectorAll('tr')).forEach(row => {
        row.deleteCell(-1); 
    });

    const ws = XLSX.utils.table_to_sheet(tableClone, { 
        sheet: "Ventas",
        raw: true,
    });

    XLSX.utils.book_append_sheet(wb, ws, "Ventas");
    const filename = getCustomFilename('ventas', 'xlsx');
    if (filename) {
        XLSX.writeFile(wb, filename);
    }
}
</script>
@endpush

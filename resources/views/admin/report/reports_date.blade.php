@extends('layouts.admin') {{-- Usando tu layout personalizado --}}

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Reporte de Ventas por Fechas')

@section('page_header', 'Reporte de Ventas por Rango de Fechas')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Reportes</a></li> {{-- Asumiendo que 'home' es el inicio de reportes o dashboard general --}}
    <li class="breadcrumb-item active" aria-current="page">Por Rango de Fechas</li>
@endsection

@section('content')
    {{-- El H1 anterior se elimina ya que @page_header lo maneja --}}

    {{-- Formulario para seleccionar fechas --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Seleccionar Rango de Fechas</h5>
        </div>
        <form action="{{ route('report.results') }}" method="POST" id="reportDateForm">
            @csrf
            <div class="card-body">
                <div class="row align-items-end"> {{-- Alinea verticalmente al final --}}
                    <div class="col-md-5 mb-3 mb-md-0"> {{-- Añadido margen inferior en móvil --}}
                        <label for="fecha_ini" class="form-label">Fecha Inicial</label>
                        {{-- Mantenemos el valor si ya se hizo una búsqueda --}}
                        <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" value="{{ isset($fecha_ini) ? $fecha_ini->format('Y-m-d') : old('fecha_ini', \Carbon\Carbon::today('America/Lima')->format('Y-m-d')) }}" required>
                        @error('fecha_ini')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0"> {{-- Añadido margen inferior en móvil --}}
                        <label for="fecha_fin" class="form-label">Fecha Final</label>
                        {{-- Mantenemos el valor si ya se hizo una búsqueda --}}
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ isset($fecha_fin) ? $fecha_fin->format('Y-m-d') : old('fecha_fin', \Carbon\Carbon::today('America/Lima')->format('Y-m-d')) }}" required>
                         @error('fecha_fin')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Consultar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- Sección para mostrar resultados (solo si existen) --}}
    @isset($sales)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="card-title mb-2 mb-md-0">Resultados para el rango: {{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</h5>
                @if($sales->count())
                    <div class="mt-2 mt-md-0">
                        <button id="exportReportExcelButton" class="btn btn-success me-2">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                        <button id="exportReportPdfButtonTrigger" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($sales->count())
                <div class="table-responsive">
                    <table id="salesReportTable" class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th class="text-end">Total (€)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($sale->status == 'VALID')
                                        <span class="badge bg-success">Válida</span>
                                    @elseif ($sale->status == 'CANCELLED')
                                        <span class="badge bg-danger">Anulada</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $sale->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($sale->total, 2, ',', '.') }} €</td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total del Periodo:</strong></td>
                            <td class="text-end"><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @else
                <div class="alert alert-warning">
                    No se encontraron ventas para el rango de fechas seleccionado.
                </div>
            @endif
        </div>
    </div>
    @endisset
@endsection {{-- Cambiado de @stop a @endsection --}}

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
        $('#salesReportTable').DataTable({
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
        document.getElementById('exportReportPdfButtonTrigger').addEventListener('click', function () {
            pdfExport();
        });
    });
   function pdfExport() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const table = document.getElementById('salesReportTable');
        const headers = [];
        const rows = [];
        const ths = table.querySelectorAll('thead th');
        ths.forEach((th, index) => {
            // Excluir la última columna (Acciones)
            if (index < ths.length - 1) {
                headers.push(th.innerText.trim());
            }
        });
        table.querySelectorAll('tbody tr').forEach(tr => {
            const tds = tr.querySelectorAll('td');
            const row = [];

            tds.forEach((td, index) => {
                // Excluir la última columna (Acciones)
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

        const filename = getCustomFilename('reporte_ventas_fechas', 'pdf');
        if (filename) {
            doc.save(filename);
        }
}
document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportReportExcelButton').addEventListener('click', function () {
            excelExport();
        });
    });
function excelExport() {
    const table = document.getElementById('salesReportTable');
    const wb = XLSX.utils.book_new();

    // Clonar la tabla y eliminar la última columna (Acciones) antes de convertir
    const tableClone = table.cloneNode(true);
    Array.from(tableClone.querySelectorAll('tr')).forEach(row => {
        row.deleteCell(-1); // Elimina la última celda de cada fila (th o td)
    });

    const ws = XLSX.utils.table_to_sheet(tableClone, { // Usar la tabla clonada
        sheet: "Reportes de ventas",
        raw: true,
    });

    XLSX.utils.book_append_sheet(wb, ws, "Reportes de ventas");
    const filename = getCustomFilename('reporte_ventas_fechas', 'xlsx');
    if (filename) {
        XLSX.writeFile(wb, filename);
    }
}

</script>
@endpush

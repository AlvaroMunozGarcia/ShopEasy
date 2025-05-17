@extends('layouts.admin') {{-- Usando tu layout personalizado --}}

@section('title', 'Reporte de Ventas por Fechas')

{{-- El título ahora va dentro de la sección 'content' --}}
@section('content')
    <h1>Reporte de Ventas por Rango de Fechas</h1>

    {{-- Formulario para seleccionar fechas --}}
    <div class="card card-primary mb-4"> {{-- Añadido margen inferior --}}
        <div class="card-header">
            <h3 class="card-title">Seleccionar Rango</h3>
        </div>
        <form action="{{ route('report.results') }}" method="POST">
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Consultar</button> {{-- w-100 para ancho completo --}}
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Sección para mostrar resultados (solo si existen) --}}
    @isset($sales)
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Resultados para el rango: {{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</h3>
                @if($sales->count())
                    <div>
                        <button id="exportReportCsvButton" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bi bi-filetype-csv me-1"></i> CSV
                        </button>
                        <button id="exportReportExcelButton" class="btn btn-sm btn-outline-success me-2">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                        <button id="exportReportPdfButton" class="btn btn-sm btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</button>
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
                            <th class="text-right">Total (S/)</th>
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
                                    {{-- Usando clases de Bootstrap 5 para badges --}}
                                    @if ($sale->status == 'VALID')
                                        <span class="badge bg-success">Válida</span>
                                    @elseif ($sale->status == 'CANCELLED')
                                        <span class="badge bg-danger">Anulada</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $sale->status }}</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($sale->total, 2) }}</td>
                                <td>
                                    {{-- Usando iconos Bootstrap --}}
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                    {{-- El PDF individual se genera desde la vista show de sales --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total del Periodo:</strong></td>
                            <td class="text-right"><strong>S/ {{ number_format($total, 2) }}</strong></td>
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

{{-- Modal for CSV Export Options --}}
<div class="modal fade" id="csvExportModal" tabindex="-1" aria-labelledby="csvExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="csvExportModalLabel">Exportar a CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="csvFilenameInput" class="form-label">Nombre del archivo:</label>
                    <input type="text" class="form-control" id="csvFilenameInput" placeholder="nombre_archivo.csv">
                </div>
                <div class="mb-3">
                    <label for="csvSeparatorSelect" class="form-label">Separador:</label>
                    <select id="csvSeparatorSelect" class="form-select">
                        <option value=";" selected>Punto y coma (;)</option>
                        <option value=",">Coma (,)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmCsvExportBtn">Confirmar y Exportar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Excel Export Options --}}
<div class="modal fade" id="excelExportModal" tabindex="-1" aria-labelledby="excelExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excelExportModalLabel">Exportar a Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="excelFilenameInput" class="form-label">Nombre del archivo:</label>
                    <input type="text" class="form-control" id="excelFilenameInput" placeholder="nombre_archivo.xlsx">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmExcelExportBtn">Confirmar y Exportar</button>
            </div>
        </div>
    </div>
</div>
@endsection {{-- Cambiado de @stop a @endsection --}}

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportReportPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const fechaIni = "{{ isset($fecha_ini) ? $fecha_ini->format('d/m/Y') : '' }}";
            const fechaFin = "{{ isset($fecha_fin) ? $fecha_fin->format('d/m/Y') : '' }}";
            const title = `Reporte de Ventas (${fechaIni} - ${fechaFin})`;

            doc.setFontSize(18);
            doc.text(title, 14, 22);
            doc.autoTable({
                html: '#salesReportTable',
                startY: 30,
            });
            doc.save(`reporte_ventas_${fechaIni.replace(/\//g, '-')}_${fechaFin.replace(/\//g, '-')}.pdf`);
        });
    }

    // --- Common Export Functions ---
    function escapeCsvCell(cellData) {
        if (cellData == null) return '';
        let dataString = String(cellData).replace(/"/g, '""');
        if (dataString.search(/("|,|;|\n)/g) >= 0) dataString = '"' + dataString + '"';
        return dataString;
    }

    function exportTableToCSV(tableId, filename = 'export.csv', separator = ',') {
        const table = document.getElementById(tableId);
        if (!table) { alert(`Error: Tabla con id "${tableId}" no encontrada.`); return; }
        let csv = ['\uFEFF']; // BOM for UTF-8
        const processRows = (rows, cellType) => {
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                const rowData = [];
                const cols = row.querySelectorAll(cellType);
                if (cols.length === 1 && cols[0].getAttribute('colspan')) {
                    let headerColCount = table.querySelector('thead tr th') ? table.querySelector('thead tr').querySelectorAll('th').length : 0;
                    if (parseInt(cols[0].getAttribute('colspan')) >= headerColCount) return;
                }
                cols.forEach(col => rowData.push(escapeCsvCell(col.innerText.trim())));
                if (rowData.length > 0 && rowData.some(cell => cell !== '""' && cell !== '')) csv.push(rowData.join(separator));
            });
        };
        processRows(table.querySelectorAll('thead tr'), 'th');
        processRows(table.querySelectorAll('tbody tr'), 'td');
        processRows(table.querySelectorAll('tfoot tr'), 'td, th');

        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    }

    function parseNumericValue(text) {
        if (text === null || text === undefined) return text;
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, '');
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) { // Format 1.234,56
                cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            } else { // Format 1,234.56
                cleanText = cleanText.replace(/,/g, '');
            }
        } else if (cleanText.includes(',')) { // Only comma, assume it's decimal like "123,45" or part of "1,234"
             // For "1,234" (integer) or "123,45" (decimal with comma)
            if (cleanText.match(/,\d{1,2}$/)) { // Ends with ,XX or ,X -> likely decimal
                 cleanText = cleanText.replace(/,/g, '.');
            } else { // Assume it's a thousands separator
                 cleanText = cleanText.replace(/,/g, '');
            }
        }
        const num = parseFloat(cleanText);
        return isNaN(num) ? text : num;
    }

    function processTableForExcel(tableElement, workbook, sheetName) {
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return false; }
        let aoaData = [], htmlRowsInfo = [];
        const processRowCollection = (rows, cellSelector, type) => {
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                const rowData = [], cols = row.querySelectorAll(cellSelector);
                if (cols.length === 1 && cols[0].getAttribute('colspan')) {
                    let headerColCount = tableElement.querySelector('thead tr th') ? tableElement.querySelector('thead tr').querySelectorAll('th').length : 0;
                    if (parseInt(cols[0].getAttribute('colspan')) >= headerColCount) return;
                }
                cols.forEach(col => {
                    let cellText = col.innerText.trim();
                    const isNumeric = col.classList.contains('text-right') || (col.style.textAlign === 'right') || /^\S*\s*[\d,.-]+\d$/.test(cellText.replace(/[S\/$.€]/g, ''));
                    rowData.push(isNumeric ? parseNumericValue(cellText) : cellText);
                });
                if (rowData.length > 0) { aoaData.push(rowData); htmlRowsInfo.push({ type, element: row }); }
            });
        };
        processRowCollection(tableElement.querySelectorAll('thead tr'), 'th', 'header');
        processRowCollection(tableElement.querySelectorAll('tbody tr'), 'td', 'body');
        processRowCollection(tableElement.querySelectorAll('tfoot tr'), 'td, th', 'footer');

        const ws = XLSX.utils.aoa_to_sheet(aoaData);
        let currentWsRow = 0;
        htmlRowsInfo.forEach(rowInfo => {
            const htmlCells = rowInfo.element.querySelectorAll(rowInfo.type === 'header' ? 'th' : 'td, th');
            htmlCells.forEach((htmlCell, colIndex) => {
                const cell_ref = XLSX.utils.encode_cell({ c: colIndex, r: currentWsRow });
                if (!ws[cell_ref]) return;
                if (!ws[cell_ref].s) ws[cell_ref].s = {};
                let s = ws[cell_ref].s;
                if (rowInfo.type === 'header' || rowInfo.type === 'footer' || htmlCell.querySelector('strong')) {
                    if (!s.font) s.font = {}; s.font.bold = true;
                }
                if (htmlCell.classList.contains('text-right') || htmlCell.style.textAlign === 'right' || (ws[cell_ref].t === 'n' && colIndex > 0)) {
                    if (!s.alignment) s.alignment = {}; s.alignment.horizontal = "right";
                }
                if (htmlCell.classList.contains('text-center') || htmlCell.style.textAlign === 'center') {
                    if (!s.alignment) s.alignment = {}; s.alignment.horizontal = "center";
                }
                if (rowInfo.type === 'header' && rowInfo.element.parentElement.classList.contains('table-dark')) {
                    if (!s.fill) s.fill = {}; s.fill.patternType = "solid"; s.fill.fgColor = { rgb: "FF212529" };
                    if (!s.font) s.font = {}; s.font.color = { rgb: "FFFFFFFF" };
                }
                if (ws[cell_ref].t === 'n') s.numFmt = "0.00";
            });
            currentWsRow++;
        });
        const colWidths = [];
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let maxLen = 0;
            for (let R = range.s.r; R <= range.e.r; ++R) {
                const cell_ref = XLSX.utils.encode_cell({ c: C, r: R });
                if (ws[cell_ref]) {
                    let cellText = ws[cell_ref].v !== null && ws[cell_ref].v !== undefined ? String(ws[cell_ref].v) : "";
                    if (ws[cell_ref].t === 'n' && ws[cell_ref].s && ws[cell_ref].s.numFmt) cellText = Number(ws[cell_ref].v).toFixed(2);
                    maxLen = Math.max(maxLen, cellText.length);
                }
            }
            colWidths[C] = { wch: Math.max(10, maxLen + 2) };
        }
        ws['!cols'] = colWidths;
        XLSX.utils.book_append_sheet(workbook, ws, sheetName);
        return true;
    }

    function exportSingleTableToExcel(tableId, filename = 'export.xlsx', sheetName = 'Sheet1') {
        const tableElement = document.getElementById(tableId);
        if (!tableElement) { alert(`Error: Tabla con id "${tableId}" no encontrada.`); return; }
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }
        const wb = XLSX.utils.book_new();
        if (processTableForExcel(tableElement, wb, sheetName)) {
            XLSX.writeFile(wb, filename);
        }
    }

    // --- Modal Instances & Export Button Event Listeners ---
    const csvModalEl = document.getElementById('csvExportModal');
    const csvModal = csvModalEl ? new bootstrap.Modal(csvModalEl) : null;
    const excelModalEl = document.getElementById('excelExportModal');
    const excelModal = excelModalEl ? new bootstrap.Modal(excelModalEl) : null;

    const csvFilenameInput = document.getElementById('csvFilenameInput');
    const csvSeparatorSelect = document.getElementById('csvSeparatorSelect');
    const excelFilenameInput = document.getElementById('excelFilenameInput');

    const tableIdToExport = 'salesReportTable'; // Specific to this view
    const baseFilename = "reporte_ventas_rango_{{ isset($fecha_ini) ? $fecha_ini->format('Ymd') : '' }}_{{ isset($fecha_fin) ? $fecha_fin->format('Ymd') : '' }}";

    document.getElementById('exportReportCsvButton')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            csvModal.show();
        }
    });

    document.getElementById('exportReportExcelButton')?.addEventListener('click', () => {
        if (excelModal && excelFilenameInput) {
            excelFilenameInput.value = `${baseFilename}.xlsx`;
            excelModal.show();
        }
    });

    document.getElementById('confirmCsvExportBtn')?.addEventListener('click', () => {
        if (csvFilenameInput && csvSeparatorSelect) {
            const filename = csvFilenameInput.value.trim() || `${baseFilename}.csv`;
            const separator = csvSeparatorSelect.value;
            exportTableToCSV(tableIdToExport, filename, separator);
            if(csvModal) csvModal.hide();
        }
    });

    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportSingleTableToExcel(tableIdToExport, filename, 'Reporte Ventas');
            if(excelModal) excelModal.hide();
        }
    });

});
</script>
@endpush

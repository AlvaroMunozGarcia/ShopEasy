@extends('layouts.admin')

@section('title', 'Listado de Ventas')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Mensajes Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Tarjeta de Ventas --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Ventas</h5>
                <div>
                    <button id="exportCsvButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-filetype-csv me-1"></i> CSV
                    </button>
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonList" class="btn btn-info btn-sm fw-semibold me-2"> {{-- Original PDF button --}}
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
                                <th>Total</th>
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
                                    <td>S/ {{ number_format($sale->total, 2) }}</td>
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

                                        @if($sale->status == 'VALID')
                                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres anular esta venta?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Anular Venta">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                        {{-- El PDF individual se genera ahora desde la vista show --}}
                                        {{-- <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Descargar PDF">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a> --}}
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

            {{-- Footer de paginación opcional --}}
            @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator && $sales->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>

    </div>

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
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportPdfButtonList');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Listado de Ventas", 14, 22);
            doc.autoTable({
                html: '#salesTable',
                startY: 30,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
            });
            doc.save('listado_ventas.pdf');
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
        // This table (salesTable) does not have a tfoot in the provided HTML
        // processRows(table.querySelectorAll('tfoot tr'), 'td, th');

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
        } else if (cleanText.includes(',')) { // Only comma
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
        // This table (salesTable) does not have a tfoot in the provided HTML
        // processRowCollection(tableElement.querySelectorAll('tfoot tr'), 'td, th', 'footer');

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

    const tableIdToExport = 'salesTable'; // Specific to this view
    const date = new Date();
    const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
    const baseFilename = `listado_ventas_${todayForFilename}`;

    document.getElementById('exportCsvButtonList')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            csvModal.show();
        }
    });

    document.getElementById('exportExcelButtonList')?.addEventListener('click', () => {
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
            exportSingleTableToExcel(tableIdToExport, filename, 'Listado Ventas');
            if(excelModal) excelModal.hide();
        }
    });

});
</script>
@endpush

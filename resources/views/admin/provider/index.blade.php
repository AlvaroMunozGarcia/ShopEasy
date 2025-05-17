@extends('layouts.admin')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

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
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Proveedores</h5>
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

            {{-- Paginación (descomenta si se utiliza) --}}
            @if(method_exists($providers, 'links'))
                <div class="card-footer d-flex justify-content-center">
                    {{ $providers->links() }}
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
            doc.text("Listado de Proveedores", 14, 22);
            doc.autoTable({
                html: '#providersTable',
                startY: 30,
            });
            doc.save('listado_proveedores.pdf');
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
                cols.forEach(col => {
                    if (cellType === 'td' && col.querySelector('form, a.btn')) {
                        // Skip action column for data rows
                    } else {
                        rowData.push(escapeCsvCell(col.innerText.trim()));
                    }
                });
                if (cellType === 'th') {
                    const headerData = [];
                    cols.forEach(col => headerData.push(escapeCsvCell(col.innerText.trim())));
                    if (headerData.length > 0 && headerData[headerData.length - 1].toLowerCase() === 'acciones') {
                        headerData.pop();
                    }
                    if (headerData.length > 0) csv.push(headerData.join(separator));
                } else if (rowData.length > 0 && rowData.some(cell => cell !== '""' && cell !== '')) {
                     csv.push(rowData.join(separator));
                }
            });
        };
        processRows(table.querySelectorAll('thead tr'), 'th');
        processRows(table.querySelectorAll('tbody tr'), 'td');

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
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) {
                cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            } else {
                cleanText = cleanText.replace(/,/g, '');
            }
        } else if (cleanText.includes(',')) {
            if (cleanText.match(/,\d{1,2}$/)) {
                 cleanText = cleanText.replace(/,/g, '.');
            } else {
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
                cols.forEach((col) => {
                    if (cellSelector === 'td' && col.querySelector('form, a.btn')) {
                        // Skip action column for data rows
                    } else if (cellSelector === 'th' && col.innerText.trim().toLowerCase() === 'acciones') {
                        // Skip "Acciones" header
                    } else {
                        let cellText = col.innerText.trim();
                        // For providers, only ID might be numeric, others are generally text (RUC, Phone)
                        const isNumeric = (cellSelector === 'th' && col.innerText.trim().toLowerCase() === 'id') ||
                                          (cellSelector === 'td' && col.cellIndex === 0); // ID column
                        rowData.push(isNumeric ? parseNumericValue(cellText) : cellText);
                    }
                });
                if (rowData.length > 0) { aoaData.push(rowData); htmlRowsInfo.push({ type, element: row }); }
            });
        };
        processRowCollection(tableElement.querySelectorAll('thead tr'), 'th', 'header');
        processRowCollection(tableElement.querySelectorAll('tbody tr'), 'td', 'body');

        const ws = XLSX.utils.aoa_to_sheet(aoaData);
        let currentWsRow = 0;
        htmlRowsInfo.forEach(rowInfo => {
            const htmlCells = rowInfo.element.querySelectorAll(rowInfo.type === 'header' ? 'th' : 'td');
            let excelColIndex = 0;
            htmlCells.forEach((htmlCell) => {
                if (rowInfo.type === 'header' && htmlCell.innerText.trim().toLowerCase() === 'acciones') return;
                if (rowInfo.type === 'body' && htmlCell.querySelector('form, a.btn')) return;

                const cell_ref = XLSX.utils.encode_cell({ c: excelColIndex, r: currentWsRow });
                if (!ws[cell_ref]) return;
                if (!ws[cell_ref].s) ws[cell_ref].s = {};
                let s = ws[cell_ref].s;
                if (rowInfo.type === 'header' || htmlCell.querySelector('strong')) {
                    if (!s.font) s.font = {}; s.font.bold = true;
                }
                if (htmlCell.classList.contains('text-right') || htmlCell.style.textAlign === 'right' || (ws[cell_ref].t === 'n' && excelColIndex > 0)) {
                    if (!s.alignment) s.alignment = {}; s.alignment.horizontal = "right";
                }
                if (htmlCell.classList.contains('text-center') || htmlCell.style.textAlign === 'center') {
                    if (!s.alignment) s.alignment = {}; s.alignment.horizontal = "center";
                }
                if (rowInfo.type === 'header' && rowInfo.element.parentElement.classList.contains('table-dark')) {
                    if (!s.fill) s.fill = {}; s.fill.patternType = "solid"; s.fill.fgColor = { rgb: "FF212529" };
                    if (!s.font) s.font = {}; s.font.color = { rgb: "FFFFFFFF" };
                }
                if (ws[cell_ref].t === 'n') s.numFmt = "0"; // ID is integer
                excelColIndex++;
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
                    if (ws[cell_ref].t === 'n' && ws[cell_ref].s && ws[cell_ref].s.numFmt) cellText = Number(ws[cell_ref].v).toString();
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

    const tableIdToExport = 'providersTable';
    const date = new Date();
    const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
    const baseFilename = `listado_proveedores_${todayForFilename}`;

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

    document.getElementById('confirmCsvExportBtn')?.addEventListener('click', ()¡ => {
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
            exportSingleTableToExcel(tableIdToExport, filename, 'Listado Proveedores');
            if(excelModal) excelModal.hide();
        }
    });

});
</script>
@endpush

@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Gestión de Clientes')

@section('page_header', 'Gestión de Clientes')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Clientes</li>
@endsection


@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- El @page_header ya muestra el título principal de la página. --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- Este encabezado de tarjeta puede mantenerse --}}
                <h5 class="mb-0">Lista de Clientes</h5>
                <div>
                    <button id="exportCsvButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-filetype-csv me-1"></i> CSV
                    </button>
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('clients.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-person-plus me-1"></i> Añadir Cliente
                    </a>
                </div>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="clientsTable" class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>RUC</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr>
                                    <td class="text-center">{{ $client->id }}</td>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->dni }}</td>
                                    <td>{{ $client->ruc ?? 'N/A' }}</td>
                                    <td>{{ $client->email ?? 'N/A' }}</td>
                                    <td>{{ $client->phone ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
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
                                    <td colspan="7" class="text-center text-muted">No se encontraron clientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- La paginación de Laravel se elimina o comenta, DataTables la manejará --}}
            {{-- @if(method_exists($clients, 'links'))
                <div class="card-footer d-flex justify-content-center">
                    {{ $clients->links() }}
                </div>
            @endif --}}
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

    {{-- Modal for PDF Export Options --}}
    <div class="modal fade" id="pdfExportModal" tabindex="-1" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfExportModalLabel">Exportar a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfFilenameInput" placeholder="nombre_archivo.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfExportBtn">Confirmar y Exportar</button>
                </div>
            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function exportListToPdf(filename = 'listado_clientes.pdf') {
        const { jsPDF } = window.jspdf;
        try {
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Listado de Clientes", 14, 22);

            const table = document.getElementById('clientsTable');
            const head = [];
            const body = [];
            let actionsColumnIndex = -1;

            // Process header
            const headerRow = table.querySelector('thead tr');
            if (headerRow) {
                const ths = Array.from(headerRow.querySelectorAll('th'));
                const currentHead = [];
                ths.forEach((th, index) => {
                    if (th.innerText.trim().toLowerCase() === 'acciones') {
                        actionsColumnIndex = index;
                    } else {
                        currentHead.push(th.innerText.trim());
                    }
                });
                head.push(currentHead);
            }

            // Process body (visible rows after DataTables filtering/pagination)
            $('#clientsTable').DataTable().rows({ search: 'applied' }).every(function() {
                const rowNode = this.node();
                const rowData = [];
                $(rowNode).find('td').each(function(index) {
                    if (index !== actionsColumnIndex) {
                        rowData.push($(this).text().trim());
                    }
                });
                if (rowData.length > 0) body.push(rowData);
            });

            doc.autoTable({
                head: head,
                body: body,
                startY: 30,
            });
            doc.save(filename);
        } catch (error) {
            console.error("Error al generar PDF:", error);
            alert("Error al generar PDF. Verifique la consola para más detalles.");
        }
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
                    // Exclude action buttons column from CSV/Excel
                    if (cellType === 'td' && col.querySelector('form, a.btn')) { // Heuristic to detect action column
                        // Skip this column for data rows
                    } else {
                        rowData.push(escapeCsvCell(col.innerText.trim()));
                    }
                });
                 // For header, always include all columns
                if (cellType === 'th') {
                    const headerData = [];
                    cols.forEach(col => headerData.push(escapeCsvCell(col.innerText.trim())));
                    // Remove last header if it's "Acciones"
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
                cols.forEach((col, index) => {
                    // Exclude action buttons column from Excel
                    if (cellSelector === 'td' && col.querySelector('form, a.btn')) {
                        // Skip this column for data rows
                    } else if (cellSelector === 'th' && col.innerText.trim().toLowerCase() === 'acciones') {
                        // Skip "Acciones" header
                    }
                    else {
                        let cellText = col.innerText.trim();
                        const isNumeric = col.classList.contains('text-right') || (col.style.textAlign === 'right') || /^\S*\s*[\d,.-]+\d$/.test(cellText.replace(/[S\/$.€]/g, ''));
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
                if (rowInfo.type === 'header' && htmlCell.innerText.trim().toLowerCase() === 'acciones') return; // Skip actions header
                if (rowInfo.type === 'body' && htmlCell.querySelector('form, a.btn')) return; // Skip actions cell in body

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
                if (ws[cell_ref].t === 'n') s.numFmt = "0.00"; // Or a more specific format if needed
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
    const pdfModalEl = document.getElementById('pdfExportModal');
    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;

    const csvFilenameInput = document.getElementById('csvFilenameInput');
    const csvSeparatorSelect = document.getElementById('csvSeparatorSelect');
    const excelFilenameInput = document.getElementById('excelFilenameInput');
    const pdfFilenameInput = document.getElementById('pdfFilenameInput');

    const tableIdToExport = 'clientsTable';
    const date = new Date();
    const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
    const baseFilename = `listado_clientes_${todayForFilename}`;

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

    document.getElementById('exportPdfButtonListTrigger')?.addEventListener('click', () => {
        if (pdfModal && pdfFilenameInput) {
            pdfFilenameInput.value = `${baseFilename}.pdf`;
            pdfModal.show();
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
            exportSingleTableToExcel(tableIdToExport, filename, 'Listado Clientes');
            if(excelModal) excelModal.hide();
        }
    });

    document.getElementById('confirmPdfExportBtn')?.addEventListener('click', () => {
        if (pdfFilenameInput) {
            const filename = pdfFilenameInput.value.trim() || `${baseFilename}.pdf`;
            exportListToPdf(filename);
            if(pdfModal) pdfModal.hide();
        }
    });

    // Inicializar DataTables
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $('#clientsTable').length > 0) {
        try {
            $('#clientsTable').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "searchable": false, "targets": -1 } // La última columna (Acciones) no se ordena ni se busca
                ],
                // dom: 'lBfrtip', // Si quisieras botones de DataTables (necesitarías más librerías: buttons.html5.min.js, etc.)
                // Para la estructura estándar de BS5:
                // dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                //      "<'row'<'col-sm-12'tr>>" +
                //      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
            console.log("DataTables inicializado para #clientsTable");
        } catch (e) {
            console.error("Error inicializando DataTables para #clientsTable:", e);
        }
    }
});
</script>
@endpush

@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

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
                    <h5 class="modal-title" id="pdfExportModalLabel">Exportar Listado a PDF</h5>
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

{{-- Librerías para exportación --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableIdToExport = 'providersTable';
    let dataTableInstance; // Para acceder a la instancia de DataTables

    // Inicializar DataTables para la tabla de proveedores
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $(`#${tableIdToExport}`).length > 0) {
        try {
            dataTableInstance = $(`#${tableIdToExport}`).DataTable({
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
            console.log(`DataTables inicializado para #${tableIdToExport}`);
        } catch (e) {
            console.error(`Error inicializando DataTables para #${tableIdToExport}:`, e);
        }
    }

    function exportListToPdf(filename = 'listado_proveedores.pdf') {
        if (!dataTableInstance) {
            alert("La tabla de datos no está inicializada.");
            return;
        }
        try {
            const { jsPDF } = window.jspdf;
            if (!jsPDF) { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const doc = new jsPDF();
            doc.setFontSize(18);
            doc.text("Listado de Proveedores", 14, 22);

            const head = [];
            const body = [];
            
            // Obtener cabeceras de DataTables (respetando visibilidad y orden)
            const headerCells = dataTableInstance.table().header().querySelectorAll('th');
            const currentHead = [];
            let actionsColumnIndex = -1;
            headerCells.forEach((th, index) => {
                if (th.offsetParent !== null) { // Solo columnas visibles
                    if (th.innerText.trim().toLowerCase() === 'acciones') {
                        actionsColumnIndex = index; // Guardamos el índice original de la columna de acciones
                    } else {
                        currentHead.push(th.innerText.trim());
                    }
                }
            });
            head.push(currentHead);
            
            // Obtener datos de DataTables (respetando filtro y orden)
            dataTableInstance.rows({ search: 'applied' }).every(function() {
                const rowNode = this.node();
                const rowData = [];
                $(rowNode).find('td').each(function(index) {
                     // Comparamos el índice actual de la celda con el índice original de la columna de acciones
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
            console.error("Error al generar PDF del listado:", error);
            alert("Error al generar PDF del listado. Verifique la consola para más detalles.");
        }
    }
    
    // --- Funciones Comunes de Exportación ---
    function escapeCsvCell(cellData) {
        if (cellData == null) return '';
        let dataString = String(cellData).replace(/"/g, '""');
        if (dataString.search(/("|,|;|\n)/g) >= 0) dataString = '"' + dataString + '"';
        return dataString;
    }

    function getTableDataForExport(tableInstance, excludeActions = true) {
        const headers = [];
        const body = [];
        let actionsColIdx = -1;

        // Cabeceras
        const headerCells = tableInstance.table().header().querySelectorAll('th');
        headerCells.forEach((th, idx) => {
            if (th.offsetParent !== null) { // Solo columnas visibles
                if (excludeActions && th.innerText.trim().toLowerCase() === 'acciones') {
                    actionsColIdx = idx;
                } else {
                    headers.push(th.innerText.trim());
                }
            }
        });
        
        // Cuerpo
        tableInstance.rows({ search: 'applied' }).every(function() {
            const rowNode = this.node();
            const rowData = [];
            $(rowNode).find('td').each(function(idx) {
                if (idx !== actionsColIdx) {
                    rowData.push($(this).text().trim());
                }
            });
            if (rowData.length > 0) body.push(rowData);
        });
        return { headers, body };
    }


    function exportTableToCSV(filename = 'export.csv', separator = ';') {
        if (!dataTableInstance) { alert("La tabla de datos no está inicializada."); return; }
        
        const { headers, body } = getTableDataForExport(dataTableInstance);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        let csv = ['\uFEFF']; // BOM for UTF-8
        csv.push(headers.map(header => escapeCsvCell(header)).join(separator));
        body.forEach(rowArray => {
            csv.push(rowArray.map(cell => escapeCsvCell(cell)).join(separator));
        });

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
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, ''); // Quitar símbolos de moneda
        // Detectar formato: si hay coma y punto, el último es el decimal
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) { // Formato europeo: 1.234,56
                cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            } else { // Formato americano: 1,234.56
                cleanText = cleanText.replace(/,/g, '');
            }
        } else if (cleanText.includes(',')) { // Solo coma, podría ser decimal europeo
             // Asumir que si la coma está seguida de 1 o 2 dígitos es decimal
            if (cleanText.match(/,\d{1,2}$/)) {
                 cleanText = cleanText.replace(/,/g, '.');
            } else { // Sino, es separador de miles
                 cleanText = cleanText.replace(/,/g, '');
            }
        }
        const num = parseFloat(cleanText);
        return isNaN(num) ? text : num; // Devolver el texto original si no es un número válido
    }

    function exportTableToExcel(filename = 'export.xlsx', sheetName = 'Datos') {
        if (!dataTableInstance) { alert("La tabla de datos no está inicializada."); return; }
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }

        const { headers, body: dataRows } = getTableDataForExport(dataTableInstance);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        const aoaData = [headers]; // Array of Arrays
        dataRows.forEach(rowArray => {
            const processedRow = rowArray.map((cell, colIndex) => {
                // Intentar convertir a número si la cabecera es 'ID' o si parece numérico
                // Para proveedores, solo ID es claramente numérico. RUC y Teléfono son cadenas.
                if (headers[colIndex] && headers[colIndex].toLowerCase() === 'id') {
                    return parseNumericValue(cell);
                }
                return cell; // Dejar como texto por defecto
            });
            aoaData.push(processedRow);
        });
        
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoaData);

        // Aplicar estilos básicos y autoajuste de columnas
        const colWidths = headers.map(header => ({ wch: Math.max(10, header.length + 2) }));
        aoaData.forEach(row => {
            row.forEach((cell, colIndex) => {
                const len = cell ? String(cell).length : 0;
                if (colWidths[colIndex]) {
                    colWidths[colIndex].wch = Math.max(colWidths[colIndex].wch, len + 2);
                }
            });
        });
        ws['!cols'] = colWidths;

        // Estilo para cabeceras
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const cell_ref = XLSX.utils.encode_cell({ c: C, r: 0 }); // Primera fila (cabeceras)
            if (ws[cell_ref]) {
                if (!ws[cell_ref].s) ws[cell_ref].s = {};
                ws[cell_ref].s.font = { bold: true };
                ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } }; // Gris claro
                ws[cell_ref].s.alignment = { horizontal: "center" };
            }
        }
        // Formato para columna ID como número
        const idColIndex = headers.findIndex(h => h.toLowerCase() === 'id');
        if (idColIndex !== -1) {
            for (let R = range.s.r + 1; R <= range.e.r; ++R) { // Empezar desde la segunda fila (datos)
                const cell_ref = XLSX.utils.encode_cell({ c: idColIndex, r: R });
                if (ws[cell_ref] && typeof ws[cell_ref].v === 'number') {
                    if (!ws[cell_ref].s) ws[cell_ref].s = {};
                    ws[cell_ref].s.numFmt = "0"; // Formato de número entero
                }
            }
        }

        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, filename);
    }

    // --- Modales y Event Listeners para botones de exportación ---
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

    const date = new Date();
    const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
    const baseFilename = `listado_proveedores_${todayForFilename}`;

    document.getElementById('exportCsvButtonList')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            if (csvSeparatorSelect) csvSeparatorSelect.value = ';'; // Default
            csvModal.show();
        } else {
            exportTableToCSV(`${baseFilename}.csv`); // Exportar directamente si no hay modal
        }
    });

    document.getElementById('exportExcelButtonList')?.addEventListener('click', () => {
        if (excelModal && excelFilenameInput) {
            excelFilenameInput.value = `${baseFilename}.xlsx`;
            excelModal.show();
        } else {
            exportTableToExcel(`${baseFilename}.xlsx`, 'Proveedores'); // Exportar directamente
        }
    });

    document.getElementById('exportPdfButtonListTrigger')?.addEventListener('click', () => {
        if (pdfModal && pdfFilenameInput) {
            pdfFilenameInput.value = `${baseFilename}.pdf`;
            pdfModal.show();
        } else {
            exportListToPdf(`${baseFilename}.pdf`); // Exportar directamente si no hay modal
        }
    });

    document.getElementById('confirmCsvExportBtn')?.addEventListener('click', () => {
        if (csvFilenameInput && csvSeparatorSelect) {
            const filename = csvFilenameInput.value.trim() || `${baseFilename}.csv`;
            const separator = csvSeparatorSelect.value;
            exportTableToCSV(filename, separator);
            if(csvModal) csvModal.hide();
        }
    });

    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportTableToExcel(filename, 'Proveedores');
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

});
</script>
@endpush

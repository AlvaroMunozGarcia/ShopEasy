@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Listado de Compras')

@section('page_header', 'Gestión de Compras')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Compras</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- El @page_header ya muestra el título principal de la página. --}}

        {{-- Mensajes flash --}}
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

        {{-- Tarjeta principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- Este encabezado de tarjeta puede mantenerse --}}
                <h5 class="mb-0">Compras Registradas</h5>
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
                    <a href="{{ route('purchases.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Crear Nueva Compra
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchasesTable" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td class="text-center">{{ $purchase->id }}</td>
                                    <td>{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $purchase->provider->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($purchase->total, 2) }}</td>
                                    <td>
                                        @switch($purchase->status)
                                            @case('VALID')
                                                <span class="badge bg-success">Válida</span>
                                                @break
                                            @case('CANCELLED')
                                                <span class="badge bg-danger">Cancelada</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning text-dark">{{ $purchase->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        {{-- El PDF individual se genera ahora desde la vista show --}}
                                        {{-- <a href="{{ route('purchases.print', $purchase) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Descargar PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No se encontraron compras.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer para paginación si se requiere --}}
            {{-- La paginación de Laravel se elimina o comenta, DataTables la manejará --}}
            {{-- @if ($purchases instanceof \Illuminate\Pagination\LengthAwarePaginator && $purchases->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $purchases->links() }}
                </div>
            @endif --}}
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
    const tableIdToExport = 'purchasesTable';
    let dataTableInstance; // Para acceder a la instancia de DataTables

    // Inicializar DataTables
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
                    { "orderable": false, "searchable": false, "targets": -1 }, // Última columna (Acciones)
                    { "type": "num", "targets": 0 }, // ID es numérico
                    { "type": "date-eu", "targets": 1 }, // Fecha (formato dd/mm/yyyy)
                    { "type": "num-fmt", "targets": 3 } // Total (para ordenación con formato de moneda)
                ]
            });
            console.log(`DataTables inicializado para #${tableIdToExport}`);
        } catch (e) {
            console.error(`Error inicializando DataTables para #${tableIdToExport}:`, e);
        }
    }

    // --- Exportación a PDF ---
    function exportListToPdf(filename = 'listado_compras.pdf') {
        if (!dataTableInstance) {
            alert("La tabla de datos no está inicializada.");
            return;
        }
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(18);
            doc.text("Listado de Compras", 14, 22);

            const { headers, body } = getTableDataForExport(dataTableInstance);
            if (headers.length === 0) { alert("No hay datos para exportar."); return; }

            doc.autoTable({
                head: [headers],
                body: body,
                startY: 30,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
            });
            doc.save(filename);
        } catch (error) {
            console.error("Error al generar PDF del listado de compras:", error);
            alert("Error al generar PDF del listado de compras. Verifique la consola para más detalles.");
        }
    }
    
    // --- Funciones Comunes de Exportación (Adaptadas para DataTables) ---
    function escapeCsvCell(cellData) {
        if (cellData == null) return '';
        let dataString = String(cellData).replace(/"/g, '""');
        if (dataString.search(/("|,|;|\n)/g) >= 0) dataString = '"' + dataString + '"';
        return dataString;
    }

    function getTableDataForExport(dtInstance, excludeActions = true) {
        const headers = [];
        const body = [];
        let actionsColOriginalIndex = -1;

        // Cabeceras
        const headerCells = dtInstance.table().header().querySelectorAll('th');
        headerCells.forEach((th) => {
            if (th.offsetParent !== null) { // Solo columnas visibles
                if (excludeActions && th.innerText.trim().toLowerCase() === 'acciones') {
                    actionsColOriginalIndex = $(th).index();
                } else {
                    headers.push(th.innerText.trim());
                }
            }
        });

        // Cuerpo
        dtInstance.rows({ search: 'applied' }).data().each(function(rowDataArray) {
            const filteredRow = [];
            rowDataArray.forEach((cellData, cellIndex) => {
                if (cellIndex !== actionsColOriginalIndex) {
                    let cleanData = cellData;
                    // Para la columna 'Estado', extraer el texto de los badges
                    if (headers[cellIndex] && headers[cellIndex].toLowerCase() === 'estado' && typeof cellData === 'string' && cellData.includes('<span class="badge')) {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = cellData;
                        cleanData = tempDiv.querySelector('.badge')?.textContent.trim() || cellData;
                    }
                    filteredRow.push(String(cleanData).trim());
                }
            });
            if (filteredRow.length > 0) body.push(filteredRow);
        });
        return { headers, body };
    }

    function exportDataToCSV(filename = 'export.csv', separator = ',', dtInstance) {
        if (!dtInstance) { alert("La tabla de datos no está inicializada."); return; }
        const { headers, body: dataRows } = getTableDataForExport(dtInstance);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        let csv = ['\uFEFF']; // BOM for UTF-8
        csv.push(headers.map(header => escapeCsvCell(header)).join(separator));
        dataRows.forEach(rowArray => {
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
        if (text == null) return text;
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, ''); // Quitar símbolos de moneda
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) { // Formato europeo: 1.234,56
                cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            } else { // Formato americano: 1,234.56
                cleanText = cleanText.replace(/,/g, '');
            }
        } else if (cleanText.includes(',')) { // Solo coma, podría ser decimal europeo
            cleanText = cleanText.replace(/,/g, '.');
        }
        const num = parseFloat(cleanText);
        return isNaN(num) ? String(text).trim() : num;
    }

    function exportDataToExcel(filename = 'export.xlsx', sheetName = 'Datos', dtInstance) {
        if (!dtInstance) { alert("La tabla de datos no está inicializada."); return; }
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }

        const { headers, body: dataRows } = getTableDataForExport(dtInstance);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        const aoaData = [headers];
        dataRows.forEach(rowArray => {
            const processedRow = rowArray.map((cell, colIndex) => {
                const headerName = headers[colIndex] ? headers[colIndex].toLowerCase() : '';
                if (headerName === 'id' || headerName === 'total') {
                    return parseNumericValue(cell);
                }
                return cell;
            });
            aoaData.push(processedRow);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoaData);

        const range = XLSX.utils.decode_range(ws['!ref']);
        const colWidths = headers.map(header => ({ wch: Math.max(10, header.length + 2) }));

        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[cell_ref]) continue; // Asegura que el bucle continúe si la celda no existe
                if (!ws[cell_ref].s) ws[cell_ref].s = {};

                const len = ws[cell_ref].v ? String(ws[cell_ref].v).length : 0;
                if (colWidths[C]) colWidths[C].wch = Math.max(colWidths[C].wch, len + 2);

                if (R === 0) { // Cabecera
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } };
                    ws[cell_ref].s.alignment = { horizontal: "center", vertical: "center" };
                }

                const headerName = headers[C] ? headers[C].toLowerCase() : '';
                if (ws[cell_ref].t === 'n') { // Si es número
                    if (headerName === 'id') {
                        ws[cell_ref].s.numFmt = "0"; // Entero
                        ws[cell_ref].s.alignment = { horizontal: "center" };
                    } else if (headerName === 'total') {
                        ws[cell_ref].s.numFmt = '$#,##0.00'; // Moneda
                        ws[cell_ref].s.alignment = { horizontal: "right" };
                    }
                } else if (headerName === 'id' || headerName === 'estado' || headerName === 'fecha') {
                     ws[cell_ref].s.alignment = { horizontal: "center" };
                }
            }
        }
        ws['!cols'] = colWidths;

        XLSX.utils.book_append_sheet(wb, ws, sheetName); // Corregido: usa wb en lugar de workbook
        XLSX.writeFile(wb, filename);
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

    const date = new Date();
    const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
    const baseFilename = `listado_compras_${todayForFilename}`;

    document.getElementById('exportCsvButtonList')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            if (csvSeparatorSelect) csvSeparatorSelect.value = ';';
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
        if (csvFilenameInput && csvSeparatorSelect && dataTableInstance) {
            const filename = csvFilenameInput.value.trim() || `${baseFilename}.csv`;
            const separator = csvSeparatorSelect.value;
            exportDataToCSV(filename, separator, dataTableInstance);
            if(csvModal) csvModal.hide();
        }
    });

    document.getElementById('exportPdfButtonListTrigger')?.addEventListener('click', () => {
        if (pdfModal && pdfFilenameInput) {
            pdfFilenameInput.value = `${baseFilename}.pdf`;
            pdfModal.show();
        }
    });

    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput && dataTableInstance) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportDataToExcel(filename, 'Listado Compras', dataTableInstance);
            if(excelModal) excelModal.hide();
        }
    });

    document.getElementById('confirmPdfExportBtn')?.addEventListener('click', () => {
        if (pdfFilenameInput && dataTableInstance) {
            const filename = pdfFilenameInput.value.trim() || `${baseFilename}.pdf`;
            exportListToPdf(filename);
            if(pdfModal) pdfModal.hide();
        }
    });

});
</script>
@endpush

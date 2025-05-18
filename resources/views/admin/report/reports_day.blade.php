@extends('layouts.admin') {{-- Usando tu layout personalizado --}}

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Reporte de Ventas - Hoy')

@section('page_header', 'Reporte de Ventas del Día')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Reportes</a></li> {{-- Asumiendo que 'home' es el inicio de reportes o dashboard general --}}
    <li class="breadcrumb-item active" aria-current="page">Del Día</li>
@endsection

@section('content')
    {{-- El H1 anterior se elimina ya que @page_header lo maneja --}}

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Ventas realizadas hoy: {{ \Carbon\Carbon::today('America/Lima')->format('d/m/Y') }}</h3>
                @if($sales->count())
                    <div>
                        <button id="exportReportCsvButton" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bi bi-filetype-csv me-1"></i> CSV
                        </button>
                        <button id="exportReportExcelButton" class="btn btn-sm btn-outline-success me-2">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                        <button id="exportReportPdfButtonTrigger" class="btn btn-sm btn-danger">
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
                            <td colspan="5" class="text-right"><strong>Total del Día:</strong></td>
                            <td class="text-right"><strong>S/ {{ number_format($total, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @else
                <div class="alert alert-info">
                    No se encontraron ventas para el día de hoy.
                </div>
            @endif
        </div>
    </div>
@endsection {{-- Cambiado de @stop a @endsection (más estándar) --}}

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

    {{-- Modal for PDF Export Options --}}
    <div class="modal fade" id="pdfReportExportModal" tabindex="-1" aria-labelledby="pdfReportExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfReportExportModalLabel">Exportar Reporte a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfReportFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfReportFilenameInput" placeholder="nombre_archivo.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfReportExportBtn">Confirmar y Exportar</button>
                </div>
            </div>
        </div>
    </div>
</div>

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
    const tableIdToExport = 'salesReportTable';
    let dataTableInstance;

    // Inicializar DataTables
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $(`#${tableIdToExport}`).length > 0 && $(`#${tableIdToExport} tbody tr`).length > 1) { // Solo inicializar si hay datos
        try {
            dataTableInstance = $(`#${tableIdToExport}`).DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "searching": $(`#${tableIdToExport} tbody tr`).length > 10, // Habilitar búsqueda solo si hay más de 10 filas
                "paging": $(`#${tableIdToExport} tbody tr`).length > 10,    // Habilitar paginación solo si hay más de 10 filas
                "info": $(`#${tableIdToExport} tbody tr`).length > 10,     // Habilitar info solo si hay más de 10 filas
                "columnDefs": [
                    { "orderable": false, "searchable": false, "targets": -1 }, // Última columna (Acciones)
                    { "type": "num", "targets": 0 }, // ID Venta
                    { "type": "date-eu", "targets": 1 }, // Fecha
                    { "type": "num-fmt", "targets": 5 } // Total (S/)
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Sumar la columna 'Total (S/)' visible en la página actual
                    var totalPage = api
                        .column( 5, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(String(a).replace(/[S/.]/g, '').replace(',', '.')) + parseFloat(String(b).replace(/[S/.]/g, '').replace(',', '.'));
                        }, 0 );

                    // Sumar la columna 'Total (S/)' de todos los datos (después de filtrar)
                    var totalAll = api
                        .column( 5, { search: 'applied' } )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(String(a).replace(/[S/.]/g, '').replace(',', '.')) + parseFloat(String(b).replace(/[S/.]/g, '').replace(',', '.'));
                        }, 0 );

                    // Actualizar el footer
                    // El footer original ya tiene el total general, así que no es estrictamente necesario actualizarlo aquí
                    // a menos que quieras mostrar el total de la página actual.
                    // $( api.column( 5 ).footer() ).html(
                    //     'S/ ' + totalPage.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) +
                    //     ' (S/ ' + totalAll.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' total)'
                    // );
                }
            });
            console.log(`DataTables inicializado para #${tableIdToExport}`);
        } catch (e) {
            console.error(`Error inicializando DataTables para #${tableIdToExport}:`, e);
        }
    }

    // --- Exportación a PDF ---
    function exportReportToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const todayDate = "{{ \Carbon\Carbon::today('America/Lima')->format('d/m/Y') }}";
            const title = `Reporte de Ventas del Día (${todayDate})`;
            const finalFilename = filename || `reporte_ventas_dia_${todayDate.replace(/\//g, '-')}.pdf`;

            doc.setFontSize(18);
            doc.text(title, 14, 22);

            const { headers, body, footer } = getTableDataForExport(dataTableInstance || document.getElementById(tableIdToExport), true, true);
            if (headers.length === 0 && body.length === 0) { alert("No hay datos para exportar a PDF."); return; }

            doc.autoTable({
                head: [headers],
                body: body,
                foot: [footer], // Añadir el pie de tabla
                startY: 30,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
                footStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
            });
            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF del reporte diario:", error);
            alert("Error al generar PDF del reporte diario. Verifique la consola para más detalles.");
        }
    }

    // --- Funciones Comunes de Exportación (Adaptadas para DataTables) ---
    function escapeCsvCell(cellData) {
        if (cellData == null) return '';
        let dataString = String(cellData).replace(/"/g, '""');
        if (dataString.search(/("|,|;|\n)/g) >= 0) dataString = '"' + dataString + '"';
        return dataString;
    }

    function getTableDataForExport(tableSource, excludeActions = true, includeFooter = false) {
        const headers = [];
        const body = [];
        const footer = [];
        let actionsColOriginalIndex = -1;

        if (tableSource instanceof $.fn.dataTable.Api) { // Si es una instancia de DataTables
            // Cabeceras
            const headerCells = tableSource.table().header().querySelectorAll('th');
            headerCells.forEach((th) => {
                if (th.offsetParent !== null) {
                    if (excludeActions && th.innerText.trim().toLowerCase() === 'acciones') {
                        actionsColOriginalIndex = $(th).index();
                    } else {
                        headers.push(th.innerText.trim());
                    }
                }
            });
            // Cuerpo
            tableSource.rows({ search: 'applied' }).data().each(function(rowDataArray) {
                const filteredRow = [];
                rowDataArray.forEach((cellData, cellIndex) => {
                    if (cellIndex !== actionsColOriginalIndex) {
                        let cleanData = cellData;
                        if (headers[filteredRow.length] && headers[filteredRow.length].toLowerCase() === 'estado' && typeof cellData === 'string' && cellData.includes('<span class="badge')) {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = cellData;
                            cleanData = tempDiv.querySelector('.badge')?.textContent.trim() || cellData;
                        }
                        filteredRow.push(String(cleanData).trim());
                    }
                });
                if (filteredRow.length > 0) body.push(filteredRow);
            });
            // Pie de tabla (si DataTables lo maneja o si existe en el HTML)
            if (includeFooter) {
                const footerRow = tableSource.table().footer().querySelector('tr');
                if (footerRow) {
                    $(footerRow).find('td, th').each(function(index) {
                        if (index !== actionsColOriginalIndex) { // Considerar si la columna de acciones tiene un td en el footer
                             footer.push($(this).text().trim());
                        }
                    });
                }
            }
        } else { // Si es un elemento de tabla HTML (fallback)
            const tableElement = tableSource;
            $(tableElement).find('thead tr th').each(function(index) {
                if (excludeActions && $(this).text().trim().toLowerCase() === 'acciones') {
                    actionsColOriginalIndex = index;
                } else {
                    headers.push($(this).text().trim());
                }
            });
            $(tableElement).find('tbody tr').each(function() {
                const rowData = [];
                $(this).find('td').each(function(index) {
                    if (index !== actionsColOriginalIndex) {
                        let cellText = $(this).text().trim();
                        if (headers[rowData.length] && headers[rowData.length].toLowerCase() === 'estado' && $(this).find('.badge').length) {
                            cellText = $(this).find('.badge').text().trim();
                        }
                        rowData.push(cellText);
                    }
                });
                if (rowData.length > 0) body.push(rowData);
            });
            if (includeFooter) {
                $(tableElement).find('tfoot tr td, tfoot tr th').each(function(index) {
                     if (index !== actionsColOriginalIndex) {
                        footer.push($(this).text().trim());
                     }
                });
            }
        }
        return { headers, body, footer };
    }

    function exportDataToCSV(filename = 'export.csv', separator = ',', dataSource) {
        const { headers, body, footer } = getTableDataForExport(dataSource, true, true);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        let csv = ['\uFEFF']; // BOM for UTF-8
        csv.push(headers.map(header => escapeCsvCell(header)).join(separator));
        body.forEach(rowArray => {
            csv.push(rowArray.map(cell => escapeCsvCell(cell)).join(separator));
        });
        if (footer.length > 0) {
            csv.push(footer.map(cell => escapeCsvCell(cell)).join(separator));
        }

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
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, '').replace(/\s*€$/, '');
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) {
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
        return isNaN(num) ? String(text).trim() : num;
    }

    function exportDataToExcel(filename = 'export.xlsx', sheetName = 'Datos', dataSource) {
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }

        const { headers, body: dataRows, footer: footerRow } = getTableDataForExport(dataSource, true, true);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        const aoaData = [headers];
        dataRows.forEach(rowArray => {
            const processedRow = rowArray.map((cell, colIndex) => {
                const headerName = headers[colIndex] ? headers[colIndex].toLowerCase() : '';
                if (headerName === 'id venta' || headerName === 'total (s/)') {
                    return parseNumericValue(cell);
                }
                return cell;
            });
            aoaData.push(processedRow);
        });

        if (footerRow.length > 0) {
             const processedFooter = footerRow.map((cell, colIndex) => {
                // Asumimos que el total en el footer está en la misma posición que la cabecera "Total (S/)"
                // o que es la última celda numérica relevante.
                // Esta lógica puede necesitar ajuste si la estructura del footer es compleja.
                const potentialHeaderIndex = headers.length - footerRow.length + colIndex;
                const headerName = headers[potentialHeaderIndex] ? headers[potentialHeaderIndex].toLowerCase() : '';
                 if (cell.toLowerCase().includes('total') && headerName === 'total (s/)') { // Si la celda del footer es un total
                    return parseNumericValue(cell.replace(/[^0-9,.-]+/g,"")); // Extraer solo el número
                 }
                 return cell.includes('Total del Día:') || cell.includes('Total del Periodo:') ? {v: cell, s: {font: {bold: true}}} : cell;
            });
            aoaData.push(processedFooter);
        }

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoaData);

        const range = XLSX.utils.decode_range(ws['!ref']);
        const colWidths = headers.map(header => ({ wch: Math.max(10, header.length + 2) }));

        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[cell_ref]) continue; // Cambiado para asegurar que el bucle continúe
                if (!ws[cell_ref].s) ws[cell_ref].s = {};

                const len = ws[cell_ref].v ? String(ws[cell_ref].v).length : 0;
                if (colWidths[C]) colWidths[C].wch = Math.max(colWidths[C].wch, len + 2);

                if (R === 0) { // Cabecera
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } };
                    ws[cell_ref].s.alignment = { horizontal: "center", vertical: "center" };
                } else if (R === range.e.r && footerRow.length > 0) { // Pie de tabla
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFEFEFEF" } };
                }

                const headerName = headers[C] ? headers[C].toLowerCase() : '';
                if (ws[cell_ref].t === 'n') {
                    if (headerName === 'id venta') {
                        ws[cell_ref].s.numFmt = "0";
                        ws[cell_ref].s.alignment = { horizontal: "center" };
                    } else if (headerName === 'total (s/)') {
                        ws[cell_ref].s.numFmt = '"S/" #,##0.00';
                        ws[cell_ref].s.alignment = { horizontal: "right" };
                    }
                } else if (headerName === 'id venta' || headerName === 'estado' || headerName === 'fecha') {
                     ws[cell_ref].s.alignment = { horizontal: "center" };
                }
            }
        }
        ws['!cols'] = colWidths;

        XLSX.utils.book_append_sheet(wb, ws, sheetName); // Corregido: usar wb en lugar de workbook
        XLSX.writeFile(wb, filename);
    }

    // --- Modal Instances & Export Button Event Listeners ---
    const csvModalEl = document.getElementById('csvExportModal');
    const csvModal = csvModalEl ? new bootstrap.Modal(csvModalEl) : null;
    const excelModalEl = document.getElementById('excelExportModal');
    const excelModal = excelModalEl ? new bootstrap.Modal(excelModalEl) : null;
    const pdfReportModalEl = document.getElementById('pdfReportExportModal');
    const pdfReportModal = pdfReportModalEl ? new bootstrap.Modal(pdfReportModalEl) : null;

    const csvFilenameInput = document.getElementById('csvFilenameInput');
    const csvSeparatorSelect = document.getElementById('csvSeparatorSelect');
    const excelFilenameInput = document.getElementById('excelFilenameInput');
    const pdfReportFilenameInput = document.getElementById('pdfReportFilenameInput');

    const todayForFilename = "{{ \Carbon\Carbon::today('America/Lima')->format('Ymd') }}";
    const baseFilename = `reporte_ventas_dia_${todayForFilename}`;

    document.getElementById('exportReportCsvButton')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput && csvSeparatorSelect) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            csvSeparatorSelect.value = ';';
            csvModal.show();
        }
    });

    document.getElementById('exportReportExcelButton')?.addEventListener('click', () => {
        if (excelModal && excelFilenameInput) {
            excelFilenameInput.value = `${baseFilename}.xlsx`;
            excelModal.show();
        }
    });

    document.getElementById('exportReportPdfButtonTrigger')?.addEventListener('click', () => {
        if (pdfReportModal && pdfReportFilenameInput) {
            pdfReportFilenameInput.value = `${baseFilename}.pdf`;
            pdfReportModal.show();
        }
    });

    document.getElementById('confirmCsvExportBtn')?.addEventListener('click', () => {
        if (csvFilenameInput && csvSeparatorSelect) {
            const filename = csvFilenameInput.value.trim() || `${baseFilename}.csv`;
            const separator = csvSeparatorSelect.value;
            exportDataToCSV(filename, separator, dataTableInstance || document.getElementById(tableIdToExport));
            if(csvModal) csvModal.hide();
        }
    });

    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportDataToExcel(filename, 'Reporte Ventas Dia', dataTableInstance || document.getElementById(tableIdToExport));
            if(excelModal) excelModal.hide();
        }
    });

    document.getElementById('confirmPdfReportExportBtn')?.addEventListener('click', () => {
        if (pdfReportFilenameInput) {
            const filename = pdfReportFilenameInput.value.trim() || `${baseFilename}.pdf`;
            exportReportToPdf(filename);
            if(pdfReportModal) pdfReportModal.hide();
        }
    });

});
</script>
@endpush

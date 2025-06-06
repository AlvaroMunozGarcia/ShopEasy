@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Reporte de Ventas por Categoría')
@section('page_header', 'Reporte de Ventas por Categoría')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Reportes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ventas por Categoría</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Formulario para seleccionar fechas --}}
    <div class="card card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title">Seleccionar Rango de Fechas</h3>
        </div>
        <form action="{{ route('reports.sales_by_category_results') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label for="fecha_ini" class="form-label">Fecha Inicial</label>
                        <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" value="{{ isset($fecha_ini) ? $fecha_ini->format('Y-m-d') : old('fecha_ini', \Carbon\Carbon::today(config('app.timezone', 'UTC'))->subMonth()->startOfMonth()->format('Y-m-d')) }}" required>
                        @error('fecha_ini')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label for="fecha_fin" class="form-label">Fecha Final</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ isset($fecha_fin) ? $fecha_fin->format('Y-m-d') : old('fecha_fin', \Carbon\Carbon::today(config('app.timezone', 'UTC'))->format('Y-m-d')) }}" required>
                        @error('fecha_fin')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Consultar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Sección para mostrar resultados --}}
    @isset($salesByCategory)
    <div class="row">
        {{-- Tabla de Ventas por Categoría --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Resultados: {{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</h3>
                        @if($salesByCategory->count())
                            <div>
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
                    @if($salesByCategory->count())
                        <div class="table-responsive">
                            <table id="salesByCategoryTable" class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-center">Cantidad Vendida</th>
                                    <th class="text-end">Monto Total (€)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesByCategory as $saleCat)
                                    <tr>
                                        <td>{{ $saleCat->category_name }}</td>
                                        <td class="text-center">{{ $saleCat->total_quantity_sold }}</td>
                                        <td class="text-end">{{ number_format($saleCat->total_amount_sold, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><strong>TOTAL GENERAL:</strong></td>
                                    <td class="text-center"><strong>{{ $totalGeneralQuantity }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($totalGeneralAmount, 2, ',', '.') }} €</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No se encontraron ventas por categoría para el rango de fechas seleccionado.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Gráfico de Ventas por Categoría --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Gráfico de Ventas por Categoría</h3>
                </div>
                <div class="card-body">
                    @if(isset($salesByCategory) && $salesByCategory->count())
                        <canvas id="salesByCategoryChart" style="min-height: 300px; height: 300px; max-height: 350px; max-width: 100%;"></canvas>
                    @else
                        <p class="text-center text-muted">No hay datos suficientes para mostrar el gráfico.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endisset

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

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Librerías para exportación --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableIdToExport = 'salesByCategoryTable';
    let dataTableInstance;

    // Inicializar DataTables (solo si la tabla de resultados existe y tiene datos)
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $(`#${tableIdToExport}`).length > 0 && $(`#${tableIdToExport} tbody tr`).length > 0) {
        const dataRowsCount = $(`#${tableIdToExport} tbody tr`).filter(function() {
            return $(this).find('td').length > 1;
        }).length;

        if (dataRowsCount > 0) {
            try {
                dataTableInstance = $(`#${tableIdToExport}`).DataTable({
                    "pageLength": 10,
                    "language": { "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json" },
                    "responsive": true,
                    "autoWidth": false,
                    "searching": dataRowsCount > 10,
                    "paging": dataRowsCount > 10,
                    "info": dataRowsCount > 10,
                    "columnDefs": [
                        { "type": "num-fmt", "targets": 1 }, // Cantidad
                        { "type": "num-fmt", "targets": 2 }  // Monto (€)
                    ]
                });
            } catch (e) { console.error(`Error inicializando DataTables:`, e); }
        }
    }

    // --- Gráfico de Ventas por Categoría ---
    @if(isset($salesByCategory) && $salesByCategory->count())
    const salesByCategoryData = @json($salesByCategory);
    const categoryLabels = salesByCategoryData.map(item => item.category_name);
    const categoryAmounts = salesByCategoryData.map(item => item.total_amount_sold);

    const backgroundColors = categoryLabels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`);

    const chartCtx = document.getElementById('salesByCategoryChart')?.getContext('2d');
    if (chartCtx) {
        new Chart(chartCtx, {
            type: 'pie', 
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Ventas por Categoría (€)',
                    data: categoryAmounts,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.7', '1')), 
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top', 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // --- Funciones de Exportación (Adaptadas) ---
    function getTableDataForExport(tableSource, excludeActions = false, includeFooter = true) {
        const headers = [];
        const body = [];
        const footer = [];
        
        if (tableSource instanceof $.fn.dataTable.Api) {
            const headerCells = tableSource.table().header().querySelectorAll('th');
            headerCells.forEach(th => {
                if (th.offsetParent !== null) headers.push(th.innerText.trim());
            });
            tableSource.rows({ search: 'applied' }).data().each(function(rowDataArray) {
                const row = rowDataArray.map(cellData => String(cellData).trim());
                if (row.length > 0) body.push(row);
            });
            if (includeFooter) {
                const footerRow = tableSource.table().footer().querySelector('tr');
                if (footerRow) {
                    $(footerRow).find('td, th').each(function() { footer.push($(this).text().trim()); });
                }
            }
        } else { 
            const tableElement = tableSource;
            if (!tableElement) return { headers, body, footer };
            $(tableElement).find('thead tr th').each(function() { headers.push($(this).text().trim()); });
            $(tableElement).find('tbody tr').each(function() {
                if ($(this).find('td[colspan]').length > 0) return;
                const rowData = [];
                $(this).find('td').each(function() { rowData.push($(this).text().trim()); });
                if (rowData.length > 0) body.push(rowData);
            });
            if (includeFooter) {
                $(tableElement).find('tfoot tr td, tfoot tr th').each(function() { footer.push($(this).text().trim()); });
            }
        }
        return { headers, body, footer };
    }
    
    function parseNumericValue(text) {
        if (text === null || text === undefined) return text;
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, '').replace(/\s*€$/, '');
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            else cleanText = cleanText.replace(/,/g, '');
        } else if (cleanText.includes(',')) {
            if (cleanText.match(/,\d{1,2}$/)) cleanText = cleanText.replace(/,/g, '.');
            else cleanText = cleanText.replace(/,/g, '');
        }
        const num = parseFloat(cleanText);
        return isNaN(num) ? String(text).trim() : num;
    }

    function exportDataToExcel(filename = 'export.xlsx', sheetName = 'Datos', dataSource) {
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }
        const { headers, body: dataRows, footer: footerRow } = getTableDataForExport(dataSource, false, true);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }
        const aoaData = [headers];
        dataRows.forEach(rowArray => {
            const processedRow = rowArray.map((cell, colIndex) => {
                const headerName = headers[colIndex] ? headers[colIndex].toLowerCase() : '';
                if (headerName === 'cantidad vendida' || headerName === 'monto total (€)') return parseNumericValue(cell);
                return cell;
            });
            aoaData.push(processedRow);
        });
        if (footerRow.length > 0) {
            const processedFooter = footerRow.map((cell, colIndex) => {
                const headerName = headers[colIndex] ? headers[colIndex].toLowerCase() : '';
                if (headerName === 'cantidad vendida' || headerName === 'monto total (€)') return parseNumericValue(cell.replace(/[^0-9,.-]+/g,""));
                return cell.includes('TOTAL GENERAL:') ? {v: cell, s: {font: {bold: true}}} : cell;
            });
            aoaData.push(processedFooter);
        }
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoaData);
        const range = XLSX.utils.decode_range(ws['!ref']);
        const colWidths = headers.map(header => ({ wch: Math.max(15, header.length + 2) }));
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[cell_ref]) continue;
                if (!ws[cell_ref].s) ws[cell_ref].s = {};
                const len = ws[cell_ref].v ? String(ws[cell_ref].v).length : 0;
                if (colWidths[C]) colWidths[C].wch = Math.max(colWidths[C].wch, len + 2);
                if (R === 0) { 
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } };
                    ws[cell_ref].s.alignment = { horizontal: "center", vertical: "center" };
                } else if (R === range.e.r && footerRow.length > 0) { 
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFEFEFEF" } };
                }
                const headerName = headers[C] ? headers[C].toLowerCase() : '';
                if (ws[cell_ref].t === 'n') {
                    if (headerName === 'cantidad vendida') ws[cell_ref].s.numFmt = "0";
                    else if (headerName === 'monto total (€)') ws[cell_ref].s.numFmt = '#,##0.00 "€"';
                    ws[cell_ref].s.alignment = { horizontal: "right" };
                } else if (headerName === 'cantidad vendida') {
                     ws[cell_ref].s.alignment = { horizontal: "center" };
                }
            }
        }
        ws['!cols'] = colWidths;
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, filename);
    }

    function exportReportToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const fechaIni = "{{ isset($fecha_ini) ? $fecha_ini->format('d/m/Y') : '' }}";
            const fechaFin = "{{ isset($fecha_fin) ? $fecha_fin->format('d/m/Y') : '' }}";
            const title = `Reporte Ventas por Categoría (€) (${fechaIni} - ${fechaFin})`;
            const defaultFilename = `reporte_ventas_categoria_${fechaIni.replace(/\//g, '-')}_${fechaFin.replace(/\//g, '-')}.pdf`;
            const finalFilename = filename || defaultFilename;
            doc.setFontSize(18);
            doc.text(title, 14, 22);
            const { headers, body, footer } = getTableDataForExport(dataTableInstance || document.getElementById(tableIdToExport), false, true);
            if (headers.length === 0 && body.length === 0) { alert("No hay datos para exportar a PDF."); return; }
            doc.autoTable({
                head: [headers], body: body, foot: [footer], startY: 30, theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
                footStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
            });
            doc.save(finalFilename);
        } catch (error) { console.error("Error al generar PDF:", error); alert("Error al generar PDF. Verifique la consola."); }
    }

    // --- Modal Instances & Export Button Event Listeners ---
    const excelModalEl = document.getElementById('excelExportModal');
    const excelModal = excelModalEl ? new bootstrap.Modal(excelModalEl) : null;
    const pdfReportModalEl = document.getElementById('pdfReportExportModal');
    const pdfReportModal = pdfReportModalEl ? new bootstrap.Modal(pdfReportModalEl) : null;

    const excelFilenameInput = document.getElementById('excelFilenameInput');
    const pdfReportFilenameInput = document.getElementById('pdfReportFilenameInput');

    const baseFilename = "reporte_ventas_categoria_{{ isset($fecha_ini) ? $fecha_ini->format('Ymd') : 'FECHA_INI' }}_{{ isset($fecha_fin) ? $fecha_fin->format('Ymd') : 'FECHA_FIN' }}";

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
    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportDataToExcel(filename, 'Ventas por Categoría', dataTableInstance || document.getElementById(tableIdToExport));
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

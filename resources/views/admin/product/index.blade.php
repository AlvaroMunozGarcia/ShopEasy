@extends('layouts.admin')

@push('styles')
{{-- DataTables Bootstrap 5 CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Gestión de Productos')

{{-- Modificar el page_header si está filtrado --}}
@section('page_header')
    Gestión de Productos
    @if(isset($filtered_provider_name) && $filtered_provider_name)
        <small class="text-muted fs-5 fst-italic">- Mostrando productos de: {{ $filtered_provider_name }}</small>
    @endif
@endsection

@section('breadcrumbs')
    @if(isset($filtered_provider_name) && $filtered_provider_name && isset($provider_id_for_breadcrumb_link))
        <li class="breadcrumb-item"><a href="{{ route('providers.index') }}">Proveedores</a></li>
        <li class="breadcrumb-item"><a href="{{ route('providers.show', $provider_id_for_breadcrumb_link) }}">{{ $filtered_provider_name }}</a></li>
    @endif
    <li class="breadcrumb-item active" aria-current="page">Productos</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        {{-- El @page_header ya muestra el título principal de la página. --}}

        {{-- Mensajes flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- INICIO: Mostrar Alertas de Stock Bajo (generadas por ventas) --}}
        @if (session()->has('low_stock_alerts') && is_array(session('low_stock_alerts')) && count(session('low_stock_alerts')) > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>¡Atención! Productos con stock bajo (detectado en transacciones recientes):</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('low_stock_alerts') as $alert_message)
                        <li>{{ $alert_message }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            {{-- Limpiar las alertas de la sesión después de mostrarlas en esta página --}}
            @php session()->forget('low_stock_alerts'); @endphp
        @endif
        {{-- FIN: Mostrar Alertas de Stock Bajo --}}

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Lista de Productos
                    @if(isset($filtered_provider_name) && $filtered_provider_name)
                        <small class="text-white-50"> (Proveedor: {{ $filtered_provider_name }})</small>
                    @endif
                </h5>
                <div>
                    @if(isset($filtered_provider_name) && $filtered_provider_name)
                        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm fw-semibold me-2" title="Quitar filtro de proveedor">
                            <i class="bi bi-x-lg"></i> Quitar Filtro
                        </a>
                    @endif
                    <button id="exportCsvButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-filetype-csv me-1"></i> CSV
                    </button>
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    {{-- El enlace para añadir producto ahora considera si hay un filtro de proveedor activo --}}
                    <a href="{{ route('products.create', (isset($provider_id_for_create_link) && $provider_id_for_create_link ? ['provider_id' => $provider_id_for_create_link] : [])) }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Añadir Producto
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Mensaje alternativo de filtro (si prefieres no tenerlo en el título del card) --}}
                {{-- @if(isset($filtered_provider_name) && $filtered_provider_name)
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span>Mostrando productos del proveedor: <strong>{{ $filtered_provider_name }}</strong></span>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-x-lg"></i> Quitar Filtro
                        </a>
                    </div>
                @endif --}}
                <div class="table-responsive">
                    <table id="productsTable" class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th>Stock</th>
                                <th>Precio Venta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="text-center">{{ $product->id }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->provider->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $product->stock }}</td>
                                    <td>{{ number_format($product->sell_price, 2, ',', '.') }} €</td>
                                    <td class="text-center">
                                        @if($product->status === 'ACTIVE')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
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
                                    <td colspan="9" class="text-center text-muted">
                                        @if(isset($filtered_provider_name) && $filtered_provider_name)
                                            No hay productos registrados para el proveedor <strong>{{ $filtered_provider_name }}</strong>.
                                        @else
                                            No hay productos registrados.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- La paginación de Laravel se elimina o comenta, DataTables la manejará --}}
            {{-- @if(method_exists($products, 'links'))
                <div class="card-footer d-flex justify-content-center">
                    {{ $products->links() }}
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
    const tableIdToExport = 'productsTable';
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
                    { "type": "num", "targets": [0, 5] }, // ID y Stock son numéricos para ordenación
                    { "type": "num-fmt", "targets": [6] } // Precio Venta (para ordenación con formato)
                ]
            });
            console.log(`DataTables inicializado para #${tableIdToExport}`);
        } catch (e) {
            console.error(`Error inicializando DataTables para #${tableIdToExport}:`, e);
        }
    }

    function exportListToPdf(filename = 'listado_productos.pdf') {
        if (!dataTableInstance) {
            alert("La tabla de datos no está inicializada.");
            return;
        }
        try {
            const { jsPDF } = window.jspdf;
            if (!jsPDF) { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }

            const doc = new jsPDF();
            doc.setFontSize(18);
            doc.text("Listado de Productos", 14, 22);

            const head = [];
            const body = [];
            let actionsColumnOriginalIndex = -1;

            // Obtener cabeceras de DataTables (respetando visibilidad y orden)
            const headerCells = dataTableInstance.table().header().querySelectorAll('th');
            const currentHead = [];
            headerCells.forEach((th, index) => {
                if (th.offsetParent !== null) { // Solo columnas visibles
                    if (th.innerText.trim().toLowerCase() === 'acciones') {
                        actionsColumnOriginalIndex = $(th).index(); // Índice original en el DOM
                    } else {
                        currentHead.push(th.innerText.trim());
                    }
                }
            });
            head.push(currentHead);

            // Obtener datos de DataTables (respetando filtro y orden)
            dataTableInstance.rows({ search: 'applied' }).data().each(function(rowDataArray) {
                const filteredRow = [];
                rowDataArray.forEach((cellData, cellIndex) => {
                    if (cellIndex !== actionsColumnOriginalIndex) {
                        // Limpiar HTML de badges para el PDF, si es necesario
                        let cleanData = cellData;
                        if (typeof cellData === 'string' && cellData.includes('<span class="badge')) {
                            cleanData = $(cellData).text(); // Extraer solo el texto del badge
                        }
                        filteredRow.push(cleanData);
                    }
                });
                if (filteredRow.length > 0) body.push(filteredRow);
            });

            doc.autoTable({
                head: head,
                body: body,
                startY: 30,
            });
            doc.save(filename);
        } catch (error) {
            console.error("Error al generar PDF del listado de productos:", error);
            alert("Error al generar PDF del listado de productos. Verifique la consola para más detalles.");
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
                    if (typeof cellData === 'string' && cellData.includes('<span class="badge')) {
                        cleanData = $(cellData).text();
                    }
                    filteredRow.push(cleanData);
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
        if (text == null) return text; // Handles null or undefined
        let cleanText = String(text).trim().replace(/^(S\/\s*|\$\s*|€\s*)/, '').replace(/\s*€$/, ''); // Remove currency symbols
        if (cleanText.includes(',') && cleanText.includes('.')) {
            if (cleanText.lastIndexOf(',') > cleanText.lastIndexOf('.')) { // European: 1.234,56
                cleanText = cleanText.replace(/\./g, '').replace(/,/g, '.');
            } else { // "1,234.56"
                cleanText = cleanText.replace(/,/g, '');
            }
        } else if (cleanText.includes(',')) { // Handles "123,45"
            cleanText = cleanText.replace(/,/g, '.');
        }
        // Check if it's an integer after cleaning
        if (!cleanText.includes('.') && /^\d+$/.test(cleanText)) {
            const intVal = parseInt(cleanText, 10);
            if (!isNaN(intVal) && String(intVal) === cleanText) return intVal;
        }
        const num = parseFloat(cleanText);
        return isNaN(num) ? String(text).trim() : num; // Return original trimmed string if not a valid number
    }

    function exportDataToExcel(filename = 'export.xlsx', sheetName = 'Datos', dtInstance) {
        if (!dtInstance) { alert("La tabla de datos no está inicializada."); return; }
        if (typeof XLSX === 'undefined') { alert("Error: Librería XLSX no cargada."); return; }

        const { headers, body: dataRows } = getTableDataForExport(dtInstance);
        if (headers.length === 0) { alert("No hay datos para exportar."); return; }

        const aoaData = [headers];
        dataRows.forEach(rowArray => {
            const processedRow = rowArray.map((cell, colIndex) => {
                // Column names for numeric parsing (case-insensitive)
                const numericHeaders = ['id', 'stock', 'precio venta'];
                if (headers[colIndex] && numericHeaders.includes(headers[colIndex].toLowerCase())) {
                    return parseNumericValue(cell);
                }
                return cell; // Keep as string otherwise
            });
            aoaData.push(processedRow);
        });

        const ws = XLSX.utils.aoa_to_sheet(aoaData);

        // Apply styles and number formats
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[cell_ref]) continue;
                if (!ws[cell_ref].s) ws[cell_ref].s = {};

                // Header style
                if (R === 0) { // Header row
                    ws[cell_ref].s.font = { bold: true };
                    ws[cell_ref].s.fill = { patternType: "solid", fgColor: { rgb: "FFD9D9D9" } }; // Light grey
                    ws[cell_ref].s.alignment = { horizontal: "center", vertical: "center" };
                }

                // Number formatting for specific columns
                const headerName = headers[C] ? headers[C].toLowerCase() : '';
                if (ws[cell_ref].t === 'n') { // If it's a number type
                    if (headerName === 'id' || headerName === 'stock') {
                        ws[cell_ref].s.numFmt = "0"; // Integer
                    } else if (headerName === 'precio venta') {
                        ws[cell_ref].s.numFmt = "#,##0.00 €"; // Currency
                    }
                    ws[cell_ref].s.alignment = { horizontal: "right" };
                } else if (headerName === 'estado' || headerName === 'id' || headerName === 'stock') {
                     ws[cell_ref].s.alignment = { horizontal: "center" };
                }
            }
        }

        // Auto-adjust column widths
        const colWidths = [];
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let maxLen = 0;
            for (let R = range.s.r; R <= range.e.r; ++R) {
                const cell_ref = XLSX.utils.encode_cell({ c: C, r: R });
                if (ws[cell_ref]) {
                    let cellText = ws[cell_ref].v !== null && ws[cell_ref].v !== undefined ? String(ws[cell_ref].v) : "";
                    if (ws[cell_ref].t === 'n' && ws[cell_ref].s && ws[cell_ref].s.numFmt) {
                        try { // Attempt to format for width calculation
                            cellText = XLSX.SSF.format(ws[cell_ref].s.numFmt, ws[cell_ref].v);
                        } catch(e) {
                            // fallback
                        }
                    }
                    maxLen = Math.max(maxLen, cellText.length);
                }
            }
            colWidths[C] = { wch: Math.max(10, maxLen + 2) };
        }
        ws['!cols'] = colWidths;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
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
    const baseFilename = `listado_productos_${todayForFilename}`;

    document.getElementById('exportCsvButtonList')?.addEventListener('click', () => {
        if (csvModal && csvFilenameInput && csvSeparatorSelect) {
            csvFilenameInput.value = `${baseFilename}.csv`;
            csvSeparatorSelect.value = ';'; // Default
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
        if (csvFilenameInput && csvSeparatorSelect && dataTableInstance) {
            const filename = csvFilenameInput.value.trim() || `${baseFilename}.csv`;
            const separator = csvSeparatorSelect.value;
            exportDataToCSV(filename, separator, dataTableInstance);
            if(csvModal) csvModal.hide();
        }
    });

    document.getElementById('confirmExcelExportBtn')?.addEventListener('click', () => {
        if (excelFilenameInput && dataTableInstance) {
            const filename = excelFilenameInput.value.trim() || `${baseFilename}.xlsx`;
            exportDataToExcel(filename, 'Listado Productos', dataTableInstance);
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

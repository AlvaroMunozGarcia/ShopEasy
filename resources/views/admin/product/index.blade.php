@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('title', 'Gestión de Productos')

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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
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
            @php session()->forget('low_stock_alerts'); @endphp
        @endif
               <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h5 class="mb-2 mb-md-0">
                    Lista de Productos
                    @if(isset($filtered_provider_name) && $filtered_provider_name)
                        <small class="text-white-50"> (Proveedor: {{ $filtered_provider_name }})</small>
                    @endif
                </h5>
                <div class="mt-2 mt-md-0">
                    @if(isset($filtered_provider_name) && $filtered_provider_name)
                        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm fw-semibold me-2" title="Quitar filtro de proveedor">
                            <i class="bi bi-x-lg"></i> Quitar Filtro
                        </a>
                    @endif
                    <button id="exportExcelButtonList" class="btn btn-outline-light btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    <a href="{{ route('products.create', (isset($provider_id_for_create_link) && $provider_id_for_create_link ? ['provider_id' => $provider_id_for_create_link] : [])) }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Añadir Producto
                    </a>
                </div>
            </div>

            <div class="card-body">
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
        </div>
    </div>

    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);
        if (userFilename === null) {
            return null;
        }
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }

        return finalFilename;
    }


 $(document).ready(function () {
        $('#productsTable').DataTable({
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
        document.getElementById('exportPdfButtonListTrigger').addEventListener('click', function () {
            pdfExport();
        });
    });





   function pdfExport() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const table = document.getElementById('productsTable');
    const headers = [];
    const rows = [];
    const ths = table.querySelectorAll('thead th');
    ths.forEach((th, index) => {
        if (index < ths.length - 1) { 
            headers.push(th.innerText.trim());
        }
    });
    table.querySelectorAll('tbody tr').forEach(tr => {
        const tds = tr.querySelectorAll('td');
        const row = [];

        tds.forEach((td, index) => {
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

    const filename = getCustomFilename('productos', 'pdf');
    if (filename) {
    doc.save(filename);
}}




document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportExcelButtonList').addEventListener('click', function () {
            excelExport();
        });
    });





function excelExport() {
    const table = document.getElementById('productsTable');
    const wb = XLSX.utils.book_new();

    const ws = XLSX.utils.table_to_sheet(table, {
        sheet: "Productos",
        raw: true,
       
    });

    XLSX.utils.book_append_sheet(wb, ws, "Productos");
    const filename = getCustomFilename('productos', 'xlsx');
    if (filename) {
    XLSX.writeFile(wb, filename);
}
}
</script>
@endpush

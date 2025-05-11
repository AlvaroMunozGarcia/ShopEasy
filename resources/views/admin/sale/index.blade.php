@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Listado de Ventas')

@section('content')
    {{-- Se elimina content-wrapper y content-header para una estructura de tarjeta simple como en las otras vistas --}}
    {{-- El breadcrumb, si es necesario, debería manejarse consistentemente a través del layout principal o no incluirse aquí si no está en las otras --}}

    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Listado de Ventas</h4> {{-- Título principal de la tarjeta --}}
                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill"></i> Registrar Nueva Venta {{-- Icono Bootstrap --}}
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
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="salesTable" class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
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
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                <td>S/ {{ number_format($sale->total, 2) }}</td>
                                <td>
                                    @if($sale->status == 'VALID')
                                        <span class="badge bg-success">Válida</span>
                                    @elseif($sale->status == 'CANCELLED')
                                        <span class="badge bg-danger">Anulada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ Str::title(str_replace('_', ' ', $sale->status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="bi bi-eye-fill"></i></a>

                                    @if($sale->status == 'VALID')
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres anular esta venta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-warning" title="Anular Venta"><i class="bi bi-x-circle-fill"></i></button>
                                    </form>
                                    @endif

                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-sm btn-danger" title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron ventas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- Si usas paginación para ventas, puedes añadirla aquí --}}
            {{-- @if ($sales->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $sales->links() }}
                </div>
            @endif --}}
        </div>
    </div>
@endsection

@push('scripts')
{{-- Scripts para DataTables si los usas --}}
{{-- <script>
    $(function () {
        $("#salesTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#salesTable_wrapper .col-md-6:eq(0)');
    });
</script> --}}
{{-- Si no usas DataTables, puedes eliminar este @push o mantenerlo para futuros scripts específicos de esta página --}}
@endpush

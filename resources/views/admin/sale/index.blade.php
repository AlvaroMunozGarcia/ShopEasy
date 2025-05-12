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
                <a href="{{ route('sales.create') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="bi bi-plus-circle-fill me-1"></i> Registrar Nueva Venta
                </a>
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

                                        <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Descargar PDF">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
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
</div>
@endsection

@push('scripts')
{{-- Si en el futuro decides usar DataTables --}}
{{-- 
<script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            responsive: true,
            autoWidth: false,
            lengthChange: false,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        }).buttons().container().appendTo('#salesTable_wrapper .col-md-6:eq(0)');
    });
</script> 
--}}
@endpush

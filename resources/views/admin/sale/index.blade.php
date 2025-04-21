@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Listado de Ventas')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Listado de Ventas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ventas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ventas Registradas</h3>
                            <div class="card-tools">
                                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Registrar Nueva Venta
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
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
                                <table id="salesTable" class="table table-bordered table-striped table-hover"> {{-- Añadido ID para posible DataTables --}}
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
                                                {{-- Asume que $casts está configurado en el modelo Sale --}}
                                                <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                                <td>S/ {{ number_format($sale->total, 2) }}</td> {{-- Ajusta el símbolo de moneda si es necesario --}}
                                                <td>
                                                    @if($sale->status == 'VALID') {{-- O el estado que uses para 'activa' --}}
                                                        <span class="badge bg-success">Válida</span>
                                                    @elseif($sale->status == 'CANCELLED') {{-- O el estado que uses para 'anulada' --}}
                                                        <span class="badge bg-danger">Anulada</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">{{ $sale->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- Botón Ver Detalles --}}
                                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="far fa-eye"></i></a>

                                                    {{-- Botón Cancelar (si aplica y el estado es válido) --}}
                                                    @if($sale->status == 'VALID')
                                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres anular esta venta?');">
                                                        @csrf
                                                        @method('DELETE') {{-- Asume que DELETE anula la venta --}}
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Anular Venta"><i class="fas fa-ban"></i></button>
                                                    </form>
                                                    @endif

                                                    {{-- Botón PDF (si tienes la ruta definida) --}}
                                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-sm btn-danger" title="Descargar PDF">
                                                        <i class="fas fa-file-pdf"></i>
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
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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
@endpush

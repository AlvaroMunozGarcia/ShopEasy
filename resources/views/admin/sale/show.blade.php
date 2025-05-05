{{-- resources/views/admin/sale/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Detalles de la Venta #{{ $sale->id }}</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Venta</dt>
                <dd class="col-sm-9">{{ $sale->id }}</dd>

                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">{{ $sale->sale_date->format('d/m/Y H:i') }}</dd> {{-- Asumiendo que sale_date es Carbon --}}

                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $sale->client->name ?? 'N/A' }}</dd> {{-- Ajusta según tu relación --}}

                <dt class="col-sm-3">Vendedor</dt>
                <dd class="col-sm-9">{{ $sale->user->name ?? 'N/A' }}</dd> {{-- Ajusta según tu relación --}}

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9">{{ number_format($sale->total, 2, ',', '.') }} €</dd>

                {{-- Añade otros campos generales de la venta si los tienes (impuestos, estado, etc.) --}}
            </dl>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Productos Incluidos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                            <th>Código de Barras</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                <td>{{ $detail->product->code ?? 'N/A' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->price, 2, ',', '.') }} €</td>
                                <td>{{ number_format($detail->quantity * $detail->price, 2, ',', '.') }} €</td>
                                <td>
                                    {{-- Mostrar el código de barras usando la clave de producto ID --}}
                                    {!! $barcodes[$detail->product->id] ?? '<span class="text-danger">Error al generar</span>' !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
             <a href="{{ route('sales.index') }}" class="btn btn-secondary"> {{-- Asume que tienes una ruta sales.index --}}
                <i class="bi bi-arrow-left"></i> Volver a la lista
            </a>
            {{-- Puedes añadir botones para imprimir, editar, etc. si es necesario --}}
        </div>
    </div>
</div>
@endsection
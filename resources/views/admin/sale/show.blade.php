@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles de Venta')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalles de Venta</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
                        <li class="breadcrumb-item active">Detalle Venta #{{ $sale->id }}</li>
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
                            <h3 class="card-title">Información de la Venta #{{ $sale->id }}</h3>
                            <div class="card-tools">
                                {{-- Botón para imprimir PDF si existe la ruta --}}
                                <a href="{{ route('sales.pdf', $sale) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Imprimir PDF
                                </a>
                                {{-- Botón para volver al listado --}}
                                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Cliente:</strong> {{ $sale->client->name ?? 'N/A' }}</p>
                                    <p><strong>DNI/RUC:</strong> {{ $sale->client->dni ?? $sale->client->ruc ?? 'N/A' }}</p>
                                    <p><strong>Email Cliente:</strong> {{ $sale->client->email ?? 'N/A' }}</p>
                                    <p><strong>Teléfono Cliente:</strong> {{ $sale->client->phone ?? 'N/A' }}</p>
                                    <p><strong>Dirección Cliente:</strong> {{ $sale->client->address ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 text-md-right">
                                     {{-- Asume que $casts está configurado en el modelo Sale --}}
                                    <p><strong>Fecha de Venta:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : 'N/A' }}</p>
                                    <p><strong>Usuario Registrador:</strong> {{ $sale->user->name ?? 'N/A' }}</p>
                                    <p><strong>Impuesto (%):</strong> {{ $sale->tax }}%</p>
                                    <p><strong>Estado:</strong>
                                        @if($sale->status == 'VALID')
                                            <span class="badge bg-success">Válida</span>
                                        @elseif($sale->status == 'CANCELLED')
                                            <span class="badge bg-danger">Anulada</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $sale->status }}</span>
                                        @endif
                                    </p>
                                    <h4><strong>Total Pagado:</strong> S/ {{ number_format($sale->total, 2) }}</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Venta</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario (S/)</th>
                                            <th>Descuento (%)</th>
                                            <th>Subtotal (S/)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subtotalGeneral = 0; @endphp
                                        @forelse ($sale->saleDetails as $detail)
                                            @php
                                                // Asegúrate que price y quantity no sean null antes de calcular
                                                $price = $detail->price ?? 0;
                                                $quantity = $detail->quantity ?? 0;
                                                $discount = $detail->discount ?? 0;
                                                $lineSubtotal = ($quantity * $price) * (1 - $discount / 100);
                                                $subtotalGeneral += $lineSubtotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                                <td>{{ $quantity }}</td>
                                                <td>{{ number_format($price, 2) }}</td>
                                                <td>{{ number_format($discount, 2) }}%</td>
                                                <td>{{ number_format($lineSubtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay productos en esta venta.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Subtotal (antes de imp.):</strong></td>
                                            {{-- Este subtotal es la suma de los subtotales de línea (ya con descuento) --}}
                                            <td>S/ {{ number_format($subtotalGeneral, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Impuesto ({{ $sale->tax }}%):</strong></td>
                                             {{-- El impuesto se calcula sobre el subtotal general --}}
                                            <td>S/ {{ number_format($subtotalGeneral * ($sale->tax / 100), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                            {{-- El total final guardado en la venta --}}
                                            <td><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver al Listado</a>
                            {{-- Puedes añadir un botón de editar si implementas esa funcionalidad --}}
                            {{-- <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary">Editar</a> --}}
                             <a href="{{ route('sales.pdf', $sale) }}" class="btn btn-info float-right" target="_blank">
                                <i class="fas fa-file-pdf"></i> Imprimir PDF
                            </a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('scripts')
{{-- Si necesitas algún script específico para esta vista --}}
@endpush

{{-- Asumiendo que tienes un layout base como 'layouts.admin' --}}
@extends('layouts.admin') {{-- Cambia 'layouts.admin' por tu layout principal si es diferente --}}

@section('title', 'Detalles de Compra') {{-- Título de la página --}}

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalles de Compra</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
                        <li class="breadcrumb-item active">Detalle Compra #{{ $purchase->id }}</li>
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
                            <h3 class="card-title">Información de la Compra #{{ $purchase->id }}</h3>
                            <div class="card-tools">
                                {{-- Botón para imprimir PDF si existe la ruta --}}
                                <a href="{{ route('purchases.pdf', $purchase) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Imprimir PDF
                                </a>
                                {{-- Botón para volver al listado --}}
                                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Proveedor:</strong> {{ $purchase->provider->name ?? 'N/A' }}</p>
                                    <p><strong>Email Proveedor:</strong> {{ $purchase->provider->email ?? 'N/A' }}</p>
                                    <p><strong>Teléfono Proveedor:</strong> {{ $purchase->provider->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <p><strong>Fecha de Compra:</strong> {{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
                                    <p><strong>Usuario Registrador:</strong> {{ $purchase->user->name ?? 'N/A' }}</p>
                                    <p><strong>Impuesto (%):</strong> {{ $purchase->tax }}%</p>
                                    <h4><strong>Total Pagado:</strong> S/ {{ number_format($purchase->total, 2) }}</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Compra</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario (S/)</th>
                                            <th>Subtotal (S/)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchase->purchaseDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ number_format($detail->price, 2) }}</td>
                                                <td>{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No hay productos en esta compra.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                            {{-- Calculamos el subtotal antes de impuestos para mostrarlo si es relevante --}}
                                            @php
                                                $subtotalGeneral = $purchase->total / (1 + ($purchase->tax / 100));
                                            @endphp
                                            <td>S/ {{ number_format($subtotalGeneral, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Impuesto ({{ $purchase->tax }}%):</strong></td>
                                            <td>S/ {{ number_format($purchase->total - $subtotalGeneral, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                            <td><strong>S/ {{ number_format($purchase->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Volver al Listado</a>
                            {{-- Puedes añadir un botón de editar si implementas esa funcionalidad --}}
                            {{-- <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary">Editar</a> --}}
                             <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-info float-right" target="_blank">
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

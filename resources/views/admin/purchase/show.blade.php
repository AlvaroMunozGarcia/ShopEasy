@extends('layouts.admin')

@section('content')
    <h1>Detalles de la Compra #{{ $purchase->id }}</h1>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Información General</span>
            <div>
                {{-- Botón PDF --}}
                <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="btn btn-sm btn-danger" title="Descargar PDF">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                 {{-- Botón Cancelar Compra --}}
                 @if($purchase->status == 'VALID')
                 <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres cancelar esta compra?');">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="btn btn-sm btn-warning" title="Cancelar Compra"><i class="bi bi-x-circle"></i> Cancelar</button>
                 </form>
                 @endif
                {{-- Botón Volver --}}
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary" title="Volver al Listado">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">ID Compra:</dt>
                        <dd class="col-sm-8">{{ $purchase->id }}</dd>

                        <dt class="col-sm-4">Fecha:</dt>
                        <dd class="col-sm-8">{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : 'N/A' }}</dd>

                        <dt class="col-sm-4">Proveedor:</dt>
                        <dd class="col-sm-8">{{ $purchase->provider ? $purchase->provider->name : 'N/A' }}</dd> {{-- Ajusta si el campo de nombre es diferente --}}
                    </dl>
                </div>
                <div class="col-md-6">
                     <dl class="row">
                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            @if($purchase->status == 'VALID')
                                <span class="badge bg-success">Válida</span>
                            @elseif($purchase->status == 'CANCELLED')
                                <span class="badge bg-danger">Cancelada</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ $purchase->status }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Total Compra:</dt>
                        <dd class="col-sm-8">${{ number_format($purchase->total, 2) }}</dd> {{-- Asegúrate que el símbolo de moneda sea el correcto --}}

                        <dt class="col-sm-4">Creado:</dt>
                        <dd class="col-sm-8">{{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                        <dt class="col-sm-4">Actualizado:</dt>
                        <dd class="col-sm-8">{{ $purchase->updated_at ? $purchase->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            Detalles de los Productos Comprados
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Asume que la relación purchaseDetails y product existen --}}
                        @forelse ($purchase->purchaseDetails as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->product ? $detail->product->name : 'Producto no encontrado' }}</td> {{-- Ajusta el campo de nombre si es necesario --}}
                                <td>{{ $detail->quantity }}</td>
                                <td>${{ number_format($detail->price, 2) }}</td>
                                <td>${{ number_format($detail->quantity * $detail->price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron detalles para esta compra.</td>
                            </tr>
                        @endforelse
                    </tbody>
                     <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total General:</strong></td>
                            <td><strong>${{ number_format($purchase->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

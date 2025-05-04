@extends('layouts.admin')

@section('content')
    <h1>Listado de Compras</h1>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Compras Registradas</span>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Nueva Compra
            </a>
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
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Proveedor</th> {{-- Columna corregida/añadida --}}
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : 'N/A' }}</td>
                                {{-- Asegúrate que la relación 'provider' y el campo 'name' existan --}}
                                <td>{{ $purchase->provider ? $purchase->provider->name : 'N/A' }}</td> {{-- Columna corregida/añadida --}}
                                {{-- Asegúrate que el símbolo de moneda sea el correcto ($ o €) --}}
                                <td>${{ number_format($purchase->total, 2) }}</td>
                                <td>
                                    @if($purchase->status == 'VALID')
                                        <span class="badge bg-success">Válida</span>
                                    @elseif($purchase->status == 'CANCELLED')
                                        <span class="badge bg-danger">Cancelada</span>
                                    @else
                                        {{-- Otros estados si los hubiera --}}
                                        <span class="badge bg-warning text-dark">{{ $purchase->status }}</span>
                                    @endif
                                </td>
                                <td>

                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                
                                    @if($purchase->status == 'VALID')
                                   
                                    @endif

                                     {{-- Botón PDF --}}
                                    <a href="{{ route('purchases.print', $purchase) }}" target="_blank" class="btn btn-sm btn-danger" title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron compras.</td> {{-- Ajustado colspan a 6 --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
       
    </div>
@endsection

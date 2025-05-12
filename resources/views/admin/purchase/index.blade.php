@extends('layouts.admin')

@section('title', 'Listado de Compras')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Mensajes flash --}}
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

        {{-- Tarjeta principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Compras Registradas</h5>
                <a href="{{ route('purchases.create') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Crear Nueva Compra
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td class="text-center">{{ $purchase->id }}</td>
                                    <td>{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $purchase->provider->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($purchase->total, 2) }}</td>
                                    <td>
                                        @switch($purchase->status)
                                            @case('VALID')
                                                <span class="badge bg-success">Válida</span>
                                                @break
                                            @case('CANCELLED')
                                                <span class="badge bg-danger">Cancelada</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning text-dark">{{ $purchase->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('purchases.print', $purchase) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Descargar PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No se encontraron compras.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer para paginación si se requiere --}}
            @if ($purchases instanceof \Illuminate\Pagination\LengthAwarePaginator && $purchases->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

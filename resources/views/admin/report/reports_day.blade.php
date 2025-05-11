@extends('layouts.admin') {{-- Usando tu layout personalizado --}}

@section('title', 'Reporte de Ventas - Hoy')

{{-- El título ahora va dentro de la sección 'content' --}}
@section('content')
    <h1>Reporte de Ventas del Día</h1>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ventas realizadas hoy: {{ \Carbon\Carbon::today('America/Lima')->format('d/m/Y') }}</h3>
        </div>
        <div class="card-body">
            @if($sales->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th class="text-right">Total (S/)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                <td>
                                    {{-- Usando clases de Bootstrap 5 para badges --}}
                                    @if ($sale->status == 'VALID')
                                        <span class="badge bg-success">Válida</span>
                                    @elseif ($sale->status == 'CANCELLED')
                                        <span class="badge bg-danger">Anulada</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $sale->status }}</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($sale->total, 2) }}</td>
                                <td>
                                    {{-- Usando iconos Bootstrap --}}
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-sm btn-danger" title="Ver PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total del Día:</strong></td>
                            <td class="text-right"><strong>S/ {{ number_format($total, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @else
                <div class="alert alert-info">
                    No se encontraron ventas para el día de hoy.
                </div>
            @endif
        </div>
    </div>
@endsection {{-- Cambiado de @stop a @endsection (más estándar) --}}

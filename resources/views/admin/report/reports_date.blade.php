@extends('layouts.admin') {{-- Usando tu layout personalizado --}}

@section('title', 'Reporte de Ventas por Fechas')

{{-- El título ahora va dentro de la sección 'content' --}}
@section('content')
    <h1>Reporte de Ventas por Rango de Fechas</h1>

    {{-- Formulario para seleccionar fechas --}}
    <div class="card card-primary mb-4"> {{-- Añadido margen inferior --}}
        <div class="card-header">
            <h3 class="card-title">Seleccionar Rango</h3>
        </div>
        <form action="{{ route('report.results') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row align-items-end"> {{-- Alinea verticalmente al final --}}
                    <div class="col-md-5 mb-3 mb-md-0"> {{-- Añadido margen inferior en móvil --}}
                        <label for="fecha_ini" class="form-label">Fecha Inicial</label>
                        {{-- Mantenemos el valor si ya se hizo una búsqueda --}}
                        <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" value="{{ isset($fecha_ini) ? $fecha_ini->format('Y-m-d') : old('fecha_ini', \Carbon\Carbon::today('America/Lima')->format('Y-m-d')) }}" required>
                        @error('fecha_ini')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0"> {{-- Añadido margen inferior en móvil --}}
                        <label for="fecha_fin" class="form-label">Fecha Final</label>
                        {{-- Mantenemos el valor si ya se hizo una búsqueda --}}
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ isset($fecha_fin) ? $fecha_fin->format('Y-m-d') : old('fecha_fin', \Carbon\Carbon::today('America/Lima')->format('Y-m-d')) }}" required>
                         @error('fecha_fin')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Consultar</button> {{-- w-100 para ancho completo --}}
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Sección para mostrar resultados (solo si existen) --}}
    @isset($sales)
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Resultados para el rango: {{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</h3>
                @if($sales->count())
                    <button id="exportReportPdfButton" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-pdf me-1"></i> Exportar a PDF</button>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($sales->count())
                <div class="table-responsive">
                    <table id="salesReportTable" class="table table-striped table-hover align-middle">
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
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                    {{-- El PDF individual se genera desde la vista show de sales --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total del Periodo:</strong></td>
                            <td class="text-right"><strong>S/ {{ number_format($total, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @else
                <div class="alert alert-warning">
                    No se encontraron ventas para el rango de fechas seleccionado.
                </div>
            @endif
        </div>
    </div>
    @endisset
@endsection {{-- Cambiado de @stop a @endsection --}}

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportReportPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const fechaIni = "{{ isset($fecha_ini) ? $fecha_ini->format('d/m/Y') : '' }}";
            const fechaFin = "{{ isset($fecha_fin) ? $fecha_fin->format('d/m/Y') : '' }}";
            const title = `Reporte de Ventas (${fechaIni} - ${fechaFin})`;

            doc.setFontSize(18);
            doc.text(title, 14, 22);
            doc.autoTable({
                html: '#salesReportTable',
                startY: 30,
            });
            doc.save(`reporte_ventas_${fechaIni.replace(/\//g, '-')}_${fechaFin.replace(/\//g, '-')}.pdf`);
        });
    }
});
</script>
@endpush

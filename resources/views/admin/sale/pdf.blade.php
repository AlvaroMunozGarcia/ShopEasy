<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Detalle de Venta #{{ $sale->id }}</title>
    {{-- Estilos básicos para impresión --}}
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; font-size: 10pt; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h1, h2, h3, h4 { margin-top: 0; margin-bottom: 10px; color: #222; }
        h1 { font-size: 18pt; }
        h2 { font-size: 14pt; }
        h3 { font-size: 12pt; }
        h4 { font-size: 10pt; font-weight: bold; }
        .total-section td { font-weight: bold; }
        .no-print { margin-bottom: 15px; } /* Estilo para el contenedor del botón */

        .header-layout-table { width: 100%; margin-bottom: 20px; border: none; }
        .header-layout-table td { width: 50%; vertical-align: top; border: none; padding: 0 5px; }
        .header-layout-table td:first-child { padding-left: 0; }
        .header-layout-table td:last-child { padding-right: 0; }

        @media print {
            .no-print { display: none; } /* Oculta el botón al imprimir */
            body { margin: 0; } /* Ajusta márgenes para impresión si es necesario */
        }
    </style>
</head>
<body>
    {{-- Botón para imprimir/guardar y enlace para volver --}}
    <div class="no-print">
        <button onclick="window.print();">Imprimir / Guardar como PDF</button>
        <a href="{{ route('sales.show', $sale) }}" style="margin-left: 10px;">Volver a Detalles</a>
    </div>

    <h1>Detalle de Venta #{{ $sale->id }}</h1>

    <table class="header-layout-table">
        <tr>
            <td>
                <h4>Cliente</h4>
                <p><strong>Nombre:</strong> {{ $sale->client->name ?? 'N/A' }}</p>
                <p><strong>{{ $sale->client->dni ? 'DNI' : ($sale->client->ruc ? 'RUC' : 'ID') }}:</strong> {{ $sale->client->dni ?? $sale->client->ruc ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $sale->client->email ?? 'N/A' }}</p>
                <p><strong>Teléfono:</strong> {{ $sale->client->phone ?? 'N/A' }}</p>
            </td>
            <td>
                <h4>Información General</h4>
                <p><strong>Fecha:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : 'N/A' }}</p>
                <p><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</p>
                <p><strong>Impuesto Aplicado:</strong> {{ $sale->tax }}%</p>
                <p><strong>Estado:</strong>
                    @if($sale->status == 'VALID') VÁLIDA
                    @elseif($sale->status == 'CANCELLED') ANULADA
                    @else {{ Str::title(str_replace('_', ' ', $sale->status)) }}
                    @endif
                </p>
            </td>
        </tr>
    </table>

    <h2>Productos Vendidos</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit. (S/)</th>
                <th class="text-center">Desc. (%)</th>
                <th class="text-right">Subtotal (S/)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sale->saleDetails as $detail)
                @php
                    $lineSubtotal = ($detail->quantity * $detail->price) * (1 - $detail->discount / 100);
                @endphp
                <tr>
                    <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-right">{{ number_format($detail->price, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->discount, 2) }}%</td>
                    <td class="text-right">{{ number_format($lineSubtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay productos en esta venta.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="total-section">
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">S/ {{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Impuesto ({{ $sale->tax }}%):</strong></td>
                {{-- Calcula el impuesto basado en el subtotal calculado --}}
                <td class="text-right">S/ {{ number_format($subtotal * ($sale->tax / 100), 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                {{-- Muestra el total guardado en la venta --}}
                <td class="text-right"><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

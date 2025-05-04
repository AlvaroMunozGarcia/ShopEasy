<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Compra #{{ $purchase->id }}</title>
    {{-- Puedes incluir un CSS básico o específico para impresión --}}
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .no-print { display: block; margin-bottom: 20px; } /* Ocultar botón al imprimir */
        .text-right { text-align: right; }
        h1, h2, h3, h4 { margin-bottom: 15px; }

        @media print {
            .no-print { display: none; } /* Oculta el botón al imprimir */
            body { margin: 0; } /* Ajusta márgenes para impresión */
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print();">Imprimir / Guardar como PDF</button>
        <a href="{{ route('purchases.show', $purchase) }}">Volver a Detalles</a>
    </div>

    <h1>Detalle de Compra #{{ $purchase->id }}</h1>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <h4>Proveedor</h4>
            <p><strong>Nombre:</strong> {{ $purchase->provider->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $purchase->provider->email ?? 'N/A' }}</p>
            <p><strong>Teléfono:</strong> {{ $purchase->provider->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <h4>Información General</h4>
            <p><strong>Fecha:</strong> {{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
            <p><strong>Usuario:</strong> {{ $purchase->user->name ?? 'N/A' }}</p>
            <p><strong>Impuesto:</strong> {{ $purchase->tax }}%</p>
        </div>
    </div>

    <h2>Productos</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit. (S/)</th>
                <th>Subtotal (S/)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchase->purchaseDetails as $detail)
                <tr>
                    <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td class="text-right">{{ number_format($detail->price, 2) }}</td>
                    <td class="text-right">{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No hay productos en esta compra.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">S/ {{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Impuesto ({{ $purchase->tax }}%):</strong></td>
                <td class="text-right">S/ {{ number_format($purchase->total - $subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>S/ {{ number_format($purchase->total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
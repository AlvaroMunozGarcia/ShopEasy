<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Imprimir Compra #{{ $purchase->id }}</title>
    {{-- Puedes incluir un CSS básico o específico para impresión --}}
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
        .no-print { margin-bottom: 15px; }

        .header-layout-table { width: 100%; margin-bottom: 20px; border: none; }
        .header-layout-table td { width: 50%; vertical-align: top; border: none; padding: 0 5px; }
        .header-layout-table td:first-child { padding-left: 0; }
        .header-layout-table td:last-child { padding-right: 0; }
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

    <table class="header-layout-table">
        <tr>
            <td>
                <h4>Proveedor</h4>
                <p><strong>Nombre:</strong> {{ $purchase->provider->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $purchase->provider->email ?? 'N/A' }}</p>
                <p><strong>Teléfono:</strong> {{ $purchase->provider->phone ?? 'N/A' }}</p>
            </td>
            <td>
                <h4>Información General</h4>
                <p><strong>Fecha:</strong> {{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
                <p><strong>Usuario:</strong> {{ $purchase->user->name ?? 'N/A' }}</p>
                <p><strong>Impuesto:</strong> {{ $purchase->tax }}%</p>
                <p><strong>Estado:</strong> {{ Str::title(str_replace('_', ' ', $purchase->status ?? 'RECIBIDA')) }}</p> {{-- Asumiendo un campo status, con 'RECIBIDA' como default si no existe --}}
            </td>
        </tr>
    </table>

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
                    <td colspan="4" class="text-center">No hay productos en esta compra.</td>
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
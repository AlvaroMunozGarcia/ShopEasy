{{-- resources/views/admin/sale/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detalles de la Venta')

@section('page_header')
    Detalles de la Venta <span class="text-muted">#{{ $sale->id }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper">
    {{-- La cabecera anterior con H1 y breadcrumbs se elimina,
         ya que @page_header y @breadcrumbs del layout principal se encargarán de esto. --}}

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">Información de la Venta #{{ $sale->id }}</h3>
                            <div class="card-tools">
                                <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                                </button>
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
                                    {{-- Puedes añadir más detalles del cliente si los tienes y son relevantes --}}
                                    {{-- <p><strong>Email Cliente:</strong> {{ $sale->client->email ?? 'N/A' }}</p> --}}
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <p><strong>ID Venta:</strong> {{ $sale->id }}</p>
                                    <p><strong>Fecha de Venta:</strong> {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
                                    <p><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</p>
                                    <p><strong>Impuesto (%):</strong> {{ $sale->tax }}%</p>
                                    <h4><strong>Total Pagado:</strong> {{ number_format($sale->total, 2, ',', '.') }} €</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Venta</h4>
                            <div class="table-responsive">
                                <table id="saleDetailsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">Producto</th>
                                            <th style="width: 15%;">Código</th>
                                            <th class="text-center" style="width: 10%;">Cantidad</th>
                                            <th class="text-end" style="width: 15%;">Precio Unit. (€)</th>
                                            <th class="text-center" style="width: 10%;">Desc. (%)</th>
                                            <th class="text-end" style="width: 15%;">Subtotal (€)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subtotalGeneral = 0; @endphp
                                        @forelse ($sale->saleDetails as $detail)
                                            @php
                                                $lineSubtotal = ($detail->quantity * $detail->price) * (1 - ($detail->discount ?? 0) / 100);
                                                $subtotalGeneral += $lineSubtotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                                <td>{{ $detail->product->code ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->price, 2, ',', '.') }} €</td>
                                                <td class="text-center">{{ $detail->discount ?? 0 }}%</td>
                                                <td class="text-end">{{ number_format($lineSubtotal, 2, ',', '.') }} €</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No hay productos en esta venta.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                                            <td class="text-end">{{ number_format($subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>Impuesto ({{ $sale->tax }}%):</strong></td>
                                            <td class="text-end">{{ number_format($sale->total - $subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($sale->total, 2, ',', '.') }} €</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- El footer puede quedar vacío o para acciones adicionales si las hubiera --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
    // Función para obtener el nombre de archivo personalizado
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        // Formato de fecha YYYY-MM-DD
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;

        // Mostrar prompt al usuario
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);

        // Si el usuario cancela, devuelve null
        if (userFilename === null) {
            return null;
        }

        // Usar el nombre del usuario si no está vacío, de lo contrario usar el por defecto
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();

        // Asegurarse de que la extensión esté presente
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }
        return finalFilename;
    }
document.addEventListener('DOMContentLoaded', function () {
    // Solo un botón de exportación ahora, en el card-header
    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', exportSaleDetailsToPDF);
});

function exportSaleDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Información general de la venta
    const generalData = [
        ['ID Venta', '{{ $sale->id }}'],
        ['Cliente', '{{ $sale->client->name ?? "N/A" }}'],
        ['Fecha de Venta', '{{ $sale->sale_date->format("d/m/Y H:i") }}'],
        ['Vendedor', '{{ $sale->user->name ?? "N/A" }}'],
        ['Impuesto (%)', '{{ $sale->tax }}%'],
        ['Total Pagado', '{{ number_format($sale->total, 2, ",", ".") }} €']
    ];

    doc.setFontSize(14);
    doc.text('Detalles de la Venta', 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: generalData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] }, // Azul
        columnStyles: {
            0: { fontStyle: 'bold', cellWidth: 60 },
            1: { cellWidth: 120 }
        }
    });

    // Tabla de productos
    const productData = [
        @foreach ($sale->saleDetails as $detail)
            [
                '{{ $detail->product->name ?? "Producto no encontrado" }}',
                '{{ $detail->product->code ?? "N/A" }}',
                '{{ $detail->quantity }}',
                '{{ number_format($detail->price, 2, ",", ".") }} €',
                '{{ $detail->discount ?? 0 }}%',
                '{{ number_format(($detail->quantity * $detail->price) * (1 - ($detail->discount ?? 0) / 100), 2, ",", ".") }} €'
            ],
        @endforeach
    ];

    doc.text('Productos Incluidos en la Venta', 14, doc.lastAutoTable.finalY + 10);
    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 15,
        head: [['Producto', 'Código', 'Cantidad', 'Precio Unit. (€)', 'Desc. (%)', 'Subtotal (€)']],
        body: productData,
        styles: { fontSize: 9 }, // Un poco más pequeño para que quepa mejor
        headStyles: { fillColor: [52, 152, 219] }, // Azul más claro
        theme: 'striped'
    });

    // Totales al final (ya están en la tabla HTML, pero para el PDF los replicamos si es necesario o los tomamos de la tabla)
    // Para simplificar, los tomamos de los valores ya calculados en PHP para el tfoot
    const subtotalGeneralPDF = {{ number_format($subtotalGeneral, 2, '.', '') }};
    const impuestoPDF = {{ number_format($sale->total - $subtotalGeneral, 2, '.', '') }};
    const totalPDF = {{ number_format($sale->total, 2, '.', '') }};

    // (Opcional) Añadir los totales como otra tabla o texto si se desea un formato diferente al de la tabla de productos.
    // Por ahora, los totales ya están visualmente en la tabla de productos del HTML y se reflejarían si se exporta la tabla entera.
    // El script de purchase.show añade una tabla separada para totales, lo replicaremos.
    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 10,
        body: [
            [{ content: 'Subtotal', styles: { halign: 'right', fontStyle: 'bold' } }, '{{ number_format($subtotalGeneral, 2, ",", ".") }} €'],
            [{ content: 'Impuesto ({{ $sale->tax }}%)', styles: { halign: 'right', fontStyle: 'bold' } }, '{{ number_format($sale->total - $subtotalGeneral, 2, ",", ".") }} €'],
            [{ content: 'TOTAL', styles: { halign: 'right', fontStyle: 'bold', fillColor: [52, 152, 219], textColor: [255, 255, 255] } }, '{{ number_format($sale->total, 2, ",", ".") }} €']
        ],
        theme: 'plain',
        styles: { fontSize: 11 },
        columnStyles: {
            0: { cellWidth: 130 + 15 + 15 }, // Ajustar cellWidth para que la segunda columna se alinee bien
            1: { cellWidth: 30, halign: 'right' }
        }
    });

    // Usar el ID de la venta para el nombre base y llamar a getCustomFilename
    const baseFilename = `venta_{{ $sale->id }}`;
    const filename = getCustomFilename(baseFilename, 'pdf');
    if (filename) { // Guardar solo si el usuario no canceló
        doc.save(filename);
    }
}
</script>
@endpush

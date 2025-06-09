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
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0">Información de la Venta #{{ $sale->id }}</h5>
                        <div class="mt-2 mt-md-0">
                                <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                                </button>
                                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-arrow-left-circle"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Cliente:</strong> {{ $sale->client->name ?? 'N/A' }}</p>
                                    <p><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p><strong>Fecha de Venta:</strong> {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
                                    <p><strong>Impuesto (%):</strong> {{ $sale->tax }}%</p>
                                    <h4><strong>Total Pagado:</strong> {{ number_format($sale->total, 2, ',', '.') }} €</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Venta</h4>
                            <div class="table-responsive">
                                <table id="saleDetailsTable" class="table table-bordered table-hover table-sm">
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
                                            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end">{{ number_format($subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Impuesto ({{ $sale->tax }}%):</strong></td>
                                            <td class="text-end">{{ number_format($sale->total - $subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($sale->total, 2, ',', '.') }} €</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- El card-footer puede eliminarse si no tiene contenido --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);
        if (userFilename === null) {
            return null;
        }
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }
        return finalFilename;
    }
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', exportSaleDetailsToPDF);
});

function exportSaleDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
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
        styles: { fontSize: 9 }, 
        headStyles: { fillColor: [52, 152, 219] }, 
        theme: 'striped'
    });
    const subtotalGeneralPDF = {{ number_format($subtotalGeneral, 2, '.', '') }};
    const impuestoPDF = {{ number_format($sale->total - $subtotalGeneral, 2, '.', '') }};
    const totalPDF = {{ number_format($sale->total, 2, '.', '') }};
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
            0: { cellWidth: 130 + 15 + 15 }, 
            1: { cellWidth: 30, halign: 'right' }
        }
    });
    const baseFilename = `venta_{{ $sale->id }}`;
    const filename = getCustomFilename(baseFilename, 'pdf');
    if (filename) { 
        doc.save(filename);
    }
}
</script>
@endpush

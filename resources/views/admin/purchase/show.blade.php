@extends('layouts.admin') 

@section('title', 'Detalles de Compra') 

@section('page_header')
    Detalles de la Compra <span class="text-muted">#{{ $purchase->id }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalle Compra #{{ $purchase->id }}</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0">Información de la Compra #{{ $purchase->id }}</h5>
                        <div class="mt-2 mt-md-0">
                                <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                                </button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-arrow-left-circle"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Proveedor:</strong> {{ $purchase->provider->name ?? 'N/A' }}</p>
                                    <p><strong>Email Proveedor:</strong> {{ $purchase->provider->email ?? 'N/A' }}</p>
                                    <p><strong>Teléfono Proveedor:</strong> {{ $purchase->provider->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <p><strong>Fecha de Compra:</strong> {{ $purchase->purchase_date->format('d/m/Y H:i') }}</p>
                                    <p><strong>Usuario Registrador:</strong> {{ $purchase->user->name ?? 'N/A' }}</p>
                                    <p><strong>Impuesto (%):</strong> {{ $purchase->tax }}%</p>
                                    <h4><strong>Total Pagado:</strong> {{ number_format($purchase->total, 2, ',', '.') }} €</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Compra</h4>
                            <div class="table-responsive">
                                <table id="purchaseDetailsTable" class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Producto</th>
                                            <th class="text-center" style="width: 15%;">Cantidad</th>
                                            <th class="text-end" style="width: 20%;">Precio Unitario (€)</th>
                                            <th class="text-end" style="width: 25%;">Subtotal (€)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchase->purchaseDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->price, 2, ',', '.') }} €</td>
                                                <td class="text-end">{{ number_format($detail->quantity * $detail->price, 2, ',', '.') }} €</td>
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
                                            @php
                                                $subtotalGeneral = $purchase->total / (1 + ($purchase->tax / 100));
                                            @endphp
                                            <td class="text-end">{{ number_format($subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Impuesto ({{ $purchase->tax }}%):</strong></td>
                                            <td class="text-end">{{ number_format($purchase->total - $subtotalGeneral, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($purchase->total, 2, ',', '.') }} €</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- El card-footer puede eliminarse si no tiene contenido o acciones específicas aquí --}}
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
    document.getElementById('exportDetailPdfButtonTrigger').addEventListener('click', exportPurchaseDetailsToPDF);
});

function exportPurchaseDetailsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const generalData = [
        ['ID Compra', '{{ $purchase->id }}'],
        ['Proveedor', '{{ $purchase->provider->name ?? "N/A" }}'],
        ['Email Proveedor', '{{ $purchase->provider->email ?? "N/A" }}'],
        ['Teléfono Proveedor', '{{ $purchase->provider->phone ?? "N/A" }}'],
        ['Fecha de Compra', '{{ $purchase->purchase_date->format("d/m/Y H:i") }}'],
        ['Usuario Registrador', '{{ $purchase->user->name ?? "N/A" }}'],
        ['Impuesto (%)', '{{ $purchase->tax }}%'],
        ['Total Pagado', '{{ number_format($purchase->total, 2, ",", ".") }} €']
    ];

    doc.setFontSize(14);
    doc.text('Detalles de la Compra', 14, 20);

    doc.autoTable({
        startY: 30,
        head: [['Campo', 'Valor']],
        body: generalData,
        styles: { fontSize: 11 },
        headStyles: { fillColor: [41, 128, 185] },
        columnStyles: {
            0: { fontStyle: 'bold', cellWidth: 60 },
            1: { cellWidth: 120 }
        }
    });
    const productData = [
        @foreach ($purchase->purchaseDetails as $detail)
            [
                '{{ $detail->product->name ?? "Producto no encontrado" }}',
                '{{ $detail->quantity }}',
                '{{ number_format($detail->price, 2, ",", ".") }} €',
                '{{ number_format($detail->quantity * $detail->price, 2, ",", ".") }} €'
            ],
        @endforeach
    ];

    doc.text('Productos Incluidos en la Compra', 14, doc.lastAutoTable.finalY + 10);
    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 15,
        head: [['Producto', 'Cantidad', 'Precio Unitario (€)', 'Subtotal (€)']],
        body: productData,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [52, 152, 219] },
        theme: 'striped'
    });
    const subtotal = {{ number_format($subtotalGeneral, 2, '.', '') }};
    const impuesto = {{ number_format($purchase->total - $subtotalGeneral, 2, '.', '') }};
    const total = {{ number_format($purchase->total, 2, '.', '') }};

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 10,
        body: [
            [{ content: 'Subtotal', styles: { halign: 'right', fontStyle: 'bold' } }, '{{ number_format($subtotalGeneral, 2, ",", ".") }} €'],
            [{ content: 'Impuesto ({{ $purchase->tax }}%)', styles: { halign: 'right', fontStyle: 'bold' } }, '{{ number_format($purchase->total - $subtotalGeneral, 2, ",", ".") }} €'],
            [{ content: 'TOTAL', styles: { halign: 'right', fontStyle: 'bold', fillColor: [52, 152, 219], textColor: [255, 255, 255] } }, '{{ number_format($purchase->total, 2, ",", ".") }} €']
        ],
        theme: 'plain',
        styles: { fontSize: 11 },
        columnStyles: {
            0: { cellWidth: 130 },
            1: { cellWidth: 50, halign: 'right' }
        }
    });
    const baseFilename = `compra_{{ $purchase->id }}`;
    const filename = getCustomFilename(baseFilename, 'pdf');
    if (filename) {
        doc.save(filename);
    }
}
</script>
@endpush

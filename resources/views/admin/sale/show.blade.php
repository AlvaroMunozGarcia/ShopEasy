{{-- resources/views/admin/sale/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Detalles de la Venta #{{ $sale->id }}</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Información General</span>
            <div>
                <button id="exportDetailPdfButton" class="btn btn-sm btn-info">
                    <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Venta</dt>
                <dd class="col-sm-9">{{ $sale->id }}</dd>

                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">{{ $sale->sale_date->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $sale->client->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Vendedor</dt>
                <dd class="col-sm-9">{{ $sale->user->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9">{{ number_format($sale->total, 2, ',', '.') }} €</dd>
            </dl>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Productos Incluidos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="saleDetailsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                            <th>Código de Barras (Texto)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                <td>{{ $detail->product->code ?? 'N/A' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->price, 2, ',', '.') }} €</td>
                                <td>{{ number_format($detail->quantity * $detail->price, 2, ',', '.') }} €</td>
                                <td>
                                    {{-- Mostrar el código de barras como texto. La representación gráfica es compleja para jspdf-autotable directamente desde HTML. --}}
                                    {!! $barcodes[$detail->product->id] ?? '<span class="text-danger">Error al generar</span>' !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
             <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la lista
            </a>
            <button id="exportDetailPdfButtonFooter" class="btn btn-info float-end">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function generatePdf() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let yPos = 15;

        doc.setFontSize(18);
        doc.text("Detalles de Venta #{{ $sale->id }}", 14, yPos);
        yPos += 10;

        doc.setFontSize(12);
        doc.text("Información General:", 14, yPos); yPos += 7;
        doc.setFontSize(10);
        doc.text("ID Venta: {{ $sale->id }}", 14, yPos); yPos += 6;
        doc.text("Fecha: {{ $sale->sale_date->format('d/m/Y H:i') }}", 14, yPos); yPos += 6;
        doc.text("Cliente: {{ $sale->client->name ?? 'N/A' }}", 14, yPos); yPos += 6;
        doc.text("Vendedor: {{ $sale->user->name ?? 'N/A' }}", 14, yPos); yPos += 6;
        doc.setFontSize(12);
        doc.text("Total: {{ number_format($sale->total, 2, ',', '.') }} €", 14, yPos); yPos += 10;

        doc.setFontSize(12);
        doc.text("Productos Incluidos:", 14, yPos); yPos +=2;

        doc.autoTable({
            html: '#saleDetailsTable',
            startY: yPos,
            theme: 'grid',
            headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
            // Para la columna de código de barras, si es SVG, autoTable podría no renderizarlo bien.
            // Se mostrará el contenido textual de la celda.
            // Si necesitas imágenes de códigos de barras, sería un proceso más complejo:
            // 1. Convertir el SVG/HTML del código de barras a una imagen (PNG/JPEG) en el cliente.
            // 2. Añadir esa imagen a la celda del PDF usando doc.addImage() en los hooks de autoTable.
            // Por simplicidad, aquí se asume que el contenido textual es suficiente.
            columnStyles: {
                5: { cellWidth: 'wrap' } // Para la columna de código de barras, intentar ajustar el ancho
            }
        });
        doc.save('detalle_venta_{{ $sale->id }}.pdf');
    }
    const exportButtonHeader = document.getElementById('exportDetailPdfButton');
    const exportButtonFooter = document.getElementById('exportDetailPdfButtonFooter');

    if(exportButtonHeader) exportButtonHeader.addEventListener('click', generatePdf);
    if(exportButtonFooter) exportButtonFooter.addEventListener('click', generatePdf);
});
</script>
@endpush

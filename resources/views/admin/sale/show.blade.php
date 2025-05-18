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
<div class="container-fluid">
    {{-- El H1 anterior se elimina ya que @page_header lo maneja --}}

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Información General</span>
            <div>
                <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info">
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
            <button id="exportDetailPdfButtonFooterTrigger" class="btn btn-info float-end">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </div>
    </div>

    {{-- Modal for PDF Export Options --}}
    <div class="modal fade" id="pdfDetailExportModal" tabindex="-1" aria-labelledby="pdfDetailExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfDetailExportModalLabel">Exportar Detalles a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfDetailFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfDetailFilenameInput" placeholder="nombre_archivo.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfDetailExportBtn">Confirmar y Exportar</button>
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
document.addEventListener('DOMContentLoaded', function () {
    const pdfDetailModalEl = document.getElementById('pdfDetailExportModal');
    const pdfDetailModal = pdfDetailModalEl ? new bootstrap.Modal(pdfDetailModalEl) : null;
    const pdfDetailFilenameInput = document.getElementById('pdfDetailFilenameInput');

    function exportSaleDetailsToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const saleId = "{{ $sale->id }}";
            const defaultFilename = `detalle_venta_${saleId}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles de Venta #${saleId}`, 14, yPos);
            yPos += 10;

            doc.setFontSize(12);
            doc.text("Información General:", 14, yPos); yPos += 7;
            doc.setFontSize(10);
            doc.text(`ID Venta: ${saleId}`, 14, yPos); yPos += 6;
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
                columnStyles: {
                    5: { cellWidth: 'wrap' }
                }
            });
            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles de venta:", error);
            alert("Error al generar PDF de detalles de venta. Verifique la consola para más detalles.");
        }
    }

    function openPdfModal() {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const saleId = "{{ $sale->id }}";
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_venta_${saleId}_${todayForFilename}.pdf`;
            pdfDetailModal.show();
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', openPdfModal);
    document.getElementById('exportDetailPdfButtonFooterTrigger')?.addEventListener('click', openPdfModal);

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportSaleDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush

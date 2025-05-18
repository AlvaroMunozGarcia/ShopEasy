{{-- Asumiendo que tienes un layout base como 'layouts.admin' --}}
@extends('layouts.admin') {{-- Cambia 'layouts.admin' por tu layout principal si es diferente --}}

@section('title', 'Detalles de Compra') {{-- Título de la página --}}

@section('page_header')
    Detalles de la Compra <span class="text-muted">#{{ $purchase->id }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalle Compra #{{ $purchase->id }}</li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información de la Compra #{{ $purchase->id }}</h3>
                            <div class="card-tools">
                                <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                                </button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
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
                                    <h4><strong>Total Pagado:</strong> S/ {{ number_format($purchase->total, 2) }}</h4>
                                </div>
                            </div>

                            <hr>

                            <h4>Detalles de la Compra</h4>
                            <div class="table-responsive">
                                <table id="purchaseDetailsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario (S/)</th>
                                            <th>Subtotal (S/)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchase->purchaseDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ number_format($detail->price, 2) }}</td>
                                                <td>{{ number_format($detail->quantity * $detail->price, 2) }}</td>
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
                                            <td>S/ {{ number_format($subtotalGeneral, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Impuesto ({{ $purchase->tax }}%):</strong></td>
                                            <td>S/ {{ number_format($purchase->total - $subtotalGeneral, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                            <td><strong>S/ {{ number_format($purchase->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Volver al Listado</a>
                            <button id="exportDetailPdfButtonFooterTrigger" class="btn btn-info float-right">
                                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                            </button>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

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
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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

    function exportPurchaseDetailsToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const purchaseId = "{{ $purchase->id }}";
            const defaultFilename = `detalle_compra_${purchaseId}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles de Compra #${purchaseId}`, 14, yPos);
            yPos += 10;

            doc.setFontSize(12);
            doc.text("Información General:", 14, yPos); yPos += 7;
            doc.setFontSize(10);
            doc.text(`Proveedor: {{ $purchase->provider->name ?? 'N/A' }}`, 14, yPos); yPos += 6;
            doc.text(`Email Proveedor: {{ $purchase->provider->email ?? 'N/A' }}`, 14, yPos); yPos += 6;
            doc.text(`Teléfono Proveedor: {{ $purchase->provider->phone ?? 'N/A' }}`, 14, yPos); yPos += 6;
            doc.text(`Fecha de Compra: {{ $purchase->purchase_date->format('d/m/Y H:i') }}`, 14, yPos); yPos += 6;
            doc.text(`Usuario Registrador: {{ $purchase->user->name ?? 'N/A' }}`, 14, yPos); yPos += 6;
            doc.text(`Impuesto (%): {{ $purchase->tax }}%`, 14, yPos); yPos += 6;
            doc.setFontSize(12);
            doc.text(`Total Pagado: S/ {{ number_format($purchase->total, 2) }}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            doc.text("Detalles de la Compra:", 14, yPos); yPos += 2;

            doc.autoTable({
                html: '#purchaseDetailsTable',
                startY: yPos,
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
            });

            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles de compra:", error);
            alert("Error al generar PDF de detalles de compra. Verifique la consola para más detalles.");
        }
    }

    function openPdfModal() {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const purchaseId = "{{ $purchase->id }}";
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_compra_${purchaseId}_${todayForFilename}.pdf`;
            pdfDetailModal.show();
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', openPdfModal);
    document.getElementById('exportDetailPdfButtonFooterTrigger')?.addEventListener('click', openPdfModal);

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportPurchaseDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush

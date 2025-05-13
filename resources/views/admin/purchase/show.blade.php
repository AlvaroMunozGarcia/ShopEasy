{{-- Asumiendo que tienes un layout base como 'layouts.admin' --}}
@extends('layouts.admin') {{-- Cambia 'layouts.admin' por tu layout principal si es diferente --}}

@section('title', 'Detalles de Compra') {{-- Título de la página --}}

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalles de Compra</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
                        <li class="breadcrumb-item active">Detalle Compra #{{ $purchase->id }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información de la Compra #{{ $purchase->id }}</h3>
                            <div class="card-tools">
                                <button id="exportDetailPdfButton" class="btn btn-sm btn-info">
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
                            <button id="exportDetailPdfButtonFooter" class="btn btn-info float-right">
                                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                            </button>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
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
    function generatePdf() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let yPos = 15; // Posición Y inicial para el texto

        // Título del Documento
        doc.setFontSize(18);
        doc.text("Detalles de Compra #{{ $purchase->id }}", 14, yPos);
        yPos += 10; // Espacio después del título

        // Información General de la Compra
        doc.setFontSize(12);
        doc.text("Información General:", 14, yPos);
        yPos += 7; // Espacio
        doc.setFontSize(10);
        doc.text(`Proveedor: {{ $purchase->provider->name ?? 'N/A' }}`, 14, yPos);
        yPos += 6;
        doc.text(`Email Proveedor: {{ $purchase->provider->email ?? 'N/A' }}`, 14, yPos);
        yPos += 6;
        doc.text(`Teléfono Proveedor: {{ $purchase->provider->phone ?? 'N/A' }}`, 14, yPos);
        yPos += 6;
        doc.text(`Fecha de Compra: {{ $purchase->purchase_date->format('d/m/Y H:i') }}`, 14, yPos);
        yPos += 6;
        doc.text(`Usuario Registrador: {{ $purchase->user->name ?? 'N/A' }}`, 14, yPos);
        yPos += 6;
        doc.text(`Impuesto (%): {{ $purchase->tax }}%`, 14, yPos);
        yPos += 6;
        doc.setFontSize(12); // Resaltar el total
        doc.text(`Total Pagado: S/ {{ number_format($purchase->total, 2) }}`, 14, yPos);
        yPos += 10; // Espacio antes de la tabla

        // Título para la tabla de detalles
        doc.setFontSize(12);
        doc.text("Detalles de la Compra:", 14, yPos);
        yPos += 2; // Pequeño ajuste para que autoTable no sobreescriba el título

        // Tabla de Detalles de la Compra
        doc.autoTable({
            html: '#purchaseDetailsTable',
            startY: yPos,
            theme: 'grid', // 'striped', 'grid', 'plain'
            headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' }, // Azul oscuro para cabecera
            // footStyles: { fillColor: [211, 211, 211], textColor: 0, fontStyle: 'bold' }, // Estilo para el pie de tabla si se usa
            // didDrawPage: function (data) { // Opcional: para añadir pie de página en cada página
            //     doc.setFontSize(10);
            //     doc.text('Página ' + doc.internal.getNumberOfPages(), data.settings.margin.left, doc.internal.pageSize.height - 10);
            // }
        });

        doc.save('detalle_compra_{{ $purchase->id }}.pdf');
    }

    const exportButtonHeader = document.getElementById('exportDetailPdfButton');
    const exportButtonFooter = document.getElementById('exportDetailPdfButtonFooter');

    if (exportButtonHeader) exportButtonHeader.addEventListener('click', generatePdf);
    if (exportButtonFooter) exportButtonFooter.addEventListener('click', generatePdf);
});
</script>
@endpush

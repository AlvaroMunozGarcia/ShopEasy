@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Reporte de Beneficios por Venta')

@section('page_header', 'Reporte de Beneficios por Venta')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active" aria-current="page">Reporte de Beneficios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Filtrar por Rango de Fechas</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.profit_results') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="fecha_ini">Fecha Inicial:</label>
                            <input type="date" name="fecha_ini" id="fecha_ini" class="form-control"
                                   value="{{ $fecha_ini->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Final:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                                   value="{{ $fecha_fin->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="bi bi-search"></i> Consultar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($sales))
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h3 class="card-title">Resultados del Reporte ({{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }})</h3>
                <div class="card-tools">
                    <button id="exportProfitReportPdfButton" class="btn btn-sm btn-info">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($sales->isEmpty())
                    <div class="alert alert-info" role="alert">
                        No se encontraron ventas con beneficios para el rango de fechas seleccionado.
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="profitReportTable" class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">P. Coste Unit.</th>
                                    <th class="text-end">P. Venta Unit. (Efectivo)</th>
                                    <th class="text-end">Beneficio Línea</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotalProfit = 0; @endphp
                                @foreach($sales as $sale)
                                    @php $firstDetail = true; @endphp
                                    @foreach($sale->saleDetails as $index => $detail)
                                        @php
                                            $effectiveSellPricePerUnit = $detail->price * (1 - ($detail->discount / 100));
                                            // El beneficio ya está calculado en $detail->profit
                                            // $lineProfit = ($effectiveSellPricePerUnit - ($detail->product->cost_price ?? 0)) * $detail->quantity;
                                            // $grandTotalProfit += $lineProfit;
                                        @endphp
                                        <tr>
                                            @if($firstDetail)
                                                <td rowspan="{{ $sale->saleDetails->count() }}">{{ $sale->id }}</td>
                                                <td rowspan="{{ $sale->saleDetails->count() }}">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                                <td rowspan="{{ $sale->saleDetails->count() }}">{{ $sale->client->name ?? 'N/A' }}</td>
                                                @php $firstDetail = false; @endphp
                                            @endif
                                            <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td class="text-end">{{ number_format($detail->product->cost_price ?? 0, 2, ',', '.') }} €</td>
                                            <td class="text-end">{{ number_format($effectiveSellPricePerUnit, 2, ',', '.') }} €</td>
                                            <td class="text-end">{{ number_format($detail->profit, 2, ',', '.') }} €</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="7" class="text-end"><strong>BENEFICIO TOTAL DEL PERÍODO:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($totalProfitPeriod, 2, ',', '.') }} €</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal fade" id="pdfProfitReportExportModal" tabindex="-1" aria-labelledby="pdfProfitReportExportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfProfitReportExportModalLabel">Exportar Reporte de Beneficios a PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Se exportará el reporte de beneficios para el rango de fechas:
                            <strong>{{ $fecha_ini->format('d/m/Y') }}</strong> - <strong>{{ $fecha_fin->format('d/m/Y') }}</strong>.
                        </p>
                        <div class="mb-3">
                            <label for="pdfProfitReportFilenameInput" class="form-label">Nombre del archivo:</label>
                            <input type="text" class="form-control" id="pdfProfitReportFilenameInput" placeholder="nombre_archivo.pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmPdfProfitReportExportBtn">Confirmar y Exportar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(isset($sales))
    const pdfModalEl = document.getElementById('pdfProfitReportExportModal');
    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
    const pdfFilenameInput = document.getElementById('pdfProfitReportFilenameInput');

    function exportReportToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
                console.error("jsPDF no está cargado.");
                alert("Error: jsPDF no está cargado.");
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4'); 
            let yPos = 40; 

            const fechaIni = "{{ $fecha_ini->format('d/m/Y') }}";
            const fechaFin = "{{ $fecha_fin->format('d/m/Y') }}";
            const defaultFilename = `reporte_beneficios_${fechaIni.replace(/\//g, '-')}_a_${fechaFin.replace(/\//g, '-')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(16);
            doc.text(`Reporte de Beneficios por Venta`, doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
            yPos += 20;
            doc.setFontSize(11);
            doc.text(`Período: ${fechaIni} - ${fechaFin}`, doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
            yPos += 30;
            const tableData = [];
            const tableHeaders = ["ID Venta", "Fecha", "Cliente", "Producto", "Cant.", "P. Coste U.", "P. Venta U.", "Beneficio Línea"];
            @foreach($sales as $sale)
                @foreach($sale->saleDetails as $detail)
                    @php
                        $effectiveSellPricePerUnit = $detail->price * (1 - ($detail->discount / 100));
                    @endphp
                    tableData.push([
                        "{{ $sale->id }}",
                        "{{ $sale->sale_date->format('d/m/Y') }}",
                        "{{ $sale->client->name ?? 'N/A' }}".replace(/€/g, 'EUR'), 
                        "{{ $detail->product->name ?? 'N/A' }}".replace(/€/g, 'EUR'),
                        "{{ $detail->quantity }}",
                        "{{ number_format($detail->product->cost_price ?? 0, 2, ',', '.') }} EUR",
                        "{{ number_format($effectiveSellPricePerUnit, 2, ',', '.') }} EUR",
                        "{{ number_format($detail->profit, 2, ',', '.') }} EUR"
                    ]);
                @endforeach
            @endforeach

            doc.autoTable({
                head: [tableHeaders],
                body: tableData,
                startY: yPos,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold', halign: 'center' },
                columnStyles: {
                    0: { halign: 'center', cellWidth: 50 }, // ID Venta
                    1: { halign: 'center', cellWidth: 60 }, // Fecha
                    2: { cellWidth: 'auto' },                // Cliente
                    3: { cellWidth: 'auto' },                // Producto
                    4: { halign: 'center', cellWidth: 40 },  // Cant.
                    5: { halign: 'right', cellWidth: 70 },   // P. Coste
                    6: { halign: 'right', cellWidth: 70 },   // P. Venta
                    7: { halign: 'right', cellWidth: 70 }    // Beneficio
                },
                didDrawPage: function (data) {
                    let footerStr = "Página " + doc.internal.getNumberOfPages();
                    doc.setFontSize(10);
                    doc.text(footerStr, data.settings.margin.left, doc.internal.pageSize.getHeight() - 10);
                }
            });

            yPos = doc.lastAutoTable.finalY + 20; 

            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text(`BENEFICIO TOTAL DEL PERÍODO: {{ number_format($totalProfitPeriod, 2, ',', '.') }} EUR`, 40, yPos, {align: 'left'});


            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF del reporte de beneficios:", error);
            alert("Error al generar PDF. Verifique la consola para más detalles.");
        }
    }

    function openPdfModal() {
        if (pdfModal && pdfFilenameInput) {
            const fechaIni = "{{ $fecha_ini->format('d-m-Y') }}";
            const fechaFin = "{{ $fecha_fin->format('d-m-Y') }}";
            pdfFilenameInput.value = `reporte_beneficios_${fechaIni}_a_${fechaFin}.pdf`;
            pdfModal.show();
        }
    }

    document.getElementById('exportProfitReportPdfButton')?.addEventListener('click', openPdfModal);

    document.getElementById('confirmPdfProfitReportExportBtn')?.addEventListener('click', function () {
        const filename = pdfFilenameInput ? pdfFilenameInput.value.trim() : null;
        exportReportToPdf(filename);
        if(pdfModal) pdfModal.hide();
    });
    @endif
});
</script>
@endpush

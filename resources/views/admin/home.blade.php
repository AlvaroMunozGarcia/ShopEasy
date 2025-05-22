@extends('layouts.admin') {{-- Asegúrate que este sea tu layout de admin correcto --}}

@section('title', 'Dashboard Principal')

@section('content')
<div class="content-wrapper"> {{-- Necesario si usas AdminLTE --}}
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Fila de Estadísticas Principales -->
      <div class="row">
        <!-- Card Ventas -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-info mb-1">Ventas Totales</h5>
                  <h2 class="mb-0">{{ $totalSales ?? 0 }}</h2>
                </div>
                <div class="text-info opacity-50">
                  <i class="fas fa-shopping-cart fa-3x"></i>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-right">
                 {{-- Asegúrate que 'sales.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-info">Ver Ventas</a>
            </div>
          </div>
        </div>

        <!-- Card Ingresos -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-success mb-1">Ingresos Totales</h5>
                   {{-- Ajusta el símbolo de moneda y formato si es necesario --}}
                  <h2 class="mb-0">S/ {{ number_format($totalRevenue ?? 0, 2) }}</h2>
                </div>
                <div class="text-success opacity-50">
                  <i class="fas fa-dollar-sign fa-3x"></i>
                </div>
              </div>
            </div>
             <div class="card-footer bg-transparent border-top-0 text-right">
                 {{-- Asegúrate que 'sales.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-success">Ver Ventas</a>
            </div>
          </div>
        </div>

        <!-- Card Compras -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-warning mb-1">Compras Totales</h5>
                  <h2 class="mb-0">{{ $totalPurchases ?? 0 }}</h2>
                </div>
                <div class="text-warning opacity-50">
                  <i class="fas fa-truck-loading fa-3x"></i>
                </div>
              </div>
            </div>
             <div class="card-footer bg-transparent border-top-0 text-right">
                  {{-- Asegúrate que 'purchases.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-warning">Ver Compras</a>
            </div>
          </div>
        </div>

        <!-- Card Clientes -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-danger mb-1">Clientes</h5>
                  <h2 class="mb-0">{{ $totalClients ?? 0 }}</h2>
                </div>
                <div class="text-danger opacity-50">
                  <i class="fas fa-users fa-3x"></i>
                </div>
              </div>
            </div>
             <div class="card-footer bg-transparent border-top-0 text-right">
                  {{-- Asegúrate que 'clients.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-danger">Ver Clientes</a>
            </div>
          </div>
        </div>

        <!-- Card Proveedores -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-primary mb-1">Proveedores</h5>
                  <h2 class="mb-0">{{ $totalProviders ?? 0 }}</h2>
                </div>
                <div class="text-primary opacity-50">
                  <i class="fas fa-parachute-box fa-3x"></i>
                </div>
              </div>
            </div>
             <div class="card-footer bg-transparent border-top-0 text-right">
                  {{-- Asegúrate que 'providers.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('providers.index') }}" class="btn btn-sm btn-outline-primary">Ver Proveedores</a>
            </div>
          </div>
        </div>

        <!-- Card Productos -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-secondary mb-1">Productos</h5>
                  <h2 class="mb-0">{{ $totalProducts ?? 0 }}</h2>
                </div>
                <div class="text-secondary opacity-50">
                  <i class="fas fa-boxes fa-3x"></i>
                </div>
              </div>
            </div>
             <div class="card-footer bg-transparent border-top-0 text-right">
                  {{-- Asegúrate que 'products.index' sea el nombre correcto de tu ruta --}}
                 <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Ver Productos</a>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->

      <!-- Fila para contenido adicional (Gráficos, Tablas, etc.) -->
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header border-0">
              <h3 class="card-title">Ventas Últimos 7 Días</h3>
              <div class="card-tools">
                {{-- Puedes añadir botones aquí si quieres (ej. descargar) --}}
              </div>
            </div>
            <div class="card-body">
              <div class="position-relative mb-4">
                <canvas id="sales-chart-canvas" height="200"></canvas>
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <div class="col-lg-6">
           <div class="card">
            <div class="card-header border-0">
              <h3 class="card-title">Ventas vs Compras (Últimos 12 Meses)</h3>
               <div class="card-tools">
                 {{-- Puedes añadir botones aquí si quieres --}}
               </div>
            </div>
            <div class="card-body">
                <canvas id="comparison-chart-canvas" height="200"></canvas>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- /.row -->

      <!-- NUEVO: Informe de Productos con Stock Bajo o Agotados -->
      <div class="row mt-4">
          <div class="col-12">
              <div class="card card-warning card-outline"> {{-- Usamos card-warning para destacar --}}
                  <div class="card-header">
                      <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-1"></i>Productos con Stock Bajo o Agotados</h3>
                  </div>
                  <div class="card-body p-0"> {{-- p-0 para que la tabla ocupe todo el espacio --}}
                      @if(isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
                          <div class="table-responsive">
                              <table class="table table-hover table-striped mb-0">
                                  <thead>
                                      <tr>
                                          <th>Producto</th>
                                          <th class="text-center">Stock Actual</th>
                                          <th class="text-center">Stock Mínimo</th>
                                          <th>Estado</th>
                                          <th>Acciones</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @foreach($lowStockProducts as $product)
                                          <tr>
                                              <td>
                                                  {{ $product->name }}
                                                  @if($product->code)
                                                      <br><small class="text-muted">Código: {{ $product->code }}</small>
                                                  @endif
                                              </td>
                                              <td class="text-center {{ $product->stock == 0 ? 'text-danger font-weight-bold' : '' }}">{{ $product->stock }}</td>
                                              <td class="text-center">{{ $product->min_stock > 0 ? $product->min_stock : 'N/A' }}</td>
                                              <td>
                                                  @if($product->stock == 0)
                                                      <span class="badge bg-danger">Agotado</span>
                                                  @elseif($product->min_stock > 0 && $product->stock <= $product->min_stock)
                                                      <span class="badge bg-warning">Stock Bajo</span>
                                                  @else
                                                      {{-- Este caso no debería ocurrir con la consulta actual si min_stock es 0 y stock > 0 --}}
                                                      <span class="badge bg-secondary">Revisar</span>
                                                  @endif
                                              </td>
                                              <td>
                                                  <a href="{{ route('products.edit', $product) }}" class="btn btn-xs btn-info" title="Editar Producto"><i class="fas fa-edit"></i></a>
                                                  {{-- Podrías añadir un botón para ir a registrar una compra de este producto --}}
                                              </td>
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>
                      @else
                          <div class="p-3 text-center">
                              <p class="mb-0 text-success"><i class="fas fa-check-circle mr-1"></i>¡Excelente! No hay productos por debajo del stock mínimo o agotados en este momento.</p>
                          </div>
                      @endif
                  </div>
                  <div class="card-footer text-muted small">
                      Este informe muestra productos cuyo stock actual es igual o inferior a su stock mínimo configurado (y el mínimo es > 0), o productos con stock cero.
                  </div>
              </div>
          </div>
      </div>
      <!-- FIN NUEVO INFORME -->

    </div><!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div> {{-- Fin de content-wrapper --}}
@endsection

@push('styles')
{{-- Si necesitas estilos específicos para esta página --}}
@endpush

@push('scripts')
{{-- Si necesitas scripts específicos para esta página (ej. para gráficos) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // --- Gráfico Ventas Últimos 7 Días ---
    const salesCtx = document.getElementById('sales-chart-canvas').getContext('2d');
    const salesChart = new Chart(salesCtx, {
      type: 'bar', // Tipo de gráfico
      data: {
        labels: @json($salesLast7DaysLabels), // Etiquetas (días) desde el controlador
        datasets: [{
          label: 'Ventas (S/)',
          backgroundColor: 'rgba(60,141,188,0.9)', // Color azul AdminLTE
          borderColor: 'rgba(60,141,188,0.8)',
          pointRadius: false,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: @json($salesLast7DaysData) // Datos (montos) desde el controlador
        }]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
          display: false // Ocultar leyenda si solo hay un dataset
        },
        scales: {
          xAxes: [{
            gridLines: { display: false }
          }],
          yAxes: [{
            ticks: { beginAtZero: true } // Empezar eje Y en 0
          }]
        }
      }
    });

    // --- Gráfico Comparativa Ventas vs Compras (12 Meses) ---
    const comparisonCtx = document.getElementById('comparison-chart-canvas').getContext('2d');
    const comparisonChart = new Chart(comparisonCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyComparisonLabels),
            datasets: [
                {
                    label: 'Ventas (S/)',
                    backgroundColor: 'rgba(0, 166, 90, 0.2)', // Verde AdminLTE con transparencia
                    borderColor: 'rgba(0, 166, 90, 1)',
                    data: @json($monthlySalesData),
                    fill: true, // Rellenar área bajo la línea
                },
                {
                    label: 'Compras (S/)',
                    backgroundColor: 'rgba(243, 156, 18, 0.2)', // Naranja AdminLTE con transparencia
                    borderColor: 'rgba(243, 156, 18, 1)',
                    data: @json($monthlyPurchasesData),
                    fill: true, // Rellenar área bajo la línea
                }
            ]
        },
        options: { // Opciones similares al anterior, puedes personalizarlas
            maintainAspectRatio: false,
            responsive: true,
            legend: { display: true }, // Mostrar leyenda para distinguir líneas
            scales: {
                xAxes: [{ gridLines: { display: false } }],
                yAxes: [{ ticks: { beginAtZero: true } }]
            },
            elements: { line: { tension: 0.3 } } // Suavizar un poco las líneas
        }
    });

  });
</script>
@endpush

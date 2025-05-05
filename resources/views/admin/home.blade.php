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
            <div class="card-header">
              <h3 class="card-title">Próximamente: Gráfico de Ventas</h3>
              <div class="card-tools">
                {{-- Botones para acciones del card --}}
              </div>
            </div>
            <div class="card-body">
              {{-- Aquí iría el canvas para un gráfico (ej. Chart.js) --}}
              <p class="text-center text-muted py-4">Gráfico en desarrollo.</p>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <div class="col-lg-6">
           <div class="card">
            <div class="card-header">
              <h3 class="card-title">Próximamente: Actividad Reciente</h3>
            </div>
            <div class="card-body">
               <p class="text-center text-muted py-4">Listado en desarrollo.</p>
               {{-- Aquí iría una tabla o lista --}}
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- /.row -->

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
{{-- <script> // Ejemplo básico si añades Chart.js
  // const ctx = document.getElementById('myChart');
  // new Chart(ctx, { ...configuración... });
</script> --}}
@endpush

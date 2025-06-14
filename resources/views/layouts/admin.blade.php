<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  {{-- Título dinámico o predeterminado --}}
  <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Panel')</title>
  {{-- Estilos de Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  {{-- Estilos de Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  {{-- Estilos personalizados (igual que antes) --}}
  <style>
    body {
      margin: 0;
      background-color: #f5f5f5;
    }

    .header {
      position: sticky;
      top: 0;
      background-color: #37474f;
      color: white;
      padding: 15px 20px;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .menu-toggle {
      cursor: pointer;
      font-size: 1.2rem;
      margin-right: 20px;
    }

    .layout {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 230px;
      background-color: #263238;
      color: white;
      padding-top: 20px;
      flex-shrink: 0;
      transition: all 0.3s;
      display: flex; /* Para empujar el logout al final */
      flex-direction: column; /* Para empujar el logout al final */
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      font-size: 14px;
      transition: all 0.3s;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar.collapsed a span {
      display: none;
    }

    .sidebar h5, .sidebar h6 {
      padding: 0 20px;
      font-size: 14px;
      text-transform: uppercase;
      margin-top: 15px;
      color: #90a4ae;
      transition: all 0.3s;
    }

    .sidebar.collapsed h5,
    .sidebar.collapsed h6 {
      display: none;
    }

    .sidebar a:hover {
      background-color: #37474f;
    }

    .sidebar a.active {
      background-color: #1de9b6; /* Color activo */
      color: black;
    }

    .main-content {
      flex-grow: 1;
      padding: 20px;
      overflow-x: auto; /* Evita desbordamiento horizontal */
    }

    .dropdown-menu {
      right: 0;
      left: auto;
    }

    /* Estilos para el contenedor de breadcrumbs personalizado */
    .breadcrumb-wrapper {
      background-color: #e9ecef; /* Un color de fondo gris claro, similar a Bootstrap */
      padding: 0.75rem 1rem;    /* Espaciado interno */
      border-radius: 0.375rem; /* Bordes redondeados estándar de Bootstrap */
    }

    .breadcrumb-wrapper .breadcrumb {
      margin-bottom: 0; /* Elimina el margen inferior por defecto de la lista de breadcrumbs */
      background-color: transparent; /* Asegura que la lista <ol> en sí no tenga otro fondo */
      padding: 0; /* El padding lo maneja el wrapper, reseteamos el de <ol> */
    }
  </style>
  {{-- AQUÍ SE INSERTARÁN LOS ESTILOS ESPECÍFICOS DE CADA VISTA --}}
  @stack('styles')
</head>
<body>

  <div class="header">
    <div class="d-flex align-items-center">
      <i class="bi bi-list menu-toggle" onclick="toggleSidebar()"></i>
      @php
          $business_logo_path = null;
          $display_name_admin = config('app.name', 'Panel'); // Fallback inicial
          if (Schema::hasTable('businesses')) {
              $business = \App\Models\Business::first(); // Asume que solo hay un registro de negocio
              if ($business && $business->logo) {
                  $business_logo_path = Illuminate\Support\Facades\Storage::disk('public')->url($business->logo);
              }
              if ($business && $business->name) {
                  $display_name_admin = $business->name;
              }
          }
      @endphp
      @if($business_logo_path)
          <img src="{{ $business_logo_path }}" alt="Logo" style="height: 30px; margin-right: 10px;">
      @endif
      <strong>{{ $display_name_admin }}</strong> {{-- Nombre del negocio o app --}}
    </div>
    {{-- Verifica si el usuario está autenticado --}}
    @auth
    <div class="dropdown user-menu">
      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        {{ Auth::user()->name }} {{-- Nombre del usuario autenticado --}}
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        {{-- <li><a class="dropdown-item" href="#">Perfil</a></li> --}}
        {{-- <li><a class="dropdown-item" href="#">Configuración</a></li> --}}
        {{-- <li><hr class="dropdown-divider"></li> --}}
        <li>
            {{-- Formulario para Logout --}}
            <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
        </li>
      </ul>
    </div>
    @endauth
  </div>
  <div class="layout">
    <div class="sidebar" id="sidebar">
      <div> {{-- Contenedor para los enlaces principales --}}
        <h5>MENÚ GENERAL</h5>
        {{-- Enlace activo dinámicamente --}}
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
          <i class="bi bi-house-door-fill"></i><span>Inicio</span>
        </a>

        {{-- Adapta las rutas a las tuyas --}}
        <h6>ADMINISTRACIÓN</h6>
        @can('ver clientes')
        <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i><span>Clientes</span>
        </a>
        @endcan
        @can('ver proveedores')
        <a href="{{ route('providers.index') }}" class="{{ request()->routeIs('providers.*') ? 'active' : '' }}">
          <i class="bi bi-person-vcard"></i><span>Proveedores</span>
        </a>
        @endcan
        @can('ver productos')
         <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
          <i class="bi bi-box-seam"></i><span>Productos</span>
        </a>
        @endcan
        @can('ver categorías')
         <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
          <i class="bi bi-tag-fill"></i><span>Categorías</span>
        </a>
        @endcan

        {{-- Opciones solo para Admin --}}
        @role('Admin')
        <a href="{{ route('admin.business.index') }}" class="{{ request()->routeIs('admin.business.*') ? 'active' : '' }}">
          <i class="bi bi-briefcase-fill"></i><span>Negocio</span> {{-- Icono para negocio/empresa --}}
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
          <i class="bi bi-person-gear"></i><span>Usuarios</span> {{-- Icono de gestión de usuarios --}}
        </a>
        @endrole



        <h6>TRANSACCIONES</h6>
        @can('ver compras')
         <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
          <i class="bi bi-cart-plus-fill"></i><span>Compras</span>
        </a>
        @endcan
        @can('ver ventas')
         <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
          <i class="bi bi-receipt"></i><span>Ventas</span>
        </a>
        @endcan

        {{-- === INICIO: ENLACES DE REPORTES === --}}
        @can('ver reportes') {{-- Corregido: usa el nombre de permiso de UserSeeder.php --}}
        <h6>REPORTES</h6>
        <a href="{{ route('reports.day') }}" class="{{ request()->routeIs('reports.day') ? 'active' : '' }}">
            <i class="bi bi-calendar-day"></i><span>Reporte del Día</span> {{-- Usar icono Bootstrap --}}
        </a>
        <a href="{{ route('reports.date') }}" class="{{ request()->routeIs('reports.date') || request()->routeIs('report.results') ? 'active' : '' }}">
            <i class="bi bi-calendar-range"></i><span>Reporte por Fechas</span> {{-- Usar icono Bootstrap --}}
        </a>
        @endcan
        {{-- === FIN: ENLACES DE REPORTES === --}}

        {{-- Puedes añadir más secciones y enlaces aquí --}}
      </div>

      {{-- Enlace de Salir (usando el formulario de logout) - Empujado al final --}}
      @auth
      <div class="mt-auto mb-2"> {{-- mt-auto empuja este div hacia abajo --}}
          <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
          <a href="{{ route('logout') }}" class="text-danger"
             onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
             <i class="bi bi-box-arrow-right"></i><span>Salir</span>
          </a>
      </div>
      @endauth
    </div>

    <!-- Main Content -->
    <div class="main-content">
      {{-- INICIO: Cabecera de Página con Título y Breadcrumbs --}}
      <div class="page-header mb-3">
        {{-- Título principal de la página --}}
        <div class="row">
          <div class="col">
            <h1 class="h3 page-main-title mb-2">@yield('page_header', 'Panel')</h1> {{-- Añadido mb-2 para espacio debajo del título --}}
          </div>
        </div>

        {{-- Breadcrumbs con fondo, debajo del título --}}
        {{-- Solo se muestra si hay breadcrumbs definidos por la vista hija --}}
        @hasSection('breadcrumbs')
        <div class="row">
          <div class="col">
            <nav aria-label="breadcrumb" class="breadcrumb-wrapper">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                @yield('breadcrumbs') {{-- Las vistas hijas deben proporcionar los <li> adicionales --}}
              </ol>
            </nav>
          </div>
        </div>
        @endif
      </div>
      {{-- FIN: Cabecera de Página --}}

      {{-- INICIO: Mensajes Flash Globales (Éxito, Error, Alertas de Stock) --}}
      <div class="container-fluid px-0"> {{-- Usamos px-0 si el padding ya está en main-content --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('low_stock_alerts') && is_array(session('low_stock_alerts')) && count(session('low_stock_alerts')) > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>¡Atención! Productos con stock bajo:</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('low_stock_alerts') as $alert)
                        <li>{{ $alert }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            {{-- Comentamos esta línea para que la alerta persista y pueda ser mostrada
                 en las páginas específicas (productos, compras) antes de ser borrada allí. --}}
            {{-- @php session()->forget('low_stock_alerts'); @endphp --}}
        @endif
      </div>
      {{-- FIN: Mensajes Flash Globales --}}

      {{-- El contenido específico de cada página se insertará aquí --}}
      @yield('content')
    </div>
  </div>

  {{-- Script para el menú lateral --}}
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
    }
  </script>

  {{-- === CORRECCIÓN: Carga jQuery PRIMERO === --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  {{-- ======================================== --}}

  {{-- Script de Bootstrap (Bundle incluye Popper) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  {{-- AQUÍ SE INSERTARÁN LOS SCRIPTS ESPECÍFICOS DE CADA VISTA --}}
  @stack('scripts')

</body>
</html>

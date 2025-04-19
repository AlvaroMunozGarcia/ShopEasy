<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
      background-color: #1de9b6;
      color: black;
    }

    .main-content {
      flex-grow: 1;
      padding: 20px;
    }

    .dropdown-menu {
      right: 0;
      left: auto;
    }
  </style>
</head>
<body>

  <div class="header">
    <div class="d-flex align-items-center">
      <i class="bi bi-list menu-toggle" onclick="toggleSidebar()"></i>
      <strong>Panel de usuario</strong>
    </div>
    <div class="dropdown user-menu">
      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        MUÑOZ GARCÍA ÁLVARO
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Perfil</a></li>
        <li><a class="dropdown-item" href="#">Configuración</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="#">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
  <div class="layout">
    <div class="sidebar" id="sidebar">
      <h5>MENÚ GENERAL</h5>
      <a href="#" class="active"><i class="bi bi-house-door-fill"></i><span>Inicio</span></a>

      <h6>DATOS FISCALES</h6>
      <a href="#"><i class="bi bi-cash-coin"></i><span>Situación de cuentas</span></a>
      <a href="#"><i class="bi bi-receipt-cutoff"></i><span>Extracto de movimientos</span></a>

      <h6>FACTURACIÓN</h6>
      <a href="#"><i class="bi bi-file-earmark-text"></i><span>Facturas - Liquidación</span></a>
      <a href="#"><i class="bi bi-truck"></i><span>Albaranes de entrega</span></a>
      <a href="#"><i class="bi bi-file-text"></i><span>Facturas</span></a>
      <a href="#"><i class="bi bi-journal-text"></i><span>Facturas OPFH</span></a>

      <h6>CAMPO</h6>
      <a href="#"><i class="bi bi-flower1"></i><span>Cultivos</span></a>
      <a href="#"><i class="bi bi-list-ul"></i><span>Partidas</span></a>
      <a href="#"><i class="bi bi-bar-chart-line"></i><span>Estadística de artículos</span></a>

      <h6>DOCUMENTOS</h6>
      <a href="#"><i class="bi bi-file-earmark-person"></i><span>Fichas cultivos</span></a>
      <a href="#"><i class="bi bi-shield-lock"></i><span>Fichas Seguridad</span></a>

      <a href="#" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i><span>Salir</span></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      {{-- <h4>Bienvenido al sistema</h4> --}}
      {{-- <p>Aquí puedes colocar el contenido principal de la página.</p> --}}
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
  {{-- Script de Bootstrap (Bundle incluye Popper) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  {{-- AQUÍ SE INSERTARÁN LOS SCRIPTS ESPECÍFICOS DE CADA VISTA --}}
  @stack('scripts')

</body>
</html>

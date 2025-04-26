@extends('layouts.admin') {{-- O tu layout principal de admin --}}

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li> {{-- Asume una ruta 'admin.dashboard' --}}
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Usuarios</h3>
                            <div class="card-tools">
                                {{-- Solo muestra el botón si el usuario tiene permiso --}}
                                @can('manage users')
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Crear Nuevo Usuario
                                </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Mensajes de sesión --}}
                            @include('admin.partials._messages') {{-- Asume que tienes un parcial para mensajes --}}

                            <table id="usersTable" class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo Electrónico</th>
                                        <th>Roles</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                {{-- Muestra los nombres de los roles separados por coma --}}
                                                {{ $user->getRoleNames()->implode(', ') ?: 'Sin rol' }}
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                {{-- Botón Ver (si tienes una vista 'show') --}}
                                                {{-- @can('manage users') --}} {{-- O un permiso 'view users' --}}
                                                {{-- <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a> --}}
                                                {{-- @endcan --}}

                                                {{-- Botón Editar --}}
                                                @can('manage users')
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan

                                                {{-- Botón Eliminar --}}
                                                @can('manage users')
                                                    {{-- Evita que el admin principal se elimine a sí mismo (opcional pero buena práctica) --}}
                                                    @if(auth()->user()->id !== $user->id && !$user->hasRole('Admin')) {{-- O una lógica más compleja si hay varios admins --}}
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Deshabilita el botón si es el usuario actual o un Admin --}}
                                                        <button type="button" class="btn btn-danger btn-sm disabled" title="No se puede eliminar este usuario">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay usuarios registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Paginación (si la usas en el controlador) --}}
                        @if ($users->hasPages())
                            <div class="card-footer clearfix">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
    {{-- Si usas DataTables --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}"> --}}
@endpush

@push('scripts')
    {{-- Si usas DataTables --}}
    {{-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(function () {
            $("#usersTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"] // Opcional
            }).buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)'); // Opcional
        });
    </script> --}}
@endpush

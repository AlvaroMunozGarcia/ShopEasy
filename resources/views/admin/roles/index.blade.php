@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Gestión de Roles')

@section('content')
    {{-- El título se moverá dentro del card-header para un diseño más integrado --}}

    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gestión de Roles</h4> {{-- Título de la tarjeta --}}
                {{-- Si tienes una ruta para crear roles, descomenta y ajústala --}}
                {{-- <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-shield-plus"></i> Crear Nuevo Rol
                </a> --}}
            </div>
        </div>
        <div class="card-body">
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

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover"> {{-- Añadido table-bordered --}}
                    <thead class="table-dark"> {{-- Encabezado oscuro --}}
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Rol</th>
                            <th>Guard</th>
                            <th>Permisos Asociados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>
                                    @forelse ($role->permissions as $permission)
                                        <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">Sin permisos asignados</span>
                                    @endforelse
                                </td>
                                <td>
                                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="bi bi-eye"></i></a>
                                    {{-- <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a> --}}
                                    {{-- <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro?')"><i class="bi bi-trash"></i></button>
                                    </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay roles definidos en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- Si tienes paginación para roles, puedes añadirla aquí --}}
            {{-- @if ($roles->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $roles->links() }}
                </div>
            @endif --}}
        </div>
    </div>
@endsection

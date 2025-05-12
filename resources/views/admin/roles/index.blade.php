@extends('layouts.admin')

@section('title', 'Gestión de Roles')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Mensajes Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestión de Roles</h5>
                {{-- Descomenta si habilitas la creación de roles --}}
                {{-- 
                <a href="{{ route('admin.roles.create') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="bi bi-shield-plus me-1"></i> Crear Nuevo Rol
                </a>
                --}}
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
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
                                    <td class="text-center">{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td>
                                        @forelse ($role->permissions as $permission)
                                            <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted">Sin permisos asignados</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        {{-- 
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay roles definidos en el sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginación opcional --}}
            @if ($roles instanceof \Illuminate\Pagination\LengthAwarePaginator && $roles->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $roles->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
@endsection

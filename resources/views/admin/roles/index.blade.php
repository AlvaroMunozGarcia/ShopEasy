@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Gestión de Roles')

@section('content')
<div class="container">
    <h1>Gestión de Roles</h1>

    {{-- Mensajes de éxito/error si alguna vez se añaden acciones --}}
    {{-- @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif --}}

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
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
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info">Ver Detalles</a>
                            {{-- Aquí irían botones de Editar/Eliminar si se implementan --}}
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
@endsection

@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Rol')

@section('content')
<div class="container">
    <h1>Detalles del Rol: {{ $role->name }}</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $role->id }}</p>
            <p><strong>Nombre:</strong> {{ $role->name }}</p>
            <p><strong>Guard Name:</strong> {{ $role->guard_name }}</p>
            <hr>
            <h5>Permisos Asignados a este Rol:</h5>
            @if($role->permissions->count() > 0)
                <ul>
                    @foreach ($role->permissions as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Este rol no tiene permisos asignados directamente.</p>
            @endif

        </div>
        <div class="card-footer">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Volver al Listado de Roles</a>
             {{-- Aquí iría botón de Editar si se implementa --}}
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container">
    <h1>Gestión de Usuarios</h1>

    <div class="mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">Crear Nuevo Usuario</a>
    </div>

    {{-- Mostrar mensajes de éxito/error --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary me-1">Editar</a>
                            {{-- <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info me-1">Ver</a> --}}
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar a este usuario?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>

</div>
@endsection

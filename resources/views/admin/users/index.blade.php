@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Gestión de Usuarios') {{-- Esto está bien si tu layout lo usa para el <title> del HTML --}}

@section('content')
    {{-- El título se moverá dentro del card-header para un diseño más integrado --}}

    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gestión de Usuarios</h4> {{-- Título de la tarjeta --}}
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Crear Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Mostrar mensajes de éxito/error --}}
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
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning me-1" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    {{-- <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info me-1" title="Ver"><i class="bi bi-eye"></i></a> --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar a este usuario?')"><i class="bi bi-trash"></i></button>
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
        </div>
        <div class="card-footer">
            @if ($users->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

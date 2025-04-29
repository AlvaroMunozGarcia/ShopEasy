@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Usuario')

@section('content')
<div class="container">
    <h1>Detalles del Usuario: {{ $user->name }}</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Roles:</strong>
                @forelse ($user->roles as $role)
                    <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                @empty
                    <span>Sin roles asignados.</span>
                @endforelse
            </p>
            <p><strong>Fecha de Creación:</strong> {{ $user->created_at->format('d/m/Y H:i:s') }}</p>
            <p><strong>Última Actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Volver al Listado</a>
        </div>
    </div>
</div>
@endsection

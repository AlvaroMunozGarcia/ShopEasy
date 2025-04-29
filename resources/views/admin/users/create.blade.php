@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Crear Nuevo Usuario')

@section('content')
<div class="container">
    <h1>Crear Nuevo Usuario</h1>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="mb-3">
            <label for="roles" class="form-label">Roles</label>
            <select multiple class="form-control" id="roles" name="roles[]" required>
                @foreach ($roles as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
             <small class="form-text text-muted">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples roles.</small>
        </div>

        <button type="submit" class="btn btn-primary">Crear Usuario</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

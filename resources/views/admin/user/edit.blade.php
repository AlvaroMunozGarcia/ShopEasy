@extends('layouts.admin') {{-- O tu layout principal de admin --}}

@section('title', 'Editar Usuario')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Usuario: {{ $user->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2"> {{-- O col-12 --}}
                    <div class="card card-warning"> {{-- Cambiado a card-warning para editar --}}
                        <div class="card-header">
                            <h3 class="card-title">Formulario de Edición de Usuario</h3>
                        </div>
                        {{-- Apunta a la ruta update, pasando el ID del usuario --}}
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT') {{-- Método HTTP para actualizar --}}

                            <div class="card-body">
                                {{-- Campo Nombre --}}
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Ingrese el nombre completo">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                {{-- Campo Correo Electrónico --}}
                                <div class="form-group">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Ingrese el correo electrónico">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                {{-- Campo Contraseña (Opcional en edición) --}}
                                <div class="form-group">
                                    <label for="password">Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Mínimo 8 caracteres. Deje vacío si no desea cambiar la contraseña actual.</small>
                                </div>

                                {{-- Campo Confirmar Contraseña --}}
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirme la nueva contraseña">
                                </div>

                                {{-- Campo Selección de Roles --}}
                                <div class="form-group">
                                    <label for="roles">Roles <span class="text-danger">*</span></label>
                                    {{-- Obtener los nombres de los roles actuales del usuario --}}
                                    @php
                                        $userRoles = $user->getRoleNames()->toArray();
                                    @endphp
                                    <select name="roles[]" id="roles" class="form-control select2 @error('roles') is-invalid @enderror" multiple="multiple" data-placeholder="Seleccione uno o más roles" style="width: 100%;" required>
                                        @foreach($roles as $roleName)
                                            <option value="{{ $roleName }}"
                                                {{-- Marcar si estaba seleccionado antes (old) O si es un rol actual del usuario --}}
                                                {{ (collect(old('roles', $userRoles))->contains($roleName)) ? 'selected' : '' }}>
                                                {{ $roleName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                     <small class="form-text text-muted">Puedes seleccionar múltiples roles (Admin, Vendedor, etc.).</small>
                                </div>

                            </div> {{-- /.card-body --}}

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"> {{-- Botón de actualizar --}}
                                    <i class="fas fa-save mr-1"></i> Actualizar Usuario
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div> {{-- /.card --}}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
    {{-- Si usas Select2 --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> --}}
@endpush

@push('scripts')
    {{-- Si usas Select2 --}}
    {{-- <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('.select2').select2({
                 theme: 'bootstrap4'
            })
        });
    </script> --}}
@endpush

@extends('layouts.admin') {{-- O tu layout principal de admin --}}

@section('title', 'Crear Nuevo Usuario')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Crear Nuevo Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2"> {{-- O col-12 si prefieres ancho completo --}}
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Formulario de Creación de Usuario</h3>
                        </div>
                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                {{-- Campo Nombre --}}
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ingrese el nombre completo">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                {{-- Campo Correo Electrónico --}}
                                <div class="form-group">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Ingrese el correo electrónico">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                {{-- Campo Contraseña --}}
                                <div class="form-group">
                                    <label for="password">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Ingrese la contraseña">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                                </div>

                                {{-- Campo Confirmar Contraseña --}}
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirme la contraseña">
                                    {{-- El error de confirmación lo maneja Laravel automáticamente en el campo 'password' --}}
                                </div>

                                {{-- Campo Selección de Roles --}}
                                <div class="form-group">
                                    <label for="roles">Roles <span class="text-danger">*</span></label>
                                    {{-- Asume que pasas $roles (Role::pluck('name', 'name')) desde el controlador --}}
                                    <select name="roles[]" id="roles" class="form-control select2 @error('roles') is-invalid @enderror" multiple="multiple" data-placeholder="Seleccione uno o más roles" style="width: 100%;" required>
                                        @foreach($roles as $roleName)
                                            <option value="{{ $roleName }}" {{ (collect(old('roles'))->contains($roleName)) ? 'selected' : '' }}>
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar Usuario
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
    {{-- Si usas Select2 para el dropdown de roles --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> --}}
@endpush

@push('scripts')
    {{-- Si usas Select2 para el dropdown de roles --}}
    {{-- <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2({
                 theme: 'bootstrap4' // Usa el tema de Bootstrap 4 si lo tienes
            })
        });
    </script> --}}
@endpush

@extends('layouts.admin')

@section('title', 'Crear Nuevo Rol')

@section('page_header', 'Crear Nuevo Rol')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Crear Nuevo</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulario de Creación de Rol</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">¡Oops! Hubo algunos problemas:</h4>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="row">
                            @php
                                // Agrupar permisos por la primera palabra para mejor organización
                                $groupedPermissions = $permissions->groupBy(function ($permission) {
                                    $parts = explode(' ', $permission->name);
                                    return ucfirst($parts[0]); // ej: "Manage", "View", "Create"
                                });
                            @endphp

                            @foreach ($groupedPermissions as $groupName => $group)
                                <div class="col-md-4 mb-3">
                                    <h6>{{ $groupName }}</h6>
                                    @foreach ($group as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input @error('permissions') is-invalid @enderror @error('permissions.*') is-invalid @enderror" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        @error('permissions') {{-- Error general para el array de permisos --}}
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                         @error('permissions.*') {{-- Error específico para un permiso individual (si la validación lo devuelve así) --}}
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary fw-semibold">Guardar Rol</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-check {
        margin-bottom: 0.5rem;
    }
    .card-body h6 { /* Estilo para los títulos de grupo de permisos */
        font-weight: 500;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }
    .alert ul {
        margin-bottom: 0;
    }
    .card-footer .btn {
        min-width: 120px;
    }
    /* Para que el mensaje de error de los checkboxes se muestre correctamente */
    .form-check-input.is-invalid ~ .form-check-label {
        color: var(--bs-danger); /* O el color que uses para texto de error */
    }
    .invalid-feedback.d-block { /* Asegura que el mensaje de error para el grupo de checkboxes sea visible */
        display: block !important;
    }
</style>
@endpush

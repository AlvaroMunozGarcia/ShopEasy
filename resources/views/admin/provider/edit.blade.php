{{-- resources/views/admin/provider/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Editar Proveedor')

@section('page_header')
    Editar Proveedor: <span class="text-muted">{{ $provider->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('providers.index') }}">Proveedores</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9 col-md-10"> {{-- Ajusta estas columnas según prefieras el ancho --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        {{-- Mostrar errores de validación (si usas Form Requests) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- El controlador usa UpdateRequest, la acción es providers.update --}}
                        <form action="{{ route('providers.update', $provider) }}" method="POST">
                            @csrf {{-- Protección CSRF --}}
                            @method('PUT') {{-- Método HTTP para Update --}}

                             <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Proveedor <span class="text-danger">*</span></label>
                                {{-- Usar old() con el valor actual del proveedor como fallback --}}
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $provider->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email (Opcional)</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $provider->email) }}">
                                     @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ruc_number" class="form-label">Número RUC (Opcional)</label> {{-- Campo específico del modelo --}}
                                    <input type="text" class="form-control @error('ruc_number') is-invalid @enderror" id="ruc_number" name="ruc_number" value="{{ old('ruc_number', $provider->ruc_number) }}">
                                     @error('ruc_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Teléfono (Opcional)</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $provider->phone) }}">
                                     @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección (Opcional)</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $provider->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Actualizar Proveedor
                                </button>
                                <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

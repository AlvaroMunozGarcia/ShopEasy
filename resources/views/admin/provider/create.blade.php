{{-- resources/views/admin/provider/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Añadir Nuevo Proveedor</h1>

    <div class="card shadow-sm">
        <div class="card-body">
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

            {{-- El controlador usa StoreRequest, la acción es providers.store --}}
            <form action="{{ route('providers.store') }}" method="POST">
                @csrf {{-- Protección CSRF --}}

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Proveedor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                         @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ruc_number" class="form-label">Número RUC</label> {{-- Campo específico del modelo --}}
                        <input type="text" class="form-control @error('ruc_number') is-invalid @enderror" id="ruc_number" name="ruc_number" value="{{ old('ruc_number') }}">
                         @error('ruc_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                         @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Dirección</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Proveedor
                    </button>
                    <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                         <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

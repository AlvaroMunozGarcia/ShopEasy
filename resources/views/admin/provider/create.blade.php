{{-- resources/views/admin/provider/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Añadir Nuevo Proveedor')

@section('page_header', 'Añadir Nuevo Proveedor')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('providers.index') }}">Proveedores</a></li>
    <li class="breadcrumb-item active" aria-current="page">Añadir Nuevo</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9 col-md-10"> 
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('providers.store') }}" method="POST">
                            @csrf 

                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Proveedor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email (Opcional)</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                     @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ruc_number" class="form-label">Número RUC (Opcional)</label> 
                                    <input type="text" class="form-control @error('ruc_number') is-invalid @enderror" id="ruc_number" name="ruc_number" value="{{ old('ruc_number') }}">
                                     @error('ruc_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Teléfono (Opcional)</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                     @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección (Opcional)</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-save me-1"></i> Guardar Proveedor
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

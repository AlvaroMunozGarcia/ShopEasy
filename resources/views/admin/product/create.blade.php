{{-- resources/views/admin/product/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Añadir Nuevo Producto')

@section('page_header', 'Añadir Nuevo Producto')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Añadir Nuevo</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10 col-md-11"> {{-- Columnas más anchas para formularios con más campos --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        {{-- Mostrar errores de validación --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Necesitas enctype para subir archivos --}}
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf {{-- Protección CSRF --}}

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Código</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                                     @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Selecciona una categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label for="provider_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-select @error('provider_id') is-invalid @enderror" id="provider_id" name="provider_id" required>
                                        {{-- La opción "Selecciona un proveedor" solo está seleccionada si no hay un valor 'old' ni un 'selected_provider_id' --}}
                                        <option value=""
                                            @if(!old('provider_id') && !isset($selected_provider_id)) selected @endif
                                            disabled>Selecciona un proveedor</option>
                                        @foreach ($providers as $provider_item) {{-- Renombrado para evitar conflicto con $provider de la vista show/edit del proveedor --}}
                                            <option value="{{ $provider_item->id }}"
                                                {{-- Se prioriza el valor 'old', luego el 'selected_provider_id' pasado desde el controlador --}}
                                                {{ old('provider_id', $selected_provider_id ?? null) == $provider_item->id ? 'selected' : '' }}>
                                                {{ $provider_item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="sell_price" class="form-label">Precio de Venta (€) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" required>
                                     @error('sell_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="stock" class="form-label">Stock Inicial <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                                     @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="min_stock" class="form-label">Stock Mínimo para Alerta</label>
                                    <input type="number" min="0" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', 5) }}" placeholder="Ej: 5">
                                    <small class="form-text text-muted">Alerta cuando el stock sea igual o menor.</small>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="picture" class="form-label">Imagen del Producto</label>
                                    <input class="form-control @error('picture') is-invalid @enderror" type="file" id="picture" name="picture" accept="image/*">
                                     @error('picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="ACTIVE" {{ old('status', 'ACTIVE') == 'ACTIVE' ? 'selected' : '' }}>Activo</option>
                                        <option value="INACTIVE" {{ old('status') == 'INACTIVE' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                     @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-save me-1"></i> Guardar Producto
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">
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

{{-- resources/views/admin/product/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Editar Producto: {{ $product->name }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
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

            {{-- Necesitas enctype si permites cambiar la imagen --}}
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf {{-- Protección CSRF --}}
                @method('PUT') {{-- Método HTTP para Update --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Código</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $product->code) }}">
                         @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="" disabled>Selecciona una categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                            <option value="" disabled>Selecciona un proveedor</option>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('provider_id', $product->provider_id) == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('provider_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sell_price" class="form-label">Precio de Venta (€) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" required>
                         @error('sell_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                         @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                 <div class="row align-items-end">
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Cambiar Imagen (Opcional)</label>
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                        <small class="text-muted">Dejar en blanco para conservar la imagen actual.</small>
                         @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-6 mb-3">
                        @if($product->image)
                            <label class="form-label">Imagen Actual:</label><br>
                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" style="max-height: 100px;">
                        @else
                            <p class="text-muted">Sin imagen actual.</p>
                        @endif
                    </div>
                </div>

                 <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="ACTIVE" {{ old('status', $product->status) == 'ACTIVE' ? 'selected' : '' }}>Activo</option>
                            <option value="INACTIVE" {{ old('status', $product->status) == 'INACTIVE' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                         @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Actualizar Producto
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

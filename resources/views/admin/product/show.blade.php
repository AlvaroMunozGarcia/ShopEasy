{{-- resources/views/admin/product/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Detalles del Producto: {{ $product->name }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9">{{ $product->id }}</dd>

                        <dt class="col-sm-3">Código</dt>
                        <dd class="col-sm-9">{{ $product->code ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $product->name }}</dd>

                        <dt class="col-sm-3">Categoría</dt>
                        <dd class="col-sm-9">{{ $product->category->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Proveedor</dt>
                        <dd class="col-sm-9">{{ $product->provider->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Stock Actual</dt>
                        <dd class="col-sm-9">{{ $product->stock }}</dd>

                        <dt class="col-sm-3">Precio de Venta</dt>
                        <dd class="col-sm-9">{{ number_format($product->sell_price, 2, ',', '.') }} €</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            @if($product->status == 'ACTIVE')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Creado</dt>
                        <dd class="col-sm-9">{{ $product->created_at->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-3">Actualizado</dt>
                        <dd class="col-sm-9">{{ $product->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </dl>
                </div>
                <div class="col-md-4 text-center">
                     <dt class="mb-2">Imagen</dt>
                     @if($product->image)
                        {{-- Asume que las imágenes están en storage/app/public/products
                             y que has corrido `php artisan storage:link` --}}
                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" style="max-height: 250px;">
                     @else
                        <img src="{{ asset('path/to/default/placeholder.png') }}" alt="Sin imagen" class="img-fluid img-thumbnail" style="max-height: 250px;">
                        {{-- O simplemente muestra un texto --}}
                        {{-- <p class="text-muted">Sin imagen</p> --}}
                     @endif
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
                <div>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

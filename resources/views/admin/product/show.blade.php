{{-- resources/views/admin/product/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Detalles del Producto: {{ $product->name }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9">{{ $product->id }}</dd>

                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $product->name }}</dd>

                        <dt class="col-sm-3">Código</dt>
                        <dd class="col-sm-9">{{ $product->code }}</dd>

                        <dt class="col-sm-3">Categoría</dt>
                        <dd class="col-sm-9">{{ $product->category->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Proveedor</dt>
                        <dd class="col-sm-9">{{ $product->provider->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Stock</dt>
                        <dd class="col-sm-9">{{ $product->stock }}</dd>

                        <dt class="col-sm-3">Precio Venta</dt>
                        <dd class="col-sm-9">{{ number_format($product->sell_price, 2, ',', '.') }} €</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            @if($product->status == 'ACTIVE')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3 mt-3">Código de Barras</dt>
                        <dd class="col-sm-9 mt-3">
                            {!! $barcodeHtml !!} {{-- <-- Mostrar el código de barras HTML --}}
                        </dd>
                    </dl>
                </div>
                <div class="col-md-4">
                    @if($product->image)
                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail">
                    @else
                        <p class="text-muted text-center">Sin imagen</p>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
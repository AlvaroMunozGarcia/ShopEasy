{{-- resources/views/admin/provider/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- La variable $provider viene del controlador --}}
    <h1>Detalles del Proveedor: {{ $provider->name }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $provider->id }}</dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">{{ $provider->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $provider->email ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Número RUC</dt> {{-- Campo específico del modelo --}}
                <dd class="col-sm-9">{{ $provider->ruc_number ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Teléfono</dt>
                <dd class="col-sm-9">{{ $provider->phone ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Dirección</dt>
                <dd class="col-sm-9">{{ $provider->address ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Creado</dt>
                <dd class="col-sm-9">{{ $provider->created_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-3">Actualizado</dt>
                <dd class="col-sm-9">{{ $provider->updated_at->format('d/m/Y H:i:s') }}</dd>
            </dl>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
                <div>
                    <a href="{{ route('providers.edit', $provider) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este proveedor?');">
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

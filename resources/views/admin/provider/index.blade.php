{{-- resources/views/admin/provider/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lista de Proveedores</h1>
        <a href="{{ route('providers.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus-fill"></i> Añadir Proveedor
        </a>
    </div>

    {{-- Mensajes flash (opcional pero recomendado) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('error'))
     <div class="alert alert-danger alert-dismissible fade show" role="alert">
         {{ session('error') }}
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Número RUC</th> {{-- Campo específico del modelo --}}
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- La variable $providers viene del controlador --}}
                        @forelse ($providers as $provider)
                            <tr>
                                <td>{{ $provider->id }}</td>
                                <td>{{ $provider->name }}</td>
                                <td>{{ $provider->email ?? 'N/A' }}</td>
                                <td>{{ $provider->ruc_number ?? 'N/A' }}</td> {{-- Campo específico del modelo --}}
                                <td>{{ $provider->phone ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('providers.show', $provider) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('providers.edit', $provider) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este proveedor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Opcional: Paginación si la implementas en el controlador --}}
            {{-- <div class="mt-3">
                {{ $providers->links() }}
            </div> --}}
        </div>
    </div>
</div>
@endsection

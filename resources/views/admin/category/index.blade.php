@extends('layouts.admin')

@section('content')
    {{-- El título se movió dentro del card-header para un diseño más integrado --}}

    <div class="card mt-3"> {{-- Estructura de tarjeta principal --}}
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Category List</h4> {{-- Título de la tarjeta, puedes cambiar a "Lista de Categorías" si prefieres --}}
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Category {{-- Icono y texto del botón, puedes usar bi-tags o cambiar "Add New Category" a "Crear Categoría" --}}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover"> {{-- Clases de tabla mejoradas --}}
                    <thead class="table-dark"> {{-- Encabezado oscuro --}}
                        <tr>
                            <th>ID</th>
                            <th>Name</th> {{-- Cambiado de "Nombre" para consistencia, o puedes mantener "Nombre" --}}
                            {{-- <th>Description</th> Si tienes un campo de descripción y quieres mostrarlo --}}
                            <th>Actions</th> {{-- Cambiado de "Acciones" para consistencia --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category) {{-- Cambiado a @forelse para manejar el caso vacío --}}
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                {{-- <td>{{ $category->description ?? 'N/A' }}</td> --}}
                                <td>
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No categories found.</td> {{-- Ajusta el colspan según el número de columnas visibles --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- Opcional: Añadir enlaces de paginación si se usa --}}
            {{-- $categories->links() --}}
        </div>
    </div>
@endsection

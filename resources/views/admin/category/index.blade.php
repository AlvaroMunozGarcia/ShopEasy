@extends('layouts.admin')

@section('content')
  <div class="container mt-4">
    <h3>Lista de Categorías</h3>
    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Crear Categoría</a>
    
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($categories as $category)
          <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>
              <a href="{{ route('categories.show', $category) }}" class="btn btn-info btn-sm">Ver</a>
              <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">Editar</a>
              <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection

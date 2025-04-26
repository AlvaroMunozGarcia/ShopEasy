@extends('layouts.admin')

@section('content')
  <div class="container mt-4">
    <h3>Usuarios del sistema</h3>
    <a href="{{ route('') }}" class="btn btn-primary mb-3">Usuarios del sistema</a>
    
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Correo electrónico</th>
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

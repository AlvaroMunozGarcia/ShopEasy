@extends('layouts.admin')

@section('content')
  <div class="container mt-4">
    <h3>Detalles de la Categoría: {{ $category->name }}</h3>
    <p><strong>ID:</strong> {{ $category->id }}</p>
    <p><strong>Nombre:</strong> {{ $category->name }}</p>
    <p><strong>Descripción:</strong> {{ $category->description }}</p>
    <a href="{{ route('categories.index') }}" class="btn btn-primary">Volver al listado</a>
    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">Editar</a>
    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
  </div>
@endsection

@extends('layouts.admin')

@section('content')
  <div class="container mt-4">
    <h3>Editar Categoría: {{ $category->name }}</h3>
    <form action="{{ route('categories.update', $category) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label for="name" class="form-label">Nombre de la Categoría</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        <label for="description" class="form-label">Descripción</label>
        <input type="text" class="form-control" id="description" name="description" value="{{ $category->description }}" required>
      </div>
      <button type="submit" class="btn btn-success">Actualizar</button>
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
@endsection

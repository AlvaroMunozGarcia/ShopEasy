@extends('layouts.admin')

@section('content')
  <div class="container mt-4">
    <h3>Crear Nueva Categoría</h3>
    <form action="{{ route('categories.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="name" class="form-label">Nombre de la Categoría</label>
        <input type="text" class="form-control" id="name" name="name" required>
        <label for="description" class="form-label">Descripción</label>
        <input type="text" class="form-control" id="description" name="description" required>
      </div>
      <button type="submit" class="btn btn-success">Guardar</button>
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
@endsection

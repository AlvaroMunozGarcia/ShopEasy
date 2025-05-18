@extends('layouts.admin')

@section('title', 'Crear Nueva Categoría')

@section('page_header', 'Crear Nueva Categoría')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active" aria-current="page">Crear Nueva</li>
@endsection

@section('content')
  <div class="container mt-4">
    {{-- El H3 anterior se elimina ya que @page_header lo maneja --}}
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

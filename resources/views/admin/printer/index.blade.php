@extends('layouts.admin') {{-- Asegúrate que 'layouts.admin' sea tu plantilla principal de admin --}}

@section('title', 'Configuración de Impresora') {{-- Título específico de la página --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Configuración de Impresora Predeterminada</h3>
                </div>
                <div class="card-body">

                    {{-- Mostrar mensajes de éxito flash --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Error!</strong> Por favor corrige los siguientes errores:
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Formulario de edición --}}
                    {{-- La acción apunta a la ruta de actualización, pasando el objeto $printer --}}
                    <form action="{{ route('admin.printer.update', $printer) }}" method="POST">
                        @csrf {{-- Token de protección CSRF --}}
                        @method('PUT') {{-- Especifica el método HTTP PUT para la actualización --}}

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Impresora (Sistema)</label>
                            {{-- El valor viene de old() o del objeto $printer. Se añade clase 'is-invalid' si hay error --}}
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $printer->name) }}" required>
                            {{-- Muestra el mensaje de error específico para 'name' --}}
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Introduce el nombre exacto de la impresora tal como está configurada en el sistema operativo donde se ejecutarán las impresiones (ej. "POS-80C", "EPSON TM-T20II"). Este nombre será usado por la librería de impresión.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Aquí puedes añadir JS específico para esta página si lo necesitas más adelante --}}
@endpush
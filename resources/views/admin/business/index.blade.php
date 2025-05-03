{{-- Asumiendo que tienes un layout principal para el admin llamado admin.blade.php --}}
@extends('layouts.admin') {{-- Cambia 'layouts.admin' por el nombre de tu layout principal del admin --}}

@section('title', 'Información del Negocio') {{-- Título de la página --}}

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Gestionar Información del Negocio</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Información del Negocio</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Editar Información</h4>

                    {{-- Mostrar mensajes de éxito --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario para editar la información del negocio --}}
                    {{-- Asegúrate de que la ruta y el ID del negocio sean correctos --}}
                    <form action="{{ route('admin.business.update', $business->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Método HTTP para actualizar --}}

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Negocio</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $business->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $business->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $business->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección (Opcional)</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $business->address) }}">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="ruc" class="form-label">RUC / Identificación Fiscal</label>
                            <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{ old('ruc', $business->ruc) }}" required>
                            @error('ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($business->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($business->logo) }}" alt="Logo actual" style="max-height: 100px;">
                                    <p><small>Logo actual. Sube uno nuevo para reemplazarlo.</small></p>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar Información</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Si necesitas añadir CSS específico para esta página --}}
@endpush

@push('scripts')
{{-- Si necesitas añadir JS específico para esta página --}}
@endpush
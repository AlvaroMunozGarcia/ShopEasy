@extends('layouts.admin') {{-- Usar tu layout personalizado --}}

@section('title', 'Añadir Nuevo Cliente')

@section('page_header', 'Añadir Nuevo Cliente')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Añadir Nuevo</li>
@endsection

@section('content') {{-- Contenido principal para el @yield('content') --}}
    {{-- El H1 anterior se elimina ya que @page_header lo maneja --}}
    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                {{-- Name --}}
                <div class="mb-3"> {{-- Bootstrap 5 margin bottom --}}
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- DNI --}}
                <div class="mb-3">
                    <label for="dni" class="form-label">DNI <span class="text-danger">*</span></label>
                    <input type="text" name="dni" id="dni" class="form-control @error('dni') is-invalid @enderror" value="{{ old('dni') }}" required>
                    @error('dni')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- RUC --}}
                <div class="mb-3">
                    <label for="ruc" class="form-label">RUC</label>
                    <input type="text" name="ruc" id="ruc" class="form-control @error('ruc') is-invalid @enderror" value="{{ old('ruc') }}">
                    @error('ruc')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                    @error('address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Client</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

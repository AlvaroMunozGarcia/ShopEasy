@extends('layouts.admin')

@section('content_header')
    <h1>Crear Nuevo Cliente</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <x-admin.form-layout
                title="Formulario de Nuevo Cliente"
                action="{{ route('admin.customers.store') }}"
                cancelRoute="{{ route('admin.customers.index') }}"
            >
                <x-admin.input-field
                    name="first_name"
                    label="Nombres"
                    placeholder="Ej: Juan"
                    required
                />

                <x-admin.input-field
                    name="last_name"
                    label="Apellidos"
                    placeholder="Ej: Pérez"
                    required
                />

                <x-admin.input-field
                    type="email"
                    name="email"
                    label="Correo Electrónico"
                    placeholder="cliente@example.com"
                    required
                />

                <x-admin.input-field
                    type="tel"
                    name="phone"
                    label="Teléfono"
                    placeholder="Ej: 0987654321"
                />

                <x-admin.input-field type="textarea" name="address" label="Dirección" placeholder="Dirección completa del cliente" />
            </x-admin.form-layout>
        </div>
    </div>
</div>
@stop
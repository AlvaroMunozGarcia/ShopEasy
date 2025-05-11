@extends('layouts.admin')

@section('content_header')
    <h1>Editar Cliente</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <x-admin.form-layout
                title="Formulario de Edición de Cliente: {{ $customer->first_name }} {{ $customer->last_name }}"
                action="{{ route('admin.customers.update', $customer) }}"
                :isUpdate="true"
                cancelRoute="{{ route('admin.customers.index') }}"
            >
                <x-admin.input-field
                    name="first_name"
                    label="Nombres"
                    :model="$customer"
                    required
                />

                <x-admin.input-field
                    name="last_name"
                    label="Apellidos"
                    :model="$customer"
                    required
                />

                <x-admin.input-field
                    type="email"
                    name="email"
                    label="Correo Electrónico"
                    :model="$customer"
                    required
                />

                <x-admin.input-field
                    type="tel"
                    name="phone"
                    label="Teléfono"
                    :model="$customer"
                />

                <x-admin.input-field type="textarea" name="address" label="Dirección" :model="$customer" />

            </x-admin.form-layout>
        </div>
    </div>
</div>
@stop
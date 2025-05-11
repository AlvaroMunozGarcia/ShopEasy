@extends('layouts.admin')

@section('content_header')
    <h1>Crear Nuevo Rol</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <x-admin.form-layout
                title="Formulario de Nuevo Rol"
                action="{{ route('admin.roles.store') }}"
                cancelRoute="{{ route('admin.roles.index') }}"
            >
                <x-admin.input-field
                    name="name"
                    label="Nombre del Rol (ej: editor)"
                    placeholder="Nombre único para el rol"
                    required
                />

                {{-- Asumiendo que $permissions es Permission::pluck('name', 'id') --}}
                @if(isset($permissions) && $permissions->count())
                <x-admin.input-field type="select" name="permissions[]" label="Permisos" :options="$permissions" multiple />
                @endif

            </x-admin.form-layout>
        </div>
    </div>
</div>
@stop
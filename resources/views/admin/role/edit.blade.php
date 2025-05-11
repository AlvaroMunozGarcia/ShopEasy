@extends('layouts.admin')

@section('content_header')
    <h1>Editar Rol</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <x-admin.form-layout
                title="Formulario de Edición de Rol: {{ $role->name }}"
                action="{{ route('admin.roles.update', $role) }}"
                :isUpdate="true"
                cancelRoute="{{ route('admin.roles.index') }}"
            >
                <x-admin.input-field
                    name="name"
                    label="Nombre del Rol (ej: editor)"
                    :model="$role"
                    required
                />

                {{-- Asumiendo que $permissions es Permission::pluck('name', 'id') --}}
                {{-- y que $role->permissions es una colección de permisos asignados --}}
                @if(isset($permissions) && $permissions->count())
                <x-admin.input-field type="select" name="permissions[]" label="Permisos" :options="$permissions" :value="old('permissions', $role->permissions->pluck('id')->toArray())" multiple />
                @endif

            </x-admin.form-layout>
        </div>
    </div>
</div>
@stop
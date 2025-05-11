{{-- e:\ProyectoDAW\ShopEasy\resources\views\components\admin\form-layout.blade.php --}}
@props([
    'action',
    'method' => 'POST',
    'isUpdate' => false,
    'title',
    'enctype' => null,
    'cancelRoute' => url()->previous(), // Ruta para el botón "Cancelar"
    'submitText' => null, // Texto personalizado para el botón de envío
    'hideCard' => false // Opción para no renderizar el card si se usa en un modal, por ejemplo
])

@if(!$hideCard)
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
@endif
    <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" @if($enctype) enctype="{{ $enctype }}" @endif>
        @csrf
        @if($isUpdate && $method !== 'GET')
            @method('PUT')
        @endif

        <div class="{{ $hideCard ? '' : 'card-body' }}">
            @if ($errors->any() && !$errors->hasBag('default') && !$hideCard)
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> ¡Error!</h5>
                    Por favor, corrige los errores en el formulario.
                </div>
            @endif

            {{ $slot }} {{-- Aquí se insertarán los campos específicos del formulario --}}
        </div>
        <!-- /.card-body -->

        <div class="{{ $hideCard ? '' : 'card-footer' }}">
            <button type="submit" class="btn btn-primary">
                {{ $submitText ?? ($isUpdate ? 'Actualizar' : 'Guardar') }}
            </button>
            <a href="{{ $cancelRoute }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@if(!$hideCard)
</div>
<!-- /.card -->
@endif
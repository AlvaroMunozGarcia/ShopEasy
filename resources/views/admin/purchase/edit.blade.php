@extends('layouts.admin')

@section('content')
    <h1>Editar Compra #{{ $purchase->id }}</h1>

    <div class="card mt-3">
        <div class="card-body">
            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Comprobar si la edición está permitida según el estado --}}
            @if($purchase->status != 'VALID')
                <div class="alert alert-warning">
                    Esta compra está en estado '{{ $purchase->status == 'CANCELLED' ? 'Cancelada' : $purchase->status }}' y no puede ser editada.
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary float-end">Volver</a>
                </div>
            @else
                <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="edit-purchase-form">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        {{-- Selección de Proveedor (A menudo no editable después de crear) --}}
                        <div class="col-md-6">
                            <label for="provider_id" class="form-label">Proveedor</label>
                            <select name="provider_id" id="provider_id" class="form-select @error('provider_id') is-invalid @enderror" disabled> {{-- Típicamente deshabilitado --}}
                                <option value="">{{ $purchase->provider ? $purchase->provider->name : 'N/A' }}</option>
                                {{-- Si permites cambiar proveedor, poblar como en create.blade.php --}}
                                {{-- @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ (old('provider_id', $purchase->provider_id) == $provider->id) ? 'selected' : '' }}>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach --}}
                            </select>
                             {{-- Si está deshabilitado, no se necesita mensaje de error, pero se mantiene por consistencia si se hace editable --}}
                             @error('provider_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                             <small class="form-text text-muted">El proveedor generalmente no se puede cambiar después de crear la compra.</small>
                             {{-- Campo oculto para enviar el provider_id si está deshabilitado --}}
                             <input type="hidden" name="provider_id" value="{{ $purchase->provider_id }}">
                        </div>

                        {{-- Fecha de Compra --}}
                        <div class="col-md-6">
                            <label for="purchase_date" class="form-label">Fecha de Compra <span class="text-danger">*</span></label>
                            {{-- El input date requiere el formato YYYY-MM-DD para el 'value' --}}
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : '') }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    {{-- Sección Detalles de Compra (Solo Mostrar o Edición Compleja) --}}
                    <h4>Detalles de la Compra (Generalmente no editables)</h4>
                     <div class="alert alert-info">
                        La edición de los detalles del producto (productos, cantidades, precios) generalmente requiere cancelar y volver a crear la compra para mantener la integridad de los datos y el stock. Los detalles mostrados a continuación no se modificarán.
                     </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchase->purchaseDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->product ? $detail->product->name : 'N/A' }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>${{ number_format($detail->price, 2) }}</td>
                                        <td>${{ number_format($detail->quantity * $detail->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No hay detalles para esta compra.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>${{ number_format($purchase->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Si decides permitir la edición de detalles, reemplazarías la tabla
                         anterior con una estructura similar a create.blade.php, poblada con
                         datos existentes e incluyendo la lógica JavaScript. --}}

                    <hr>

                    {{-- Botones de Envío y Cancelar --}}
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Compra</button> {{-- Solo actualiza campos incluidos en el form (ej. purchase_date) --}}
                    </div>
                </form>
            @endif {{-- Fin del chequeo de estado editable --}}
        </div>
    </div>

@endsection

{{-- No se necesitan scripts específicos para esta vista de edición básica a menos que se añada edición de detalles --}}
{{-- @push('scripts')
    <script>
        // Añadir JS aquí si la edición de detalles es necesaria
    </script>
@endpush --}}

@extends('layouts.admin') 

@section('title', 'Editar Compra')

@section('page_header', 'Editar Compra #'.$purchase->id)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar Compra</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Formulario de Edición de Compra</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>¡Ups! Algo salió mal:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="edit-purchase-form">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="provider_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select name="provider_id" id="provider_id" class="form-select @error('provider_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un Proveedor</option>
                                        @foreach ($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('provider_id', $purchase->provider_id) == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tax" class="form-label">Impuesto (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" placeholder="Ej: 18" value="{{ old('tax', $purchase->tax) }}" min="0" step="0.01" required>
                                        @error('tax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Detalles de la Compra</h5>

                            <div class="row align-items-end mb-3">
                                <div class="col-md-4"> 
                                    <label for="product_id_select" class="form-label">Producto</label>
                                    <select id="product_id_select" class="form-select">
                                        <option value="">Seleccione un Producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                                {{ $product->name }} (Stock actual: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                 <div class="col-md-2">
                                    <label for="quantity_input" class="form-label">Cantidad</label>
                                    <input type="number" id="quantity_input" class="form-control" placeholder="Cantidad" min="1">
                                </div>
                                <div class="col-md-3"> 
                                    <label for="price_input" class="form-label">Precio Compra (€)</label>
                                    <input type="number" id="price_input" class="form-control" placeholder="Precio" min="0" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="add_product_button" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Añadir</button>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table id="purchase_details_table" class="table table-bordered table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio Compra (€)</th>
                                            <th class="text-end">Subtotal (€)</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Los detalles existentes se cargarán aquí mediante JavaScript --}}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal General:</strong></td>
                                            <td id="table_subtotal_general" class="text-end">0.00 €</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Impuesto (<span id="tax_percentage_label">{{ $purchase->tax }}</span>%):</strong></td>
                                            <td id="table_tax_amount" class="text-end">0.00 €</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>TOTAL COMPRA:</strong></td>
                                            <td id="table_total_purchase" class="text-end"><strong>0.00 €</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Actualizar Compra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    {{-- Si se usara select2, los estilos irían aquí, pero se cambió a form-select --}}
@endpush

@push('scripts')
    {{-- Si se usara select2, el script iría aquí --}}
    <script>
    $(function () {
        // Ya no se inicializa select2

        let detailIndex = 0; 
        function addProductToTable(productId, productName, quantity, price) {
            if (!productId || !productName || quantity <= 0 || price < 0) {
                console.error("Datos inválidos para añadir a la tabla", {productId, productName, quantity, price});
                return;
            }
            const subtotal = (quantity * price);
            const newRow = `
                <tr data-id="${productId}">
                    <td>
                        <input type="hidden" name="details[${detailIndex}][product_id]" value="${productId}">
                        ${productName}
                    </td>
                    <td>
                        <input type="hidden" name="details[${detailIndex}][quantity]" value="${quantity}">
                        ${quantity}
                    </td>
                    <td>
                        <input type="hidden" name="details[${detailIndex}][price]" value="${parseFloat(price).toFixed(2)}">
                        ${parseFloat(price).toFixed(2)} €
                    </td>
                    <td class="row-subtotal text-end">${subtotal.toFixed(2)} €</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-product-button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#purchase_details_table tbody').append(newRow);
            detailIndex++;
        }
        const existingDetails = @json($purchase->purchaseDetails);
        existingDetails.forEach(function(detail) {
            addProductToTable(detail.product_id, detail.product ? detail.product.name : 'Producto Desconocido', detail.quantity, parseFloat(detail.price));
        });
        calculateTotals(); 


        $('#add_product_button').on('click', function() {
            const selectedOption = $('#product_id_select').find('option:selected');
            const productId = selectedOption.val();
            const productName = selectedOption.text().split(' (Stock actual:')[0];
            const price = parseFloat($('#price_input').val() || 0);
            const quantity = parseInt($('#quantity_input').val() || 0);

            if (!productId) { alert('Seleccione un producto.'); return; }
            if (quantity <= 0) { alert('Ingrese una cantidad válida.'); return; }
            if (price < 0) { alert('Ingrese un precio de compra válido.'); return; } 

      

            addProductToTable(productId, productName, quantity, price);

            $('#product_id_select').val(''); // No necesita .trigger('change') para form-select estándar
            $('#quantity_input').val('');
            $('#price_input').val('');
            calculateTotals();
        });

        $('#purchase_details_table tbody').on('click', '.remove-product-button', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $('#tax').on('input', function() {
             calculateTotals();
             $('#tax_percentage_label').text($(this).val() || 0);
        });

        function calculateTotals() {
            let subtotalGeneral = 0;
            $('#purchase_details_table tbody tr').each(function() {
                subtotalGeneral += parseFloat($(this).find('.row-subtotal').text()) || 0;
            });

            const taxPercentage = parseFloat($('#tax').val() || 0);
            const taxAmount = subtotalGeneral * (taxPercentage / 100);
            const total = subtotalGeneral + taxAmount;

            $('#table_subtotal_general').text(subtotalGeneral.toFixed(2) + ' €');
            $('#table_tax_amount').text(taxAmount.toFixed(2) + ' €');
            $('#table_total_purchase').html('<strong>' + total.toFixed(2) + ' €</strong>');
        }
    });
    </script>
@endpush
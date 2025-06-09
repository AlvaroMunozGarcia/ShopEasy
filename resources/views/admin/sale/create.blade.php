@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Registrar Nueva Venta')

@section('page_header', 'Registrar Nueva Venta')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Registrar Nueva</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11"> {{-- Columnas más anchas para este formulario --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Formulario de Registro de Venta</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('sales.store') }}" method="POST" id="create-sale-form">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> ¡Ups! Algo salió mal:</strong>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="client_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>Seleccione un Cliente</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} ({{ $client->dni ?? $client->ruc }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tax" class="form-label">Impuesto (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" placeholder="Ej: 18" value="{{ old('tax', 18) }}" min="0" step="0.01" required>
                                        @error('tax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Detalles de la Venta</h5>

                            <div class="row align-items-end mb-3">
                                <div class="col-md-4">
                                    <label for="product_id_select" class="form-label">Producto <span class="text-danger">*</span></label>
                                    <select id="product_id_select" class="form-select">
                                        <option value="" selected disabled>Seleccione un Producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock }}">
                                                {{ $product->name }} (Stock: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                 <div class="col-md-2">
                                    <label for="quantity_input" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                    <input type="number" id="quantity_input" class="form-control" placeholder="Cant." min="1" step="1">
                                </div>
                                <div class="col-md-2">
                                    <label for="price_input" class="form-label">Precio (€) <span class="text-danger">*</span></label>
                                    <input type="number" id="price_input" class="form-control" placeholder="Precio" min="0" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <label for="discount_input" class="form-label">Desc. (%)</label>
                                    <input type="number" id="discount_input" class="form-control" placeholder="Desc. %" value="0" min="0" max="100" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="add_product_button" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Añadir</button>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table id="sale_details_table" class="table table-bordered table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio (€)</th>
                                            <th class="text-center">Desc. (%)</th>
                                            <th class="text-end">Subtotal (€)</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Filas de productos se añadirán aquí por JS --}}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td id="table_subtotal" class="text-end">0.00 €</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Impuesto (<span id="tax_percentage_label">{{ old('tax', 18) }}</span>%):</strong></td>
                                            <td id="table_tax" class="text-end">0.00 €</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                            <td id="table_total" class="text-end"><strong>0.00 €</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr>
                             @error('details') {{-- Error general para el array de detalles --}}
                                <div class="alert alert-danger mt-3">{{ $message }}</div>
                             @enderror

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4" id="submit-sale">
                                    <i class="bi bi-save me-1"></i> Guardar Venta
                                </button>
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
    {{-- Select2 CSS ya no es necesario si se usa form-select de Bootstrap --}}
@endpush

@push('scripts')
    {{-- Select2 JS ya no es necesario --}}
    <script>
        $(function () {
            // Inicialización de Select2 ya no es necesaria.
            // Los form-select de Bootstrap funcionan de forma nativa.

            $('#product_id_select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                if (price) {
                    $('#price_input').val(parseFloat(price).toFixed(2));
                } else {
                    $('#price_input').val('');
                }
            });
        });
    </script>
    {{-- El script de lógica de la tabla de detalles se mantiene, pero se ajustarán los selectores si es necesario --}}
    <script>
        $(document).ready(function() {
            // let detailIndex = 0; // Ya no es necesario para los nombres de los inputs
            $('#add_product_button').on('click', function() {
                const selectedOption = $('#product_id_select').find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.text().split(' (Stock:')[0]; 
                let price = parseFloat($('#price_input').val());
                const stock = parseInt(selectedOption.data('stock') || 0);
                const quantity = parseInt($('#quantity_input').val() || 0);
                const discount = parseFloat($('#discount_input').val() || 0);

                if (!productId) { alert('Seleccione un producto.'); return; }
                if (isNaN(quantity) || quantity <= 0) { alert('Ingrese una cantidad válida.'); return; }
                if (isNaN(price) || price <= 0) {
                    const dataPrice = parseFloat(selectedOption.data('price'));
                    if (isNaN(dataPrice) || dataPrice <= 0) {
                        alert('Ingrese un precio válido para el producto.'); return;
                    }
                    price = dataPrice;
                    $('#price_input').val(price.toFixed(2)); 
                }
                if (isNaN(discount) || discount < 0 || discount > 100) { alert('El descuento debe estar entre 0 y 100.'); return; }
                let currentQuantityInTable = 0;
                $(`#sale_details_table tbody tr[data-id="${productId}"] input[name$="[quantity]"]`).each(function() { // Ajustado para buscar name que termina en [quantity]
                    currentQuantityInTable += parseInt($(this).val());
                });

                if (quantity + currentQuantityInTable > stock) {
                    alert(`No hay suficiente stock para "${productName}". Stock disponible: ${stock - currentQuantityInTable}.`);
                    return;
                }
                const existingRow = $(`#sale_details_table tbody tr[data-id="${productId}"]`);
                if (existingRow.length > 0) {
                    const existingQuantity = parseInt(existingRow.find('input[name$="[quantity]"]').val());
                    const newQuantity = existingQuantity + quantity; // Suma la nueva cantidad a la existente
                    existingRow.find('input[name="quantity[]"]').val(newQuantity); // Actualiza el input oculto
                    existingRow.find('td:nth-child(2)').text(newQuantity); 

                    existingRow.find('input[name="price[]"]').val(price.toFixed(2)); // Actualiza el input oculto
                    existingRow.find('td:nth-child(3)').text(price.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    existingRow.find('input[name="discount[]"]').val(discount.toFixed(2)); // Actualiza el input oculto
                    existingRow.find('td:nth-child(4)').text(discount.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%');
                    
                    const newSubtotal = (newQuantity * price) * (1 - discount / 100);
                    existingRow.find('.row-subtotal').text(newSubtotal.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else {
                    const subtotal = (quantity * price) * (1 - discount / 100);
                    const newRowHTML = `
                        <tr data-id="${productId}">
                            <td>
                                <input type="hidden" name="product_id[]" value="${productId}">
                                ${productName}
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="quantity[]" value="${quantity}">
                                ${quantity}
                            </td>
                            <td class="text-end">
                                <input type="hidden" name="price[]" value="${price.toFixed(2)}">
                                ${price.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="discount[]" value="${discount.toFixed(2)}">
                                ${discount.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%
                            </td>
                            <td class="row-subtotal text-end">${subtotal.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-product-button">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#sale_details_table tbody').append(newRowHTML);
                    // detailIndex++; // Ya no es necesario
                }
                $('#product_id_select').val(''); // No es necesario .trigger('change') para form-select estándar
                $('#quantity_input').val('');
                $('#price_input').val('');
                $('#discount_input').val('0');

                calculateTotals();
            });

            $('#sale_details_table tbody').on('click', '.remove-product-button', function() {
                $(this).closest('tr').remove();
                calculateTotals(); 
            });

            $('#tax').on('input', function() {
                 const taxValue = parseFloat($(this).val());
                 if (isNaN(taxValue) || taxValue < 0) {
                     $('#tax_percentage_label').text('0');
                 } else {
                     $('#tax_percentage_label').text(taxValue);
                 }
                 calculateTotals();
            });

            function calculateTotals() {
                let subtotalGeneral = 0;
                $('#sale_details_table tbody tr').each(function() {
                    const subtotalText = $(this).find('.row-subtotal').text().replace('€', '').trim();
                    const numericSubtotal = parseFloat(subtotalText.replace(/\./g, '').replace(',', '.'));
                    if (!isNaN(numericSubtotal)) {
                        subtotalGeneral += numericSubtotal;
                    }
                });

                const taxPercentage = parseFloat($('#tax').val() || 0);
                const taxAmount = subtotalGeneral * (taxPercentage / 100);
                const total = subtotalGeneral + taxAmount;

                $('#table_subtotal').text(subtotalGeneral.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €');
                $('#table_tax').text(taxAmount.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €');
                $('#table_total').html('<strong>' + total.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €</strong>');
            }
            const initialTax = parseFloat($('#tax').val());
            if (!isNaN(initialTax) && initialTax >= 0) {
                $('#tax_percentage_label').text(initialTax);
            } else {
                 $('#tax_percentage_label').text('18'); 
            }
             calculateTotals(); 

            $('#create-sale-form').on('submit', function(event) {
                if ($('#sale_details_table tbody tr').length === 0) {
                    alert('Debe añadir al menos un producto a la venta antes de guardar.');
                    event.preventDefault(); 
                    $('#product_id_select').focus();
                    return;
                }
                const taxRate = parseFloat($('#tax').val());
                if (isNaN(taxRate) || taxRate < 0) {
                    alert('Por favor, ingrese un impuesto válido (0 o mayor) antes de guardar.');
                    event.preventDefault();
                    $('#tax').focus();
                    return;
                }
                $('#submit-sale').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...'
                );
            });
        });
    </script>
@endpush

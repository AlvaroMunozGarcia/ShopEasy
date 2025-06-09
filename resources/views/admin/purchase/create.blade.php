@extends('layouts.admin')

@section('title', 'Registrar Nueva Compra')

@section('page_header', 'Registrar Nueva Compra')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Registrar Nueva</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11"> {{-- Columnas un poco más anchas para este formulario --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                 <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('purchases.store') }}" method="POST" id="create-purchase-form">
                            @csrf

                             <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="provider_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select name="provider_id" id="provider_id" class="form-select @error('provider_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>Seleccione un proveedor</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="purchase_date" class="form-label">Fecha de Compra <span class="text-danger">*</span></label>
                                    <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="tax" class="form-label">Impuesto (%)<span class="text-danger">*</span></label>
                                    <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" required placeholder="18" value="{{ old('tax', 18) }}" step="0.01" min="0">
                                    @error('tax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr class="my-3">
                            <h5 class="mb-3">Añadir Producto a la Compra</h5>
                            <div class="row align-items-end mb-3">
                                <div class="col-md-5">
                                    <label for="product_id" class="form-label">Producto <span class="text-danger">*</span></label>
                                    <select id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                                        <option value="" selected disabled>Seleccione un producto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id') 
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="quantity" class="form-label">Cantidad<span class="text-danger">*</span></label>
                                    <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" step="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="price" class="form-label">Precio Unitario<span class="text-danger">*</span></label>
                                    <input type="number" id="price" class="form-control @error('price') is-invalid @enderror" step="0.01" min="0">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                     <button type="button" id="add-product-row" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Añadir</button>
                                </div>
                            </div>
                            <hr>
                            <h4>Detalles De Compra</h4>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">Eliminar</th>
                                            <th>Producto</th>
                                            <th style="width: 15%;" class="text-end">Precio Unit. (€)</th>
                                            <th style="width: 10%;" class="text-end">Cantidad</th>
                                            <th style="width: 20%;" class="text-end">SubTotal (€)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchase-details-body">
                                    </tbody>
                                </table>
                            </div>

                            {{-- TABLA DE TOTALES --}}
                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <td class="text-end"><strong>TOTAL:</strong></td>
                                                <td class="text-end" style="width: 40%;">
                                                    <strong id="total">0.00 €</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end">
                                                    <strong>TOTAL IMPUESTO (<span id="tax-rate-label">{{ old('tax', 18) }}</span>%):</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong id="total_impuesto">0.00 €</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>TOTAL A PAGAR:</strong></td>
                                                <td class="text-end">
                                                    <strong id="total_pagar_html">0.00 €</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                             <hr>
                             @error('details')
                                <div class="alert alert-danger">{{ $message }}</div>
                             @enderror
                             @error('details.*.product_id')
                                 <div class="alert alert-danger">Error en la selección de producto en los detalles.</div>
                             @enderror
                             @error('details.*.quantity')
                                 <div class="alert alert-danger">Error en la cantidad de producto en los detalles.</div>
                             @enderror
                              @error('details.*.price')
                                 <div class="alert alert-danger">Error en el precio de producto en los detalles.</div>
                             @enderror

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4" id="submit-purchase">
                                    <i class="bi bi-save me-1"></i> Guardar Compra
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Cargado. Buscando elementos...'); 
    const addProductBtn = document.getElementById('add-product-row');
    const detailsTableBody = document.getElementById('purchase-details-body');
    const productIdSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('price');
    const taxInput = document.getElementById('tax');
    const totalElement = document.getElementById('total');
    const totalImpuestoElement = document.getElementById('total_impuesto');
    const totalPagarElement = document.getElementById('total_pagar_html');
    const taxRateLabel = document.getElementById('tax-rate-label');
    const form = document.getElementById('create-purchase-form');
    const submitButton = document.getElementById('submit-purchase');
    let detailIndex = 0;
    console.log('Botón Añadir:', addProductBtn); 
    console.log('Select Producto:', productIdSelect); 
    console.log('Input Cantidad:', quantityInput); 
    console.log('Input Precio:', priceInput); 
    console.log('TBody Detalles:', detailsTableBody);
    console.log('Input Impuesto:', taxInput); 
    console.log('Formulario:', form); 
    function formatCurrency(value) {
        const numberValue = parseFloat(value);
        if (isNaN(numberValue)) { return 'PEN 0.00'; }
        return `${numberValue.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;
    }

    function calculateAndUpdateTotals() {
        console.log('Calculando totales...'); // <-- DEBUG: Verifica si se llama a esta función
        let currentSubTotal = 0;
        const rows = detailsTableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const hiddenPriceInput = row.querySelector('input[name$="[price]"]');
            const hiddenQuantityInput = row.querySelector('input[name$="[quantity]"]');

            if (hiddenPriceInput && hiddenQuantityInput) {
                const price = parseFloat(hiddenPriceInput.value);
                const quantity = parseFloat(hiddenQuantityInput.value);
                if (!isNaN(price) && !isNaN(quantity)) {
                     currentSubTotal += price * quantity;
                } else {
                    console.warn('Fila encontrada con precio/cantidad no numérico:', row); // <-- DEBUG: Podría indicar un problema al añadir la fila
                }
            } else {
                 console.warn('Fila encontrada sin inputs ocultos de precio/cantidad:', row); // <-- DEBUG: Podría indicar un problema al añadir la fila
            }
        });

        const taxRate = parseFloat(taxInput.value) || 0;
        const taxAmount = currentSubTotal * (taxRate / 100);
        const totalPayable = currentSubTotal + taxAmount;

        console.log(`SubTotal: ${currentSubTotal}, TaxRate: ${taxRate}, TaxAmount: ${taxAmount}, TotalPayable: ${totalPayable}`); // <-- DEBUG: Muestra los valores calculados

        totalElement.textContent = formatCurrency(currentSubTotal);
        totalImpuestoElement.textContent = formatCurrency(taxAmount);
        totalPagarElement.textContent = formatCurrency(totalPayable);
        if (taxRateLabel) { taxRateLabel.textContent = taxRate; }
    }

    function addProductRow() {
        console.log('Función addProductRow INICIADA'); 

        const productId = productIdSelect.value;
        if (!productId) {
            alert('Por favor, seleccione un producto.');
            productIdSelect.focus();
            console.log('Validación fallida: No se seleccionó producto.'); // <-- DEBUG
            return;
        }
        const productName = productIdSelect.options[productIdSelect.selectedIndex].text;
        const quantity = parseFloat(quantityInput.value);
        const price = parseFloat(priceInput.value);
        if (isNaN(quantity) || quantity <= 0) {
            alert('Por favor, ingrese una cantidad válida (mayor que 0).');
            quantityInput.focus();
            console.log('Validación fallida: Cantidad inválida.', quantityInput.value); // <-- DEBUG
            return;
        }
        if (isNaN(price) || price < 0) {
            alert('Por favor, ingrese un precio válido (0 o mayor).');
            priceInput.focus();
            console.log('Validación fallida: Precio inválido.', priceInput.value); // <-- DEBUG
            return;
        }
        const taxRate = parseFloat(taxInput.value);
         if (isNaN(taxRate) || taxRate < 0) {
            alert('Por favor, ingrese un impuesto válido (0 o mayor) antes de añadir productos.');
            taxInput.focus();
            console.log('Validación fallida: Impuesto inválido.', taxInput.value); // <-- DEBUG
            return;
        }

        console.log(`Añadiendo/Actualizando Producto: ID=${productId}, Nombre=${productName}, Cantidad=${quantity}, Precio=${price}`); // <-- DEBUG
        let existingRow = null;
        detailsTableBody.querySelectorAll('input[name$="[product_id]"][value="' + productId + '"]').forEach(input => {
             existingRow = input.closest('tr');
        });


        if (existingRow) {
            console.log('Producto encontrado en la tabla. Actualizando cantidad.'); // <-- DEBUG
            if (confirm(`El producto "${productName}" ya está en la lista. ¿Desea sumar la cantidad ${quantity}?`)) {
                const existingQuantityInput = existingRow.querySelector('input[name$="[quantity]"]');
                const quantityCell = existingRow.cells[3];
                const subtotalCell = existingRow.cells[4]; 
                const existingPrice = parseFloat(existingRow.querySelector('input[name$="[price]"]').value);

                const currentQuantity = parseFloat(existingQuantityInput.value) || 0;
                const newQuantity = currentQuantity + quantity;

                console.log(`Actualizando cantidad: ${currentQuantity} + ${quantity} = ${newQuantity}`); // <-- DEBUG

                existingQuantityInput.value = newQuantity; // Actualiza input oculto
                quantityCell.textContent = newQuantity; // Actualiza celda visible

                const newSubtotal = newQuantity * existingPrice;
                subtotalCell.textContent = formatCurrency(newSubtotal); // Actualiza celda visible
                console.log(`Nueva cantidad: ${newQuantity}, Nuevo subtotal: ${newSubtotal}`); // <-- DEBUG
            } else {
                 console.log('Usuario canceló la actualización de cantidad.'); // <-- DEBUG
                return; 
            }
        } else {
            console.log('Producto nuevo. Creando nueva fila.'); 
            const newRow = document.createElement('tr');
            const subtotal = quantity * price;
            newRow.innerHTML = `
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-product-row" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
                <td>
                    ${productName}
                    <input type="hidden" name="details[${detailIndex}][product_id]" value="${productId}">
                </td>
                <td class="text-end">
                    ${formatCurrency(price)}
                    <input type="hidden" name="details[${detailIndex}][price]" value="${price.toFixed(2)}">
                </td>
                <td class="text-end">
                    ${quantity}
                    <input type="hidden" name="details[${detailIndex}][quantity]" value="${quantity}">
                </td>
                <td class="text-end">${formatCurrency(subtotal)}</td>
            `;
            detailsTableBody.appendChild(newRow); 
            console.log(`Fila añadida para el índice ${detailIndex}`); 
            detailIndex++; 
        }

        productIdSelect.value = ''; 
        quantityInput.value = '';  
        priceInput.value = '';      
        productIdSelect.focus();    
        console.log('Campos limpiados, recalculando totales...'); 
        calculateAndUpdateTotals(); 
    }

    // --- Event Listeners ---

    if (addProductBtn) {
        addProductBtn.addEventListener('click', addProductRow); 
        console.log('Event listener añadido al botón Añadir.'); 
    } else {
        console.error("Error Crítico: Botón 'add-product-row' NO encontrado. Verifica el ID en el HTML.");
    }
    if (detailsTableBody) {
        detailsTableBody.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-product-row');
            if (removeButton) {
                console.log('Botón Eliminar clicado.'); 
                const rowToRemove = removeButton.closest('tr');
                if (rowToRemove) {
                    rowToRemove.remove();
                    console.log('Fila eliminada.'); 
                    calculateAndUpdateTotals(); 
                }
            }
        });
        console.log('Event listener añadido al TBody para eliminar filas.'); 
    } else {
         console.error("Error Crítico: Tbody 'purchase-details-body' NO encontrado. Verifica el ID en el HTML.");
    }
    if (taxInput) {
        taxInput.addEventListener('input', calculateAndUpdateTotals);
         console.log('Event listener añadido al input de Impuesto.'); 
    } else {
         console.error("Error Crítico: Input 'tax' NO encontrado. Verifica el ID en el HTML.");
    }
    console.log('Calculando totales iniciales al cargar la página...'); 
    calculateAndUpdateTotals();
     if(form) {
         form.addEventListener('submit', function(event) {
             console.log('Formulario SUBMIT intentado.'); 
             if (detailsTableBody.rows.length === 0) {
                 alert('Debe añadir al menos un producto a la compra antes de guardar.');
                 console.log('Validación de envío fallida: No hay detalles.'); 
                 event.preventDefault(); 
                 productIdSelect.focus();
                 return;
             }

             const taxRate = parseFloat(taxInput.value);
             if (isNaN(taxRate) || taxRate < 0) {
                alert('Por favor, ingrese un impuesto válido (0 o mayor) antes de guardar.');
                console.log('Validación de envío fallida: Impuesto inválido.', taxInput.value); 
                event.preventDefault(); 
                taxInput.focus();
                return;
             }
             console.log('Validaciones de envío superadas. Deshabilitando botón Guardar.'); 
             if (submitButton) {
                 submitButton.disabled = true;
                 submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
             }
         });
         console.log('Event listener añadido al Formulario para el evento submit.');
     } else {
          console.error("Error Crítico: Formulario 'create-purchase-form' NO encontrado. Verifica el ID en el HTML.");
     }

});
</script>
@endpush

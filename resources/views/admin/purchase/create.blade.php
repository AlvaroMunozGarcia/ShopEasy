@extends('layouts.admin')

@section('title', 'Registrar Nueva Compra')

@section('page_header', 'Registrar Nueva Compra')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Registrar Nueva</li>
@endsection

@section('content')
    {{-- El H1 anterior se elimina ya que @page_header lo maneja --}}
    <div class="card mt-3">
        <div class="card-body">
            {{-- Mostrar errores de validación si los hubiera --}}
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

            <form action="{{ route('purchases.store') }}" method="POST" id="create-purchase-form">
                @csrf

                 <div class="row mb-3">
                    {{-- Selección de Proveedor --}}
                    <div class="col-md-6">
                        <label for="provider_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'provider_id' --}}
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

                    {{-- Fecha de Compra --}}
                    <div class="col-md-6">
                        <label for="purchase_date" class="form-label">Fecha de Compra <span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'purchase_date' --}}
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                     {{-- Impuesto --}}
                    <div class="col-md-6">
                        <label for="tax" class="form-label">Impuesto (%)<span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'tax' --}}
                        <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" required placeholder="18" value="{{ old('tax', 18) }}" step="0.01" min="0">
                        @error('tax')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Separador visual para campos de añadir producto --}}
                    <div class="col-12"><hr class="my-3"><h5>Añadir Producto a la Compra</h5></div>

                    {{-- Selección de Producto (para añadir a la tabla) --}}
                    <div class="col-md-6">
                        <label for="product_id" class="form-label">Producto <span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'product_id' --}}
                        <select id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                            <option value="" selected disabled>Seleccione un producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') {{-- Este error probablemente no se mostrará ya que el select no tiene 'name' --}}
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Cantidad (para añadir a la tabla) --}}
                    <div class="col-md-3">
                        <label for="quantity" class="form-label">Cantidad<span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'quantity' --}}
                        <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" step="1">
                         {{-- Este error probablemente no se mostrará ya que el input no tiene 'name' --}}
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Precio (para añadir a la tabla) --}}
                    <div class="col-md-3">
                        <label for="price" class="form-label">Precio Unitario<span class="text-danger">*</span></label>
                        {{-- Asegúrate que este ID es 'price' --}}
                        <input type="number" id="price" class="form-control @error('price') is-invalid @enderror" step="0.01" min="0">
                         {{-- Este error probablemente no se mostrará ya que el input no tiene 'name' --}}
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botón para añadir el producto a la tabla --}}
                    <div class="col-12 mt-2 text-end">
                         {{-- Asegúrate que este ID es 'add-product-row' --}}
                         <button type="button" id="add-product-row" class="btn btn-success"><i class="bi bi-plus-circle"></i> Añadir Producto a la Lista</button>
                    </div>
                </div>


                <hr>

                {{-- TABLA DE DETALLES --}}
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
                        {{-- Asegúrate que este ID es 'purchase-details-body' --}}
                        <tbody id="purchase-details-body">
                            {{-- Las filas se añadirán aquí dinámicamente --}}
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
                                        {{-- Asegúrate que este ID es 'total' --}}
                                        <strong id="total">0.00 €</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        {{-- Asegúrate que este ID es 'tax-rate-label' --}}
                                        <strong>TOTAL IMPUESTO (<span id="tax-rate-label">{{ old('tax', 18) }}</span>%):</strong>
                                    </td>
                                    <td class="text-end">
                                         {{-- Asegúrate que este ID es 'total_impuesto' --}}
                                        <strong id="total_impuesto">0.00 €</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>TOTAL A PAGAR:</strong></td>
                                    <td class="text-end">
                                         {{-- Asegúrate que este ID es 'total_pagar_html' --}}
                                        <strong id="total_pagar_html">0.00 €</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                 <hr>
                 {{-- Errores específicos de los detalles (si los envías desde el backend) --}}
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


                 <div class="d-flex justify-content-end">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    {{-- Asegúrate que este ID es 'submit-purchase' --}}
                    <button type="submit" class="btn btn-primary" id="submit-purchase">Guardar Compra</button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Cargado. Buscando elementos...'); // <-- DEBUG: Verifica que el script se inicia

    // --- Elementos del DOM (Verifica que los IDs coincidan con tu HTML) ---
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

    // --- Verifica si los elementos clave existen ---
    console.log('Botón Añadir:', addProductBtn); // <-- DEBUG: ¿Encuentra el botón? Debería mostrar el elemento, no null.
    console.log('Select Producto:', productIdSelect); // <-- DEBUG: ¿Encuentra el select?
    console.log('Input Cantidad:', quantityInput); // <-- DEBUG: ¿Encuentra el input cantidad?
    console.log('Input Precio:', priceInput); // <-- DEBUG: ¿Encuentra el input precio?
    console.log('TBody Detalles:', detailsTableBody); // <-- DEBUG: ¿Encuentra el tbody?
    console.log('Input Impuesto:', taxInput); // <-- DEBUG: ¿Encuentra el input tax?
    console.log('Formulario:', form); // <-- DEBUG: ¿Encuentra el form?

    // --- Funciones ---

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
        console.log('Función addProductRow INICIADA'); // <-- DEBUG: Verifica si la función se ejecuta al hacer clic

        const productId = productIdSelect.value;
        // Validación de Producto Seleccionado
        if (!productId) {
            alert('Por favor, seleccione un producto.');
            productIdSelect.focus();
            console.log('Validación fallida: No se seleccionó producto.'); // <-- DEBUG
            return;
        }
        const productName = productIdSelect.options[productIdSelect.selectedIndex].text;
        const quantity = parseFloat(quantityInput.value);
        const price = parseFloat(priceInput.value);

        // Validaciones básicas de Cantidad y Precio
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
        // Validación de Impuesto (Asegura que haya un valor antes de añadir)
        const taxRate = parseFloat(taxInput.value);
         if (isNaN(taxRate) || taxRate < 0) {
            alert('Por favor, ingrese un impuesto válido (0 o mayor) antes de añadir productos.');
            taxInput.focus();
            console.log('Validación fallida: Impuesto inválido.', taxInput.value); // <-- DEBUG
            return;
        }

        console.log(`Añadiendo/Actualizando Producto: ID=${productId}, Nombre=${productName}, Cantidad=${quantity}, Precio=${price}`); // <-- DEBUG

        // Verificar si el producto ya existe
        let existingRow = null;
        // Busca un input oculto que termine en [product_id] y tenga el valor del producto seleccionado
        detailsTableBody.querySelectorAll('input[name$="[product_id]"][value="' + productId + '"]').forEach(input => {
             existingRow = input.closest('tr');
        });


        if (existingRow) {
            console.log('Producto encontrado en la tabla. Actualizando cantidad.'); // <-- DEBUG
            // Producto duplicado: Actualizar cantidad
            if (confirm(`El producto "${productName}" ya está en la lista. ¿Desea sumar la cantidad ${quantity}?`)) {
                const existingQuantityInput = existingRow.querySelector('input[name$="[quantity]"]');
                const quantityCell = existingRow.cells[3]; // Asume que la 4ª celda (índice 3) es la cantidad visible
                const subtotalCell = existingRow.cells[4]; // Asume que la 5ª celda (índice 4) es el subtotal visible
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
                return; // No hacer nada si cancela
            }
        } else {
            console.log('Producto nuevo. Creando nueva fila.'); // <-- DEBUG
            // Producto nuevo: Crear nueva fila
            const newRow = document.createElement('tr');
            const subtotal = quantity * price;

            // IMPORTANTE: Incluir los inputs ocultos para que los datos se envíen al backend
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
            detailsTableBody.appendChild(newRow); // Añade la fila al tbody
            console.log(`Fila añadida para el índice ${detailIndex}`); // <-- DEBUG
            detailIndex++; // Incrementa el índice para el siguiente producto único
        }

        // Limpiar campos de entrada y recalcular totales
        productIdSelect.value = ''; // Resetea el select
        quantityInput.value = '';   // Limpia cantidad
        priceInput.value = '';      // Limpia precio
        productIdSelect.focus();    // Pone el foco de nuevo en el select de producto
        console.log('Campos limpiados, recalculando totales...'); // <-- DEBUG
        calculateAndUpdateTotals(); // Llama a la función para actualizar los totales
    }

    // --- Event Listeners ---

    // Click en botón Añadir
    if (addProductBtn) {
        addProductBtn.addEventListener('click', addProductRow); // Llama a la función directamente
        console.log('Event listener añadido al botón Añadir.'); // <-- DEBUG
    } else {
        // Este error debería aparecer en la consola si el ID del botón está mal
        console.error("Error Crítico: Botón 'add-product-row' NO encontrado. Verifica el ID en el HTML.");
    }

    // Click en botón Eliminar (usando delegación de eventos en el tbody)
    if (detailsTableBody) {
        detailsTableBody.addEventListener('click', function(event) {
            // Busca si el clic fue en un botón con la clase 'remove-product-row' o dentro de él
            const removeButton = event.target.closest('.remove-product-row');
            if (removeButton) {
                console.log('Botón Eliminar clicado.'); // <-- DEBUG
                const rowToRemove = removeButton.closest('tr');
                if (rowToRemove) {
                    rowToRemove.remove();
                    console.log('Fila eliminada.'); // <-- DEBUG
                    calculateAndUpdateTotals(); // Recalcula después de eliminar
                }
            }
        });
        console.log('Event listener añadido al TBody para eliminar filas.'); // <-- DEBUG
    } else {
         // Este error debería aparecer si el ID del tbody está mal
         console.error("Error Crítico: Tbody 'purchase-details-body' NO encontrado. Verifica el ID en el HTML.");
    }


    // Cambio en el input de Impuesto
    if (taxInput) {
        taxInput.addEventListener('input', calculateAndUpdateTotals);
         console.log('Event listener añadido al input de Impuesto.'); // <-- DEBUG
    } else {
         // Este error debería aparecer si el ID del input tax está mal
         console.error("Error Crítico: Input 'tax' NO encontrado. Verifica el ID en el HTML.");
    }


    // Calcular totales al cargar la página (útil si hay datos 'old' de Laravel o si se edita)
    console.log('Calculando totales iniciales al cargar la página...'); // <-- DEBUG
    calculateAndUpdateTotals();

     // --- Validación del Formulario antes de Enviar ---
     if(form) {
         form.addEventListener('submit', function(event) {
             console.log('Formulario SUBMIT intentado.'); // <-- DEBUG

             // Validación 1: Debe haber al menos un detalle
             if (detailsTableBody.rows.length === 0) {
                 alert('Debe añadir al menos un producto a la compra antes de guardar.');
                 console.log('Validación de envío fallida: No hay detalles.'); // <-- DEBUG
                 event.preventDefault(); // Detiene el envío del formulario
                 productIdSelect.focus();
                 return;
             }

             // Validación 2: El impuesto debe ser válido
             const taxRate = parseFloat(taxInput.value);
             if (isNaN(taxRate) || taxRate < 0) {
                alert('Por favor, ingrese un impuesto válido (0 o mayor) antes de guardar.');
                console.log('Validación de envío fallida: Impuesto inválido.', taxInput.value); // <-- DEBUG
                event.preventDefault(); // Detiene el envío
                taxInput.focus();
                return;
             }

             // Si pasa las validaciones, deshabilita el botón para evitar doble envío
             console.log('Validaciones de envío superadas. Deshabilitando botón Guardar.'); // <-- DEBUG
             if (submitButton) {
                 submitButton.disabled = true;
                 // Cambia el texto/contenido para indicar que se está procesando
                 submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
             }
         });
         console.log('Event listener añadido al Formulario para el evento submit.'); // <-- DEBUG
     } else {
          // Este error debería aparecer si el ID del form está mal
          console.error("Error Crítico: Formulario 'create-purchase-form' NO encontrado. Verifica el ID en el HTML.");
     }

});
</script>
@endpush

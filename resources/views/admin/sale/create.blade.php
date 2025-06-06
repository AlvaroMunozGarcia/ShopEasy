@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Registrar Nueva Venta')

@section('page_header', 'Registrar Nueva Venta')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Registrar Nueva</li>
@endsection

@section('content')
<div class="content-wrapper">
    {{-- La cabecera anterior con H1 y breadcrumbs se elimina,
         ya que @page_header y @breadcrumbs del layout principal
         se encargarán de esto.
    --}}

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Formulario de Registro de Venta</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('sales.store') }}" method="POST">
                            @csrf
                            <div class="card-body">

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h5><i class="icon fas fa-ban"></i> ¡Ups! Algo salió mal:</h5>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client_id">Cliente</label>
                                            <select name="client_id" id="client_id" class="form-control select2 @error('client_id') is-invalid @enderror" style="width: 100%;" required>
                                                <option value="">Seleccione un Cliente</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }} ({{ $client->dni ?? $client->ruc }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tax">Impuesto (%)</label>
                                            <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" placeholder="Ej: 18" value="{{ old('tax', 18) }}" min="0" step="0.01" required>
                                             @error('tax')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4 class="mb-3">Detalles de la Venta</h4>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_id_select">Producto</label>
                                            <select id="product_id_select" class="form-control select2" style="width: 100%;">
                                                <option value="">Seleccione un Producto</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock }}">
                                                        {{ $product->name }} (Stock: {{ $product->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="quantity_input">Cantidad</label>
                                            <input type="number" id="quantity_input" class="form-control" placeholder="Cantidad" min="1" step="1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="price_input">Precio (€)</label>
                                            <input type="number" id="price_input" class="form-control" placeholder="Precio (€)" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="discount_input">Descuento (%)</label>
                                            <input type="number" id="discount_input" class="form-control" placeholder="Desc. %" value="0" min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <div class="form-group">
                                            <button type="button" id="add_product_button" class="btn btn-success btn-block"><i class="fas fa-plus"></i> Añadir</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table id="sale_details_table" class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-end">Precio (€)</th>
                                                <th class="text-center">Desc. (%)</th>
                                                <th class="text-end">Subtotal (€)</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Las filas se añadirán aquí con JavaScript --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                                <td id="table_subtotal" class="text-end">0.00 €</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Impuesto (<span id="tax_percentage_label">{{ old('tax', 18) }}</span>%):</strong></td>
                                                <td id="table_tax" class="text-end">0.00 €</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                                <td id="table_total" class="text-end"><strong>0.00 €</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    {{-- Inputs ocultos para enviar los totales calculados si es necesario --}}
                                    {{-- <input type="hidden" name="total" id="hidden_total" value="0"> --}}
                                </div>

                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Venta</button>
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('styles')
    {{-- Estilos para Select2 si los usas --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
    {{-- Scripts para Select2 --}}
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2({
                 theme: 'bootstrap4'
            });

            // Actualizar el precio en el input cuando se selecciona un producto
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
    <script>
        $(document).ready(function() {
            let detailIndex = 0; 

            $('#add_product_button').on('click', function() {
                const selectedOption = $('#product_id_select').find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.text().split(' (Stock:')[0]; 
                let price = parseFloat($('#price_input').val()); // Tomar el precio del input
                const stock = parseInt(selectedOption.data('stock') || 0);
                const quantity = parseInt($('#quantity_input').val() || 0);
                const discount = parseFloat($('#discount_input').val() || 0);

                if (!productId) { alert('Seleccione un producto.'); return; }
                if (isNaN(quantity) || quantity <= 0) { alert('Ingrese una cantidad válida.'); return; }
                if (isNaN(price) || price <= 0) {
                    // Si el precio no es válido en el input, intentar tomar el data-price
                    const dataPrice = parseFloat(selectedOption.data('price'));
                    if (isNaN(dataPrice) || dataPrice <= 0) {
                        alert('Ingrese un precio válido para el producto.'); return;
                    }
                    price = dataPrice;
                    $('#price_input').val(price.toFixed(2)); // Actualizar el input con el precio del data-attribute
                }
                if (isNaN(discount) || discount < 0 || discount > 100) { alert('El descuento debe estar entre 0 y 100.'); return; }
                
                // Validar stock
                let currentQuantityInTable = 0;
                $(`#sale_details_table tbody tr[data-id="${productId}"] input[name="quantity[]"]`).each(function() {
                    currentQuantityInTable += parseInt($(this).val());
                });

                if (quantity + currentQuantityInTable > stock) {
                    alert(`No hay suficiente stock para "${productName}". Stock disponible: ${stock - currentQuantityInTable}.`);
                    return;
                }
                
                // Verificar si el producto ya está en la tabla para actualizarlo en lugar de añadirlo
                const existingRow = $(`#sale_details_table tbody tr[data-id="${productId}"]`);
                if (existingRow.length > 0) {
                    // Actualizar cantidad, precio y descuento del producto existente
                    const existingQuantity = parseInt(existingRow.find('input[name="quantity[]"]').val());
                    const newQuantity = existingQuantity + quantity;

                    // Actualizar inputs ocultos y texto visible
                    existingRow.find('input[name="quantity[]"]').val(newQuantity);
                    existingRow.find('td:nth-child(2)').text(newQuantity); // Actualiza la cantidad visible

                    existingRow.find('input[name="price[]"]').val(price.toFixed(2));
                    existingRow.find('td:nth-child(3)').text(price.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    existingRow.find('input[name="discount[]"]').val(discount.toFixed(2));
                    existingRow.find('td:nth-child(4)').text(discount.toFixed(2) + '%');
                    
                    const newSubtotal = (newQuantity * price) * (1 - discount / 100);
                    existingRow.find('.row-subtotal').text(newSubtotal.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                } else {
                    // Añadir nueva fila si el producto no existe
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
                                ${discount.toFixed(2)}%
                            </td>
                            <td class="row-subtotal text-end">${subtotal.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-product-button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#sale_details_table tbody').append(newRowHTML);
                    detailIndex++;
                }


                $('#product_id_select').val('').trigger('change');
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
                    // Extraer el valor numérico del subtotal de la fila
                    const subtotalText = $(this).find('.row-subtotal').text().replace('€', '').trim();
                    // Convertir de formato europeo (1.234,56) a número estándar (1234.56)
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
            
            // Para inicializar la etiqueta del impuesto al cargar la página
            const initialTax = parseFloat($('#tax').val());
            if (!isNaN(initialTax) && initialTax >= 0) {
                $('#tax_percentage_label').text(initialTax);
            } else {
                 $('#tax_percentage_label').text('18'); // O el valor por defecto que tengas
            }
             calculateTotals(); 
        });
    </script>
@endpush

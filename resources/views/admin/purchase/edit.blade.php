@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Editar Compra')

@section('page_header', 'Editar Compra #'.$purchase->id)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar Compra</li>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Formulario de Edición de Compra</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                            <label for="provider_id">Proveedor</label>
                                            <select name="provider_id" id="provider_id" class="form-control select2 @error('provider_id') is-invalid @enderror" style="width: 100%;" required>
                                                <option value="">Seleccione un Proveedor</option>
                                                @foreach ($providers as $provider)
                                                    <option value="{{ $provider->id }}" {{ old('provider_id', $purchase->provider_id) == $provider->id ? 'selected' : '' }}>
                                                        {{ $provider->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('provider_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tax">Impuesto (%)</label>
                                            <input type="number" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror" placeholder="Ej: 18" value="{{ old('tax', $purchase->tax) }}" min="0" step="0.01" required>
                                             @error('tax')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4 class="mb-3">Detalles de la Compra</h4>

                                <div class="row">
                                    <div class="col-md-5"> {{-- Ajustado para dar más espacio al producto --}}
                                        <div class="form-group">
                                            <label for="product_id_select">Producto</label>
                                            <select id="product_id_select" class="form-control select2" style="width: 100%;">
                                                <option value="">Seleccione un Producto</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                                        {{ $product->name }} (Stock actual: {{ $product->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="quantity_input">Cantidad</label>
                                            <input type="number" id="quantity_input" class="form-control" placeholder="Cantidad" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3"> {{-- Ajustado para el precio --}}
                                        <div class="form-group">
                                            <label for="price_input">Precio de Compra (S/)</label>
                                            <input type="number" id="price_input" class="form-control" placeholder="Precio" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <div class="form-group">
                                            <button type="button" id="add_product_button" class="btn btn-success btn-block"><i class="fas fa-plus"></i> Añadir</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table id="purchase_details_table" class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Compra (S/)</th>
                                                <th>Subtotal (S/)</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Las filas se añadirán aquí con JavaScript, incluyendo las existentes --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Subtotal General:</strong></td>
                                                <td id="table_subtotal_general">S/ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Impuesto (<span id="tax_percentage_label">{{ $purchase->tax }}</span>%):</strong></td>
                                                <td id="table_tax_amount">S/ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>TOTAL COMPRA:</strong></td>
                                                <td id="table_total_purchase"><strong>S/ 0.00</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar Compra</button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
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
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
    $(function () {
        // Initialize Select2 Elements
        $('.select2').select2({
             theme: 'bootstrap4'
        });

        let detailIndex = 0; // Para los nombres de los inputs: details[index][field]

        // Función para añadir producto a la tabla
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
                        <input type="hidden" name="details[${detailIndex}][price]" value="${price.toFixed(2)}">
                        ${price.toFixed(2)}
                    </td>
                    <td class="row-subtotal">${subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product-button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#purchase_details_table tbody').append(newRow);
            detailIndex++;
        }

        // Cargar detalles existentes de la compra
        const existingDetails = @json($purchase->purchaseDetails);
        existingDetails.forEach(function(detail) {
            addProductToTable(detail.product_id, detail.product.name, detail.quantity, parseFloat(detail.price));
        });
        calculateTotals(); // Calcular totales después de cargar los detalles existentes


        $('#add_product_button').on('click', function() {
            const selectedOption = $('#product_id_select').find('option:selected');
            const productId = selectedOption.val();
            const productName = selectedOption.text().split(' (Stock actual:')[0];
            const price = parseFloat($('#price_input').val() || 0);
            const quantity = parseInt($('#quantity_input').val() || 0);

            if (!productId) { alert('Seleccione un producto.'); return; }
            if (quantity <= 0) { alert('Ingrese una cantidad válida.'); return; }
            if (price < 0) { alert('Ingrese un precio de compra válido.'); return; } // Precio puede ser 0

      

            addProductToTable(productId, productName, quantity, price);

            $('#product_id_select').val('').trigger('change');
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

            $('#table_subtotal_general').text('S/ ' + subtotalGeneral.toFixed(2));
            $('#table_tax_amount').text('S/ ' + taxAmount.toFixed(2));
            $('#table_total_purchase').html('<strong>S/ ' + total.toFixed(2) + '</strong>');
        }
    });
    </script>
@endpush
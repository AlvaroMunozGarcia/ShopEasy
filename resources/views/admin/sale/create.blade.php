@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Registrar Venta')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registrar Venta</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
                        <li class="breadcrumb-item active">Registrar Venta</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

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
                                            <input type="number" id="quantity_input" class="form-control" placeholder="Cantidad" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="price_input">Precio (S/)</label>
                                            <input type="number" id="price_input" class="form-control" placeholder="Precio" min="0" step="0.01">
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
                                                <th>Cantidad</th>
                                                <th>Precio (S/)</th>
                                                <th>Desc. (%)</th>
                                                <th>Subtotal (S/)</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Las filas se añadirán aquí con JavaScript --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                                <td id="table_subtotal">S/ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Impuesto (<span id="tax_percentage_label">18</span>%):</strong></td>
                                                <td id="table_tax">S/ 0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                                <td id="table_total"><strong>S/ 0.00</strong></td>
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
        });
    </script>
    {{-- TU JAVASCRIPT AQUÍ para: --}}
    {{-- 1. Capturar datos del producto seleccionado (precio, stock). --}}
    {{-- 2. Validar cantidad (no exceder stock). --}}
    {{-- 3. Botón "Añadir": Crear inputs hidden (details[index][product_id], etc.) y añadir fila a la tabla. --}}
    {{-- 4. Botón "Quitar" en cada fila: Eliminar fila y inputs hidden correspondientes. --}}
    {{-- 5. Calcular subtotal de fila (precio * cantidad * (1 - descuento/100)). --}}
    {{-- 6. Recalcular totales (Subtotal general, Impuesto, Total) cada vez que se añade/quita/modifica una fila o cambia el input de impuesto. --}}
    {{-- 7. Actualizar los valores en el tfoot de la tabla. --}}
    {{-- 8. (Opcional) Actualizar el input hidden 'total' si tu controlador lo necesita. --}}
    <script>
        // Ejemplo básico de inicialización (necesitas implementar la lógica completa)
        $(document).ready(function() {
            let detailIndex = 0; // Contador para los índices del array 'details'

            $('#add_product_button').on('click', function() {
                // --- Obtener datos del producto seleccionado ---
                const selectedOption = $('#product_id_select').find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.text().split(' (Stock:')[0]; // Extraer nombre
                const price = parseFloat($('#price_input').val() || selectedOption.data('price') || 0);
                const stock = parseInt(selectedOption.data('stock') || 0);
                const quantity = parseInt($('#quantity_input').val() || 0);
                const discount = parseFloat($('#discount_input').val() || 0);

                // --- Validaciones básicas ---
                if (!productId) { alert('Seleccione un producto.'); return; }
                if (quantity <= 0) { alert('Ingrese una cantidad válida.'); return; }
                if (price <= 0) { alert('Ingrese un precio válido.'); return; }
                if (discount < 0 || discount > 100) { alert('El descuento debe estar entre 0 y 100.'); return; }
                // Aquí deberías validar si el producto ya está en la tabla
                // Aquí deberías validar el stock disponible

                // --- Calcular subtotal de la línea ---
                const subtotal = (quantity * price) * (1 - discount / 100);

                // --- Crear la fila HTML ---
                const newRow = `
                    <tr data-id="${productId}">
                        <td>
                            <input type="hidden" name="product_id[]" value="${productId}">
                            ${productName}
                        </td>
                        <td>
                            <input type="hidden" name="quantity[]" value="${quantity}">
                            ${quantity}
                        </td>
                        <td>
                            <input type="hidden" name="price[]" value="${price.toFixed(2)}">
                            ${price.toFixed(2)}
                        </td>
                        <td>
                            <input type="hidden" name="discount[]" value="${discount.toFixed(2)}">
                            ${discount.toFixed(2)}%
                        </td>
                        <td class="row-subtotal">${subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-product-button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                // --- Añadir la fila a la tabla ---
                $('#sale_details_table tbody').append(newRow);
                detailIndex++; // Incrementar índice para el próximo producto

                // --- Limpiar inputs ---
                $('#product_id_select').val('').trigger('change');
                $('#quantity_input').val('');
                $('#price_input').val('');
                $('#discount_input').val('0');

                // --- Recalcular totales ---
                calculateTotals();
            });

            // --- Evento para quitar producto ---
            $('#sale_details_table tbody').on('click', '.remove-product-button', function() {
                $(this).closest('tr').remove();
                calculateTotals(); // Recalcular al quitar
            });

             // --- Evento para recalcular si cambia el impuesto ---
            $('#tax').on('input', function() {
                 calculateTotals();
                 $('#tax_percentage_label').text($(this).val() || 0); // Actualizar etiqueta
            });

            // --- Función para calcular totales ---
            function calculateTotals() {
                let subtotalGeneral = 0;
                $('#sale_details_table tbody tr').each(function() {
                    subtotalGeneral += parseFloat($(this).find('.row-subtotal').text()) || 0;
                });

                const taxPercentage = parseFloat($('#tax').val() || 0);
                const taxAmount = subtotalGeneral * (taxPercentage / 100);
                const total = subtotalGeneral + taxAmount;

                $('#table_subtotal').text('S/ ' + subtotalGeneral.toFixed(2));
                $('#table_tax').text('S/ ' + taxAmount.toFixed(2));
                $('#table_total').html('<strong>S/ ' + total.toFixed(2) + '</strong>');
                // $('#hidden_total').val(total.toFixed(2)); // Actualizar input hidden si es necesario
            }

             // Calcular totales al cargar la página (por si hay datos old())
             // calculateTotals(); // Necesitarías reconstruir la tabla si hay datos old()
        });
    </script>
@endpush

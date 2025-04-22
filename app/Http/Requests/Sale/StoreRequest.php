<?php

namespace App\Http\Requests\Sale;

use App\Models\Product; // Necesario para validar stock
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Necesario para reglas más complejas si las usas

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Puedes añadir lógica de autorización si es necesario, por ahora true está bien
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Contamos cuántos productos se envían para la regla 'size'
        $productCount = count($this->input('product_id', []));

        return [
            'client_id' => 'required|integer|exists:clients,id', // Asegura que el cliente exista
            'tax' => 'required|numeric|min:0|max:100', // Impuesto requerido y numérico entre 0 y 100

            // Validación para los arrays de detalles
            'product_id' => 'required|array|min:1', // Debe existir, ser array y tener al menos 1 producto
            'product_id.*' => 'required|integer|exists:products,id', // Cada ID de producto debe existir en la tabla products

            'quantity' => ['required', 'array', "size:{$productCount}"], // Requerido, array y misma longitud que product_id
            'quantity.*' => [
                'required',
                'integer',
                'min:1',
                // Validación de stock para cada cantidad
                function ($attribute, $value, $fail) {
                    // $attribute será como 'quantity.0', 'quantity.1', etc.
                    // Extraemos el índice numérico
                    $index = explode('.', $attribute)[1];
                    // Obtenemos el product_id correspondiente a este índice
                    $productId = $this->input('product_id.' . $index);
                    // Buscamos el producto en la BD
                    $product = Product::find($productId);

                    // Si no se encontró el producto (aunque la regla exists ya debería cubrir esto)
                    if (!$product) {
                        $fail("El producto con ID {$productId} no existe (índice {$index}).");
                        return;
                    }
                    // Si no hay stock suficiente
                    if ($product->stock < $value) {
                        $fail("Stock insuficiente ({$product->stock}) para el producto '{$product->name}' (ID: {$productId}). Se solicitaron {$value}.");
                    }
                },
            ],

            'price' => ['required', 'array', "size:{$productCount}"], // Requerido, array y misma longitud
            'price.*' => 'required|numeric|min:0', // Cada precio debe ser numérico y no negativo

            'discount' => ['required', 'array', "size:{$productCount}"], // Requerido, array y misma longitud
            'discount.*' => 'required|numeric|min:0|max:100', // Cada descuento numérico entre 0 y 100
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no es válido.',
            'tax.required' => 'El campo impuesto es obligatorio.',
            'tax.numeric' => 'El impuesto debe ser un valor numérico.',
            'tax.min' => 'El impuesto no puede ser negativo.',

            'product_id.required' => 'Debe agregar al menos un producto a la venta.',
            'product_id.min' => 'Debe agregar al menos un producto a la venta.',
            'product_id.*.required' => 'Falta el ID de producto en uno de los detalles.',
            'product_id.*.exists' => 'Uno de los productos seleccionados no existe.',

            'quantity.required' => 'Falta el array de cantidades.',
            'quantity.size' => 'El número de cantidades no coincide con el número de productos.',
            'quantity.*.required' => 'Falta la cantidad en uno de los detalles.',
            'quantity.*.integer' => 'La cantidad debe ser un número entero.',
            'quantity.*.min' => 'La cantidad de cada producto debe ser al menos 1.',
            // Los mensajes de la validación de stock se definen dentro de la closure con $fail()

            'price.required' => 'Falta el array de precios.',
            'price.size' => 'El número de precios no coincide con el número de productos.',
            'price.*.required' => 'Falta el precio en uno de los detalles.',
            'price.*.numeric' => 'El precio debe ser un valor numérico.',
            'price.*.min' => 'El precio no puede ser negativo.',

            'discount.required' => 'Falta el array de descuentos.',
            'discount.size' => 'El número de descuentos no coincide con el número de productos.',
            'discount.*.required' => 'Falta el descuento en uno de los detalles (puede ser 0).',
            'discount.*.numeric' => 'El descuento debe ser un valor numérico.',
            'discount.*.min' => 'El descuento no puede ser negativo.',
            'discount.*.max' => 'El descuento no puede ser mayor a 100.',
        ];
    }
}

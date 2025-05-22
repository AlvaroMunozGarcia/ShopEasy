<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // O la lógica de autorización que necesites
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider_id' => 'required|exists:providers,id',
            'tax' => 'required|numeric|min:0|max:100', // Asumiendo que el impuesto es un porcentaje
            // 'purchase_date' => 'nullable|date', // Si permites establecerla manualmente
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'provider_id.required' => 'El proveedor es obligatorio.',
            'provider_id.exists' => 'El proveedor seleccionado no es válido.',
            'tax.required' => 'El impuesto es obligatorio.',
            'tax.numeric' => 'El impuesto debe ser un número.',
            'details.required' => 'Debe agregar al menos un producto a la compra.',
            'details.array' => 'Los detalles de la compra deben ser un listado.',
            'details.min' => 'Debe agregar al menos un producto a la compra.',
            'details.*.product_id.required' => 'Debe seleccionar un producto para cada detalle.',
            'details.*.product_id.exists' => 'El producto seleccionado en un detalle no es válido.',
            'details.*.quantity.required' => 'La cantidad es obligatoria para cada detalle.',
            'details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'details.*.quantity.min' => 'La cantidad debe ser al menos 1.',
            'details.*.price.required' => 'El precio es obligatorio para cada detalle.',
            'details.*.price.numeric' => 'El precio debe ser un número.',
            'details.*.price.min' => 'El precio debe ser al menos 0.',
        ];
    }
}
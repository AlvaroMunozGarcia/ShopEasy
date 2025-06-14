<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'string|required|unique:products,name,'.$this->route('product')->id.'|max:255',
            'code'=>'string|required|unique:products,code,'.$this->route('product')->id,
            'picture'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:max_width=200,max_height=400',
            'sell_price'=>'required',
            'category_id' => 'required|exists:categories,id',
            'provider_id'=>'integer|required|exists:providers,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:ACTIVE,INACTIVE',
            'min_stock' => 'nullable|integer|min:0', 
        ];
    }

    public function messages()
    {
        return[
            'name.string'=>'El calor no es correcto.',
            'name.required'=>'El campo es requerido.',
            'name.unique'=>'El producto ya está registrado.',
            'name.max'=>'Solo es permite 255 caracteres.',

            'code.string'=>'El valor para código no es correcto.',
            'code.required'=>'El campo código es requerido.',
            'code.unique'=>'El código ya está registrado.',

            'picture.image'=>'El archivo debe ser una imagen válida (jpeg, png, jpg, gif, svg).',
            'picture.mimes'=>'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg.',
            'picture.max'=>'La imagen no debe pesar más de 2MB.',
            'picture.dimensions'=>'Las dimensiones máximas de la imagen son 200px de ancho por 400px de alto.',

            'sell_price.required'=>'El campo es requerido.',

            'category_id.integer'=>'El calor tiene que ser entero.',
            'category_id.required'=>'El campo es requerido.',
            'category_id.exists'=>'La categoria no existe.',

            'provider_id.integer'=>'El valor tiene que ser entero.',
            'provider_id.required'=>'El campo es requerido.',
            'provider_id.exists'=>'El proveedor no existe.',

            'stock.required' => 'El campo stock es requerido.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser un valor negativo.',

            // Mensajes para el estado
            'status.required' => 'El campo estado es requerido.',
            'status.string' => 'El valor para estado no es correcto.',
            'status.in' => 'El estado debe ser ACTIVO o INACTIVO.',

            // Mensajes para min_stock (igual que en StoreRequest)
            'min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',

        ];        

    }
}

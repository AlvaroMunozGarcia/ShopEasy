<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name'=>'required|string|max:50|unique:categories,name,'.$this->category->id,
            'description'=>'nullable|string|max:50'
        ];
    }

    public function messages()
    {
        return[
            'name.required'=>'Este campo es requerido.',
            'name.string'=>'El valor no es correcto.',
            'name.max'=>'Solo se permite 50 caracteres.',
            'name.unique'=>'Este nombre de categoría ya se encuentra registrado.',
            'description.string'=>'El valor no es correcto.',
            'description.max'=>'Solo se permite 255 caracteres.',
        ];        

    }

}

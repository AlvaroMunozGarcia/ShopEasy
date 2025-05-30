<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name'=>'string|required|max:255',
            'dni'=>'string|required|unique:clients|max:9',
            'ruc'=>'string|required|unique:clients|max:11',
            'address'=>'string|required|max:255',
            'phone'=>'string|required|unique:clients|max:9',
            'email'=>'string|required|unique:clients|max:225|email:rfc,dns',
        ];
      
    }
    public function messages()
    {
        return[
            'name.required'=>'Este campo es requerido.',
            'name.string'=>'El valor no es correcto.',
            'name.max'=>'Solo se permite 225 caracteres.',

            'dni.string'=>'El valor no es correcto.',
            'dni.required'=>'Este campo es requerido.',
            'dni.unique'=>'Este DNI ya se encuentra registrado.',
            'dni.min'=>'Se requiere de 9 caracteres.',
            'dni.max'=>'Solo se permite 9 caracteres.',

            'ruc.string'=>'El vaor no es correcto.',
            'ruc.unique'=>'El numero de RUC ya se encuentra registrado.',
            'ruc.min'=>'Se requiere de 11 caracteres.',
            'ruc.max'=>'Solo se permite 11 caracteres.',

            'address.string'=>'El calor no es correcto.',
            'address.max'=>'Solo se permite 255 caracteres.',

            'phone.string'=>'El valor no es correcto.',
            'phone.unique'=>'El numero de celular ya se encuentra registrado.',
            'phone.min'=>'Se requiere de 9 caracteres.',
            'phone.max'=>'Solo se permite 9 caracteres.',

            'email.string'=>'El valor no es correcto.',
            'email.unique'=>'La direccion de correo electronico ya se encuenta registrada.',
            'email.max'=>'Solo se permite 255 caracteres.',
            'email.email'=>'No es un correo electronico'
        ];        

    }
}

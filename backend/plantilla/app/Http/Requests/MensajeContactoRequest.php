<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MensajeContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'   => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'mensaje'  => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'  => 'Indica tu nombre.',
            'email.required'   => 'Indica tu email.',
            'email.email'      => 'El email no tiene un formato valido.',
            'mensaje.required' => 'Escribe tu mensaje.',
            'mensaje.min'      => 'El mensaje debe tener al menos 10 caracteres.',
        ];
    }
}

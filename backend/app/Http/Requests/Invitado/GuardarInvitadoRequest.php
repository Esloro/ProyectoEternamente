<?php

namespace App\Http\Requests\Invitado;

use Illuminate\Foundation\Http\FormRequest;

class GuardarInvitadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'max:150'],
            'alergias'         => ['nullable', 'string', 'max:500'],
            'num_acompanantes' => ['nullable', 'integer', 'min:0', 'max:20'],
            'mesa_id'          => ['nullable', 'integer', 'exists:mesas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'num_acompanantes.min' => 'El número de acompañantes no puede ser negativo.',
            'num_acompanantes.max' => 'No se pueden añadir más de 20 acompañantes.',
        ];
    }
}

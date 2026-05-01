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
            'nombre'        => ['required', 'string', 'max:150'],
            'alergias'      => ['nullable', 'string', 'max:500'],
            'acompanante'   => ['boolean'],
            'mesa_id'       => ['nullable', 'integer', 'exists:mesas,id'],
        ];
    }
}

<?php

namespace App\Http\Requests\Mensaje;

use Illuminate\Foundation\Http\FormRequest;

class EnviarMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'contenido' => ['required', 'string', 'max:2000'],
        ];
    }
}

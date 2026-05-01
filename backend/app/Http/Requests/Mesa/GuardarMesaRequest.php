<?php

namespace App\Http\Requests\Mesa;

use Illuminate\Foundation\Http\FormRequest;

class GuardarMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'numero'    => ['required', 'integer', 'min:1', 'max:100'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}

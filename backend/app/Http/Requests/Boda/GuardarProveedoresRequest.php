<?php

namespace App\Http\Requests\Boda;

use Illuminate\Foundation\Http\FormRequest;

class GuardarProveedoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'proveedores'              => ['required', 'array'],
            'proveedores.*.id'         => ['required', 'integer', 'exists:proveedores,id'],
            'proveedores.*.notas'      => ['nullable', 'string', 'max:500'],
        ];
    }
}

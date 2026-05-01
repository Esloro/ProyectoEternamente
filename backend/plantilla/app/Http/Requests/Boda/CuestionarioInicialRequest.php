<?php

namespace App\Http\Requests\Boda;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CuestionarioInicialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tipo_ceremonia'           => ['required', Rule::in(['civil', 'iglesia', 'aire_libre', 'otra'])],
            'iglesia'                  => ['nullable', 'required_if:tipo_ceremonia,iglesia', 'string', 'max:200'],
            'fecha_boda'               => ['required', 'date', 'after:today'],
            'num_invitados'            => ['required', 'integer', 'min:1', 'max:1000'],
            'franja_horaria'           => ['required', Rule::in(['manana', 'tarde', 'noche'])],
            'tematica'                 => ['required', Rule::in(['clasica', 'rustica', 'moderna', 'boho', 'glamour'])],
            'tipo_comida'              => ['required', Rule::in(['coctel', 'banquete', 'buffet', 'familiar'])],
            'presupuesto_orientativo'  => ['required', Rule::in(['hasta_10000', '10000_20000', '20000_35000', '35000_50000', 'mas_50000'])],
        ];
    }

    public function messages(): array
    {
        return [
            'iglesia.required_if'  => 'Si la ceremonia es en iglesia, debes indicar cual.',
            'fecha_boda.after'     => 'La fecha de la boda debe ser posterior a hoy.',
            'num_invitados.min'    => 'Debe haber al menos 1 invitado.',
        ];
    }
}

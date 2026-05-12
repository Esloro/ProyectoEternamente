<?php

namespace App\Http\Requests\Boda;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validacion al editar los detalles de la boda desde el panel del cliente.
 * Mismas reglas que el cuestionario inicial.
 */
class ActualizarDetallesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nombre_pareja'            => ['required', 'string', 'max:150'],
            'tipo_ceremonia'           => ['required', Rule::in(['religiosa', 'civil_ayuntamiento', 'simbolica', 'renovacion_votos'])],
            'lugar_celebracion'        => ['required', Rule::in(['iglesia', 'ayuntamiento', 'finca', 'playa', 'jardin', 'restaurante', 'otro'])],
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
            'nombre_pareja.required' => 'Indica los nombres de la pareja.',
            'fecha_boda.after'       => 'La fecha de la boda debe ser posterior a hoy.',
            'num_invitados.min'      => 'Debe haber al menos 1 invitado.',
        ];
    }
}

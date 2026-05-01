<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'                => ['required', 'string', 'max:100'],
            'apellidos'             => ['required', 'string', 'max:150'],
            'email'                 => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email'],
            'telefono'              => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'           => 'Indica tu nombre.',
            'apellidos.required'        => 'Indica tus apellidos.',
            'email.required'            => 'El email es obligatorio.',
            'email.email'               => 'El email no tiene un formato valido.',
            'email.unique'              => 'Ya existe una cuenta con este email.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed'            => 'La contraseña debe combinar mayusculas y minusculas.',
            'password.numbers'          => 'La contraseña debe contener al menos un numero.',
        ];
    }
}

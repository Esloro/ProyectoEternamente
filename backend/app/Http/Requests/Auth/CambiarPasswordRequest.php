<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CambiarPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'password_actual' => ['required', 'string'],
            'password'        => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }
}

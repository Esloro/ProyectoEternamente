<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CambiarPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Actualizar nombre, apellidos, email y telefono del usuario logueado.
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre'    => ['sometimes', 'required', 'string', 'max:100'],
            'apellidos' => ['sometimes', 'required', 'string', 'max:150'],
            'email'     => ['sometimes', 'required', 'string', 'email', 'max:150', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'telefono'  => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        // Si cambia el email, invalidamos la verificacion.
        if (isset($datos['email']) && $datos['email'] !== $usuario->email) {
            $datos['email_verificado_en'] = null;
        }

        $usuario->update($datos);

        return $this->ok($usuario->fresh(), 'Perfil actualizado.');
    }

    public function cambiarPassword(CambiarPasswordRequest $request)
    {
        $usuario = $request->user();

        if (! Hash::check($request->password_actual, $usuario->password)) {
            return $this->ko('La contraseña actual no es correcta.', 422);
        }

        $usuario->update(['password' => $request->password]);
        $usuario->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return $this->ok(null, 'Contraseña actualizada.');
    }
}

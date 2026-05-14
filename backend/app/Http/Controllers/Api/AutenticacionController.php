<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Http\Requests\Auth\RegistroRequest;
use App\Http\Requests\Auth\ResetearPasswordRequest;
use App\Models\Boda;
use App\Models\Usuario;
use App\Notifications\EliminarCuentaNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AutenticacionController extends Controller
{
    /**
     * Registro publico de un nuevo cliente. Tras crearlo, dispara el
     * evento Registered que envia el email de verificacion.
     */
    public function registro(RegistroRequest $request)
    {
        $usuario = Usuario::create([
            'nombre'    => $request->nombre,
            'apellidos' => $request->apellidos,
            'email'     => $request->email,
            'telefono'  => $request->telefono,
            'password'  => $request->password, // se hashea por cast
            'rol'       => 'cliente',
        ]);

        event(new Registered($usuario));

        $token = $usuario->createToken('auth-token')->plainTextToken;

        return $this->ok(
            ['usuario' => $usuario->fresh(), 'token' => $token],
            'Registro completado. Revisa tu correo para confirmar la cuenta.',
            201
        );
    }

    /**
     * Login con email + password. Devuelve token Sanctum.
     */
    public function login(LoginRequest $request)
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return $this->ko('Credenciales incorrectas.', 401);
        }

        $token = $usuario->createToken('auth-token')->plainTextToken;

        return $this->ok(
            ['usuario' => $usuario, 'token' => $token],
            'Login correcto.'
        );
    }

    /**
     * Devuelve los datos del usuario autenticado (incluye su boda
     * principal si la tiene, util para el guard del frontend).
     */
    public function yo(Request $request)
    {
        $usuario = $request->user();
        $boda = $usuario->bodas()->latest()->first();

        return $this->ok([
            'usuario' => $usuario,
            'boda'    => $boda,
        ]);
    }

    /**
     * Cierra la sesion actual revocando el token usado.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Sesion cerrada.');
    }

    /**
     * Reenvia el email de verificacion al usuario autenticado.
     */
    public function reenviarVerificacion(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->hasVerifiedEmail()) {
            return $this->ko('Tu email ya esta verificado.', 400);
        }

        $usuario->sendEmailVerificationNotification();

        return $this->ok(null, 'Te hemos enviado un nuevo email de verificacion.');
    }

    /**
     * Endpoint al que apunta el enlace del email de verificacion (URL
     * firmada). Marca el email como verificado y redirige al frontend.
     */
    public function verificarEmail(Request $request, int $id, string $hash)
    {
        $usuario = Usuario::findOrFail($id);

        if (! hash_equals(sha1($usuario->getEmailForVerification()), $hash)) {
            return $this->ko('El enlace de verificacion no es valido.', 400);
        }

        if (! $usuario->hasVerifiedEmail()) {
            $usuario->markEmailAsVerified();
            event(new Verified($usuario));
        }

        // Redirigimos al frontend con un flag para que muestre el mensaje.
        return redirect(config('app.frontend_url') . '/email-verificado?ok=1');
    }

    /**
     * Solicita un email de recuperacion de contraseña.
     */
    public function solicitarRecuperacion(RecuperarPasswordRequest $request)
    {
        // Por seguridad respondemos siempre OK aunque el email no exista,
        // para no filtrar que cuentas estan registradas.
        Password::sendResetLink($request->only('email'));

        return $this->ok(null, 'Si el email existe, recibiras un mensaje con instrucciones.');
    }

    /**
     * Resetea la contraseña a partir del token recibido por email.
     */
    public function resetearPassword(ResetearPasswordRequest $request)
    {
        $estado = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Usuario $usuario, string $password) {
                $usuario->forceFill(['password' => $password])->save();
                $usuario->tokens()->delete(); // invalida tokens existentes
            }
        );

        return $estado === Password::PASSWORD_RESET
            ? $this->ok(null, 'Contraseña actualizada correctamente.')
            : $this->ko('No se ha podido restablecer la contraseña. El enlace puede haber caducado.', 400);
    }

    /**
     * El cliente solicita la eliminacion de su cuenta. Solo permitido si
     * no tiene ninguna boda en estado activa o finalizada (en esos casos
     * debe contactar con el administrador). Si pasa la validacion, se le
     * envia un email con un enlace firmado para confirmar.
     */
    public function solicitarEliminacionCuenta(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->esAdministrador()) {
            return $this->ko('Los administradores no pueden auto-eliminar su cuenta.', 403);
        }

        $estadosBloqueantes = [Boda::ESTADO_ACTIVA, Boda::ESTADO_FINALIZADA];
        $tieneBodaBloqueante = $usuario->bodas()
            ->whereIn('estado', $estadosBloqueantes)
            ->exists();

        if ($tieneBodaBloqueante) {
            return $this->ko(
                'No puedes eliminar tu cuenta porque tienes una boda activa o finalizada. Contacta con el administrador para que gestione la eliminación.',
                403
            );
        }

        $usuario->notify(new EliminarCuentaNotification);

        return $this->ok(null, 'Te hemos enviado un email para confirmar la eliminación. Revisa tu bandeja de entrada.');
    }

    /**
     * Endpoint al que apunta el enlace firmado del email de confirmacion.
     * Anonimiza los datos personales del usuario y aplica soft delete.
     * Despues redirige al frontend a la pagina /cuenta-eliminada.
     */
    public function confirmarEliminacionCuenta(Request $request, int $id)
    {
        $usuario = Usuario::find($id);

        // Si ya fue eliminado, redirigimos igualmente al frontend con un
        // flag para que muestre el mensaje (idempotente: el enlace puede
        // haberse abierto dos veces).
        if (! $usuario) {
            return redirect(config('app.frontend_url') . '/cuenta-eliminada?ok=1');
        }

        // Volvemos a validar la condicion por si el estado cambio entre
        // la solicitud y la confirmacion (boda paso a activa, etc.)
        $estadosBloqueantes = [Boda::ESTADO_ACTIVA, Boda::ESTADO_FINALIZADA];
        $tieneBodaBloqueante = $usuario->bodas()
            ->whereIn('estado', $estadosBloqueantes)
            ->exists();

        if ($tieneBodaBloqueante) {
            return redirect(config('app.frontend_url') . '/cuenta-eliminada?error=boda_bloqueante');
        }

        $usuario->anonimizarYEliminar();

        return redirect(config('app.frontend_url') . '/cuenta-eliminada?ok=1');
    }
}

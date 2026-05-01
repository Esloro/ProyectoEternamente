<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MensajeContactoRequest;
use App\Mail\MensajeContactoRecibido;
use App\Models\MensajeContacto;
use App\Models\Usuario;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MensajeContactoController extends Controller
{
    /**
     * Recoge el formulario de contacto de la landing. Lo guarda en BD
     * y envia un email al administrador. El envio de email no debe
     * romper la peticion: si Mailhog esta caido lo logueamos y devolvemos
     * exito al usuario porque el mensaje SI esta guardado en BD.
     */
    public function store(MensajeContactoRequest $request)
    {
        $mensaje = MensajeContacto::create($request->validated());

        try {
            $admin = Usuario::where('rol', 'administrador')->first();
            if ($admin) {
                Mail::to($admin->email)->send(new MensajeContactoRecibido($mensaje));
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el email de contacto: ' . $e->getMessage());
        }

        return $this->ok(
            ['mensaje' => $mensaje],
            'Mensaje recibido. Te contestaremos lo antes posible.',
            201
        );
    }
}

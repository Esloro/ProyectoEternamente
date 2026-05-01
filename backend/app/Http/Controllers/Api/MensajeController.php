<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mensaje\EnviarMensajeRequest;
use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    /**
     * Devuelve la conversacion entre el cliente logueado y la wedding
     * planner para su boda. El frontend hace polling cada 5-10s.
     */
    public function conversacion(Request $request)
    {
        $boda = $request->attributes->get('boda');

        $mensajes = $boda->mensajes()
            ->orderBy('created_at')
            ->get();

        return $this->ok([
            'mensajes' => $mensajes,
            'no_leidos' => $mensajes->where('leido', false)->where('receptor_id', $request->user()->id)->count(),
        ]);
    }

    /**
     * Cliente envia un mensaje. El receptor es siempre el (primer) admin.
     */
    public function enviar(EnviarMensajeRequest $request)
    {
        $boda = $request->attributes->get('boda');
        $admin = Usuario::where('rol', 'administrador')->first();

        if (! $admin) {
            return $this->ko('No hay un administrador disponible para recibir el mensaje.', 503);
        }

        $mensaje = Mensaje::create([
            'boda_id'      => $boda->id,
            'emisor_id'    => $request->user()->id,
            'receptor_id'  => $admin->id,
            'contenido'    => $request->contenido,
            'leido'        => false,
        ]);

        return $this->ok(['mensaje' => $mensaje], 'Mensaje enviado.', 201);
    }

    /**
     * Marca como leidos todos los mensajes recibidos por el usuario
     * logueado en su conversacion.
     */
    public function marcarLeidos(Request $request)
    {
        $boda = $request->attributes->get('boda');

        $afectados = $boda->mensajes()
            ->where('receptor_id', $request->user()->id)
            ->where('leido', false)
            ->update(['leido' => true]);

        return $this->ok(['marcados_leidos' => $afectados]);
    }
}

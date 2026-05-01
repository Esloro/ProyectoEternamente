<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mensaje\EnviarMensajeRequest;
use App\Models\Boda;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Listado de conversaciones (una por boda activa) con el ultimo
     * mensaje y el contador de no leidos para el admin.
     */
    public function conversaciones(Request $request)
    {
        $admin = $request->user();

        $bodas = Boda::query()
            ->with(['usuario:id,nombre,apellidos,email'])
            ->whereHas('mensajes')
            ->orderByDesc(
                Mensaje::select('created_at')
                    ->whereColumn('boda_id', 'bodas.id')
                    ->latest()
                    ->take(1)
            )
            ->get();

        $resultado = $bodas->map(function (Boda $boda) use ($admin) {
            $ultimo = $boda->mensajes()->latest()->first();
            $noLeidos = $boda->mensajes()
                ->where('receptor_id', $admin->id)
                ->where('leido', false)
                ->count();

            return [
                'boda'         => $boda->only(['id', 'estado', 'fecha_boda']),
                'cliente'      => $boda->usuario,
                'ultimo'       => $ultimo,
                'no_leidos'    => $noLeidos,
            ];
        });

        return $this->ok(['conversaciones' => $resultado]);
    }

    public function conversacion(Boda $boda)
    {
        $boda->load(['usuario:id,nombre,apellidos,email']);

        return $this->ok([
            'boda'     => $boda,
            'mensajes' => $boda->mensajes()->orderBy('created_at')->get(),
        ]);
    }

    public function enviar(EnviarMensajeRequest $request, Boda $boda)
    {
        $admin = $request->user();

        $mensaje = Mensaje::create([
            'boda_id'      => $boda->id,
            'emisor_id'    => $admin->id,
            'receptor_id'  => $boda->usuario_id,
            'contenido'    => $request->contenido,
            'leido'        => false,
        ]);

        return $this->ok(['mensaje' => $mensaje], 'Mensaje enviado.', 201);
    }

    public function marcarLeidos(Request $request, Boda $boda)
    {
        $admin = $request->user();

        $afectados = $boda->mensajes()
            ->where('receptor_id', $admin->id)
            ->where('leido', false)
            ->update(['leido' => true]);

        return $this->ok(['marcados_leidos' => $afectados]);
    }
}

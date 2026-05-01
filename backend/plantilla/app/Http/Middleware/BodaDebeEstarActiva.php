<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el acceso a las funciones del panel completo (proveedores,
 * mesas, chat...) cuando la boda del cliente todavia no ha sido
 * activada por el administrador. Si la boda esta en pendiente_reunion
 * el cliente solo puede ver el mensaje de "estamos en contacto".
 */
class BodaDebeEstarActiva
{
    public function handle(Request $request, Closure $siguiente): Response
    {
        $usuario = $request->user();
        $boda = $usuario?->bodas()->latest()->first();

        if (! $boda) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Aun no has rellenado el cuestionario inicial.',
            ], 403);
        }

        if (! $boda->estaActiva()) {
            return response()->json([
                'success' => false,
                'data' => ['estado' => $boda->estado],
                'message' => 'Tu boda aun no esta activa. Estamos en contacto contigo para concertar la reunion inicial.',
            ], 403);
        }

        // Inyectamos la boda en el request para que los controladores
        // posteriores la recuperen sin volver a consultar la BD.
        $request->attributes->set('boda', $boda);

        return $siguiente($request);
    }
}

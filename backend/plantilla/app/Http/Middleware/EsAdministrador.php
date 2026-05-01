<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el acceso a rutas administrativas si el usuario autenticado
 * no tiene rol "administrador".
 */
class EsAdministrador
{
    public function handle(Request $request, Closure $siguiente): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->esAdministrador()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'No tienes permiso para acceder a esta seccion.',
            ], 403);
        }

        return $siguiente($request);
    }
}

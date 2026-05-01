<?php

namespace App\Http\Controllers;

/**
 * Controlador base con helpers de respuesta JSON consistentes
 * { success, data, message }, tal y como pide el contrato de la API.
 */
abstract class Controller
{
    protected function ok(mixed $data = null, string $mensaje = '', int $codigo = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $mensaje,
        ], $codigo);
    }

    protected function ko(string $mensaje, int $codigo = 400, mixed $errores = null)
    {
        return response()->json([
            'success' => false,
            'data' => $errores,
            'message' => $mensaje,
        ], $codigo);
    }
}

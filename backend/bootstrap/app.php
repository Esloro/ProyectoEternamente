<?php

use App\Http\Middleware\BodaDebeEstarActiva;
use App\Http\Middleware\EsAdministrador;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias para usar en routes/api.php
        $middleware->alias([
            'es.administrador' => EsAdministrador::class,
            'boda.activa'      => BodaDebeEstarActiva::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Toda la API responde JSON consistente { success, data, message }.

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'data' => $e->errors(),
                    'message' => 'Datos invalidos. Revisa los campos marcados.',
                ], 422);
            }
        });

        $exceptions->render(function (HttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => $e->getMessage() ?: 'Error en la peticion.',
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'data' => config('app.debug') ? ['exception' => $e->getMessage()] : null,
                    'message' => 'Error interno del servidor.',
                ], 500);
            }
        });
    })
    ->create();

<?php

use App\Http\Controllers\Api\Admin\BodaController as AdminBodaController;
use App\Http\Controllers\Api\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\MensajeContactoController as AdminMensajeContactoController;
use App\Http\Controllers\Api\Admin\PaqueteController as AdminPaqueteController;
use App\Http\Controllers\Api\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Api\Admin\ProveedorController as AdminProveedorController;
use App\Http\Controllers\Api\Admin\TestimonioController as AdminTestimonioController;
use App\Http\Controllers\Api\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Api\AutenticacionController;
use App\Http\Controllers\Api\BodaController;
use App\Http\Controllers\Api\InvitadoController;
use App\Http\Controllers\Api\MensajeContactoController;
use App\Http\Controllers\Api\MensajeController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\PaqueteController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\TestimonioController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de la API REST
|--------------------------------------------------------------------------
| Todas las rutas tienen el prefijo /api/ (configurado en bootstrap/app.php)
| Estructura:
|   - publicas         (sin auth)
|   - autenticacion    (registro, login, recuperacion...)
|   - cliente          (auth:sanctum + email verificado)
|   - cliente.activo   (auth:sanctum + email verificado + boda activa)
|   - admin            (auth:sanctum + rol administrador)
*/

// =====================================================================
// Rutas publicas (landing)
// =====================================================================

Route::get('/paquetes',     [PaqueteController::class, 'index']);
Route::get('/portfolio',    [PortfolioController::class, 'index']);
Route::get('/testimonios',  [TestimonioController::class, 'index']);
Route::post('/contacto',    [MensajeContactoController::class, 'store']);


// =====================================================================
// Autenticacion
// =====================================================================

Route::prefix('auth')->group(function () {
    // Publicas
    Route::post('/registro',                [AutenticacionController::class, 'registro']);
    Route::post('/login',                   [AutenticacionController::class, 'login']);
    Route::post('/recuperar-password',      [AutenticacionController::class, 'solicitarRecuperacion']);
    Route::post('/reset-password',          [AutenticacionController::class, 'resetearPassword']);

    // Verificacion de email (URL firmada que llega por correo).
    // El nombre de la ruta DEBE ser exactamente "verification.verify"
    // porque la notificacion VerifyEmail de Laravel lo busca asi.
    Route::get('/verificar-email/{id}/{hash}', [AutenticacionController::class, 'verificarEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');

    // Confirmacion de eliminacion de cuenta (URL firmada que llega por correo).
    // Es publica: el usuario podria estar ya deslogueado al abrir el email.
    Route::get('/confirmar-eliminar-cuenta/{id}', [AutenticacionController::class, 'confirmarEliminacionCuenta'])
        ->middleware(['signed'])
        ->name('cuenta.eliminar');

    // Requieren autenticacion
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/yo',                       [AutenticacionController::class, 'yo']);
        Route::post('/logout',                  [AutenticacionController::class, 'logout']);
        Route::post('/reenviar-verificacion',   [AutenticacionController::class, 'reenviarVerificacion']);
        Route::post('/solicitar-eliminar-cuenta',[AutenticacionController::class, 'solicitarEliminacionCuenta']);
    });
});


// =====================================================================
// Cliente autenticado (con email verificado)
// =====================================================================

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Mi perfil
    Route::put('/mi-perfil',            [UsuarioController::class, 'actualizarPerfil']);
    Route::put('/mi-perfil/password',   [UsuarioController::class, 'cambiarPassword']);

    // Mi boda (vista basica accesible aunque la boda este pendiente)
    Route::get('/mi-boda',                  [BodaController::class, 'miBoda']);
    Route::post('/mi-boda/cuestionario',    [BodaController::class, 'guardarCuestionario']);
    Route::put('/mi-boda/detalles',         [BodaController::class, 'actualizarDetalles']);


    // -----------------------------------------------------------------
    // Acciones que requieren que la boda este ACTIVA
    // -----------------------------------------------------------------
    Route::middleware('boda.activa')->group(function () {

        // Eleccion de proveedores
        Route::get('/proveedores',                      [ProveedorController::class, 'index']);
        Route::get('/proveedores/{categoria}',          [ProveedorController::class, 'porCategoria']);
        Route::get('/mi-boda/proveedores',              [BodaController::class, 'misProveedores']);
        Route::post('/mi-boda/proveedores',             [BodaController::class, 'guardarProveedores']);

        // Presupuesto definitivo (solicitar al admin)
        Route::post('/mi-boda/solicitar-presupuesto',   [BodaController::class, 'solicitarPresupuestoDefinitivo']);

        // Invitados (CRUD)
        Route::apiResource('mi-boda/invitados', InvitadoController::class)
            ->parameters(['invitados' => 'invitado']);

        // Mesas (CRUD)
        Route::apiResource('mi-boda/mesas', MesaController::class)
            ->parameters(['mesas' => 'mesa']);

        // Asignar invitado a mesa (drag & drop)
        Route::post('/mi-boda/asignar-mesa',            [InvitadoController::class, 'asignarMesa']);

        // Chat
        Route::get('/mi-boda/chat',                     [MensajeController::class, 'conversacion']);
        Route::post('/mi-boda/chat',                    [MensajeController::class, 'enviar']);
        Route::post('/mi-boda/chat/leer',               [MensajeController::class, 'marcarLeidos']);
    });
});


// =====================================================================
// Administrador
// =====================================================================

Route::prefix('admin')->middleware(['auth:sanctum', 'verified', 'es.administrador'])->group(function () {

    // Dashboard con estadisticas
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Clientes
    Route::get('/clientes',                         [AdminUsuarioController::class, 'index']);
    Route::get('/clientes/{id}',                    [AdminUsuarioController::class, 'show']);
    Route::post('/clientes/{id}/verificar-email',   [AdminUsuarioController::class, 'verificarEmail']);
    Route::delete('/clientes/{id}',                 [AdminUsuarioController::class, 'destroy']);

    // Bodas
    Route::get('/bodas',                            [AdminBodaController::class, 'index']);
    Route::get('/bodas/{boda}',                     [AdminBodaController::class, 'show']);
    Route::put('/bodas/{boda}/estado',              [AdminBodaController::class, 'cambiarEstado']);
    Route::put('/bodas/{boda}/presupuesto',         [AdminBodaController::class, 'fijarPresupuestoDefinitivo']);

    // Catalogo (CRUD completo)
    Route::apiResource('proveedores',   AdminProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::apiResource('paquetes',      AdminPaqueteController::class)->parameters(['paquetes' => 'paquete']);
    Route::apiResource('portfolio',     AdminPortfolioController::class)->parameters(['portfolio' => 'portfolio']);
    Route::apiResource('testimonios',   AdminTestimonioController::class)->parameters(['testimonios' => 'testimonio']);

    // Mensajes del formulario de contacto
    Route::get('/mensajes-contacto',                    [AdminMensajeContactoController::class, 'index']);
    Route::post('/mensajes-contacto/{id}/leer',         [AdminMensajeContactoController::class, 'marcarLeido']);
    Route::delete('/mensajes-contacto/{id}',            [AdminMensajeContactoController::class, 'destroy']);

    // Chat (todas las conversaciones)
    Route::get('/chat',                 [AdminChatController::class, 'conversaciones']);
    Route::get('/chat/{boda}',          [AdminChatController::class, 'conversacion']);
    Route::post('/chat/{boda}',         [AdminChatController::class, 'enviar']);
    Route::post('/chat/{boda}/leer',    [AdminChatController::class, 'marcarLeidos']);
});

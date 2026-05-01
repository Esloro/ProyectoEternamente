<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Boda\CuestionarioInicialRequest;
use App\Http\Requests\Boda\GuardarProveedoresRequest;
use App\Models\Boda;
use Illuminate\Http\Request;

class BodaController extends Controller
{
    /**
     * Devuelve los datos de la boda del cliente logueado. Si no tiene
     * boda creada todavia, devuelve null para que el frontend redirija
     * al cuestionario inicial.
     */
    public function miBoda(Request $request)
    {
        $boda = $request->user()->bodas()->with(['proveedores', 'mesas', 'invitados'])->latest()->first();

        if (! $boda) {
            return $this->ok(['boda' => null], 'Aun no has rellenado el cuestionario inicial.');
        }

        return $this->ok([
            'boda' => $boda,
            'cuenta_atras_dias' => max(0, now()->diffInDays($boda->fecha_boda, false)),
        ]);
    }

    /**
     * Guarda el cuestionario inicial (post-registro). Crea la boda en
     * estado pendiente_reunion. Si ya existe boda, la actualiza.
     */
    public function guardarCuestionario(CuestionarioInicialRequest $request)
    {
        $usuario = $request->user();
        $datos = $request->validated();

        // Si ya existe una boda en pendiente_reunion la actualizamos;
        // si no, creamos una nueva con estado pendiente.
        $boda = $usuario->bodas()->where('estado', Boda::ESTADO_PENDIENTE)->first();

        if ($boda) {
            $boda->update($datos);
        } else {
            $boda = $usuario->bodas()->create(array_merge($datos, [
                'estado' => Boda::ESTADO_PENDIENTE,
                'presupuesto_estimado' => 0,
            ]));
        }

        return $this->ok(
            ['boda' => $boda],
            'Hemos recibido tus datos. Nos pondremos en contacto contigo para concertar la reunion inicial.',
            201
        );
    }

    /**
     * Devuelve los proveedores que el cliente ha elegido para su boda.
     */
    public function misProveedores(Request $request)
    {
        $boda = $request->attributes->get('boda');
        return $this->ok(['proveedores' => $boda->proveedores]);
    }

    /**
     * Sustituye la lista de proveedores elegidos por la que llega en
     * el body. Recalcula el presupuesto estimado.
     */
    public function guardarProveedores(GuardarProveedoresRequest $request)
    {
        $boda = $request->attributes->get('boda');

        // Construimos el array para sync() con notas como pivot data.
        $sincronizar = [];
        foreach ($request->proveedores as $proveedor) {
            $sincronizar[$proveedor['id']] = ['notas' => $proveedor['notas'] ?? null];
        }

        $boda->proveedores()->sync($sincronizar);
        $boda->recalcularPresupuesto();

        return $this->ok(
            ['boda' => $boda->fresh('proveedores')],
            'Selecciones guardadas. Presupuesto estimado actualizado.'
        );
    }

    /**
     * El cliente solicita que el admin le confirme un presupuesto
     * definitivo. Por simplicidad solo enviamos un mensaje al admin
     * a traves del chat.
     */
    public function solicitarPresupuestoDefinitivo(Request $request)
    {
        $boda = $request->attributes->get('boda');
        $cliente = $request->user();
        $admin = \App\Models\Usuario::where('rol', 'administrador')->first();

        if ($admin) {
            $boda->mensajes()->create([
                'emisor_id'    => $cliente->id,
                'receptor_id'  => $admin->id,
                'contenido'    => 'Hola, me gustaria que me confirmarais un presupuesto definitivo para mi boda. Mi presupuesto estimado actual es de ' . number_format($boda->presupuesto_estimado, 2) . ' euros.',
                'leido'        => false,
            ]);
        }

        return $this->ok(null, 'Solicitud enviada. La wedding planner te contactara para confirmar el presupuesto definitivo.');
    }
}

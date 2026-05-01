<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BodaController extends Controller
{
    /**
     * Listado de bodas con filtros opcionales por estado y por cliente.
     */
    public function index(Request $request)
    {
        $bodas = Boda::query()
            ->with('usuario:id,nombre,apellidos,email')
            ->when($request->query('estado'), fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->query('q'), function ($q, $busqueda) {
                $q->whereHas('usuario', function ($u) use ($busqueda) {
                    $u->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('apellidos', 'like', "%{$busqueda}%")
                      ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->ok($bodas);
    }

    public function show(Boda $boda)
    {
        $boda->load(['usuario', 'proveedores', 'invitados', 'mesas.invitados', 'mensajes.emisor']);
        return $this->ok(['boda' => $boda]);
    }

    /**
     * Cambia el estado de la boda. Es la accion clave del flujo de
     * negocio: pasar de pendiente_reunion -> activa desbloquea el
     * panel completo del cliente.
     */
    public function cambiarEstado(Request $request, Boda $boda)
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in([
                Boda::ESTADO_PENDIENTE,
                Boda::ESTADO_ACTIVA,
                Boda::ESTADO_FINALIZADA,
                Boda::ESTADO_CANCELADA,
            ])],
        ]);

        $boda->update(['estado' => $datos['estado']]);

        return $this->ok(['boda' => $boda], 'Estado actualizado a "' . $datos['estado'] . '".');
    }

    public function fijarPresupuestoDefinitivo(Request $request, Boda $boda)
    {
        $datos = $request->validate([
            'presupuesto_definitivo' => ['required', 'numeric', 'min:0'],
        ]);

        $boda->update($datos);

        return $this->ok(['boda' => $boda], 'Presupuesto definitivo fijado.');
    }
}

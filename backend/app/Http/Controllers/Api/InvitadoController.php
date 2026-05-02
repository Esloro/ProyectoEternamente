<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitado\GuardarInvitadoRequest;
use App\Models\Invitado;
use Illuminate\Http\Request;

class InvitadoController extends Controller
{
    public function index(Request $request)
    {
        $boda = $request->attributes->get('boda');
        return $this->ok(['invitados' => $boda->invitados()->orderBy('nombre')->get()]);
    }

    public function store(GuardarInvitadoRequest $request)
    {
        $boda = $request->attributes->get('boda');
        $invitado = $boda->invitados()->create($request->validated());

        return $this->ok(['invitado' => $invitado], 'Invitado añadido.', 201);
    }

    public function show(Request $request, Invitado $invitado)
    {
        $this->verificarPropiedad($request, $invitado);
        return $this->ok(['invitado' => $invitado]);
    }

    public function update(GuardarInvitadoRequest $request, Invitado $invitado)
    {
        $this->verificarPropiedad($request, $invitado);
        $invitado->update($request->validated());

        return $this->ok(['invitado' => $invitado], 'Invitado actualizado.');
    }

    public function destroy(Request $request, Invitado $invitado)
    {
        $this->verificarPropiedad($request, $invitado);
        $invitado->delete();

        return $this->ok(null, 'Invitado eliminado.');
    }

    /**
     * Asigna (o reasigna, o desasigna) un invitado a una mesa.
     * Pensado para el drag & drop del organizador.
     */
    public function asignarMesa(Request $request)
    {
        $datos = $request->validate([
            'invitado_id' => ['required', 'integer', 'exists:invitados,id'],
            'mesa_id'     => ['nullable', 'integer', 'exists:mesas,id'],
        ]);

        $invitado = Invitado::findOrFail($datos['invitado_id']);
        $this->verificarPropiedad($request, $invitado);

        // Si nos dan una mesa, comprobamos capacidad teniendo en cuenta
        // al propio invitado y sus acompañantes.
        if (! is_null($datos['mesa_id']) && $invitado->mesa_id !== $datos['mesa_id']) {
            $mesa = \App\Models\Mesa::find($datos['mesa_id']);
            $plazasNecesarias = $invitado->plazasQueOcupa();
            if ($mesa && $mesa->plazasOcupadas() + $plazasNecesarias > $mesa->capacidad) {
                return $this->ko(
                    'En la mesa ' . $mesa->numero . ' no hay plazas suficientes para este invitado y sus acompañantes.',
                    422
                );
            }
        }

        $invitado->update(['mesa_id' => $datos['mesa_id']]);

        return $this->ok(['invitado' => $invitado->fresh()], 'Asignacion guardada.');
    }

    /**
     * Verifica que el invitado pertenece a la boda del usuario logueado.
     */
    private function verificarPropiedad(Request $request, Invitado $invitado): void
    {
        $boda = $request->attributes->get('boda');
        if ($invitado->boda_id !== $boda->id) {
            abort(403, 'Este invitado no pertenece a tu boda.');
        }
    }
}

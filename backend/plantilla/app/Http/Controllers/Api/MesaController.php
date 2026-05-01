<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mesa\GuardarMesaRequest;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index(Request $request)
    {
        $boda = $request->attributes->get('boda');
        return $this->ok([
            'mesas' => $boda->mesas()->with('invitados')->orderBy('numero')->get(),
        ]);
    }

    public function store(GuardarMesaRequest $request)
    {
        $boda = $request->attributes->get('boda');

        // Evitar dos mesas con el mismo numero (constraint unique en BD).
        if ($boda->mesas()->where('numero', $request->numero)->exists()) {
            return $this->ko('Ya existe una mesa con el numero ' . $request->numero . '.', 422);
        }

        $mesa = $boda->mesas()->create($request->validated());
        return $this->ok(['mesa' => $mesa], 'Mesa creada.', 201);
    }

    public function show(Request $request, Mesa $mesa)
    {
        $this->verificarPropiedad($request, $mesa);
        return $this->ok(['mesa' => $mesa->load('invitados')]);
    }

    public function update(GuardarMesaRequest $request, Mesa $mesa)
    {
        $this->verificarPropiedad($request, $mesa);

        if ($request->capacidad < $mesa->plazasOcupadas()) {
            return $this->ko('La nueva capacidad es menor que el numero de invitados ya asignados.', 422);
        }

        $mesa->update($request->validated());
        return $this->ok(['mesa' => $mesa], 'Mesa actualizada.');
    }

    public function destroy(Request $request, Mesa $mesa)
    {
        $this->verificarPropiedad($request, $mesa);

        // La FK en `invitados.mesa_id` es nullOnDelete, asi que los
        // invitados de esta mesa quedan sin asignar (no se borran).
        $mesa->delete();

        return $this->ok(null, 'Mesa eliminada. Sus invitados han quedado sin asignar.');
    }

    private function verificarPropiedad(Request $request, Mesa $mesa): void
    {
        $boda = $request->attributes->get('boda');
        if ($mesa->boda_id !== $boda->id) {
            abort(403, 'Esta mesa no pertenece a tu boda.');
        }
    }
}

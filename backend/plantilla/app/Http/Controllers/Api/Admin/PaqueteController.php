<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paquete;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    public function index()
    {
        return $this->ok(['paquetes' => Paquete::orderByDesc('destacado')->orderBy('precio')->get()]);
    }

    public function show(Paquete $paquete)
    {
        return $this->ok(['paquete' => $paquete]);
    }

    public function store(Request $request)
    {
        $paquete = Paquete::create($this->validar($request));
        return $this->ok(['paquete' => $paquete], 'Paquete creado.', 201);
    }

    public function update(Request $request, Paquete $paquete)
    {
        $paquete->update($this->validar($request));
        return $this->ok(['paquete' => $paquete], 'Paquete actualizado.');
    }

    public function destroy(Paquete $paquete)
    {
        $paquete->delete();
        return $this->ok(null, 'Paquete eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre'           => ['required', 'string', 'max:100'],
            'descripcion'      => ['required', 'string', 'max:1000'],
            'precio'           => ['required', 'numeric', 'min:0'],
            'caracteristicas'  => ['nullable', 'array'],
            'caracteristicas.*'=> ['string', 'max:200'],
            'destacado'        => ['boolean'],
        ]);
    }
}

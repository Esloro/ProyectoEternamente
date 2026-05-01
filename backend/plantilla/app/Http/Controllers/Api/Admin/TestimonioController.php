<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;
use Illuminate\Http\Request;

class TestimonioController extends Controller
{
    public function index()
    {
        return $this->ok(['testimonios' => Testimonio::latest()->get()]);
    }

    public function show(Testimonio $testimonio)
    {
        return $this->ok(['testimonio' => $testimonio]);
    }

    public function store(Request $request)
    {
        $testimonio = Testimonio::create($this->validar($request));
        return $this->ok(['testimonio' => $testimonio], 'Testimonio creado.', 201);
    }

    public function update(Request $request, Testimonio $testimonio)
    {
        $testimonio->update($this->validar($request));
        return $this->ok(['testimonio' => $testimonio], 'Testimonio actualizado.');
    }

    public function destroy(Testimonio $testimonio)
    {
        $testimonio->delete();
        return $this->ok(null, 'Testimonio eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'foto'           => ['nullable', 'string', 'max:500'],
            'valoracion'     => ['required', 'integer', 'min:1', 'max:5'],
            'comentario'     => ['required', 'string', 'max:1000'],
            'verificado'     => ['boolean'],
        ]);
    }
}

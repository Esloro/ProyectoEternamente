<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        return $this->ok(['portfolio' => Portfolio::orderBy('orden')->get()]);
    }

    public function show(Portfolio $portfolio)
    {
        return $this->ok(['portfolio' => $portfolio]);
    }

    public function store(Request $request)
    {
        $entrada = Portfolio::create($this->validar($request));
        return $this->ok(['portfolio' => $entrada], 'Entrada creada.', 201);
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $portfolio->update($this->validar($request));
        return $this->ok(['portfolio' => $portfolio], 'Entrada actualizada.');
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        return $this->ok(null, 'Entrada eliminada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'foto'        => ['required', 'string', 'max:500'],
            'orden'       => ['nullable', 'integer', 'min:0'],
        ]);
    }
}

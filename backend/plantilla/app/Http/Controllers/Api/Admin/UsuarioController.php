<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Listado paginado de clientes con filtro de busqueda por nombre/email.
     */
    public function index(Request $request)
    {
        $busqueda = $request->query('q');

        $clientes = Usuario::query()
            ->where('rol', 'cliente')
            ->when($busqueda, function ($consulta) use ($busqueda) {
                $consulta->where(function ($c) use ($busqueda) {
                    $c->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('apellidos', 'like', "%{$busqueda}%")
                      ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->withCount('bodas')
            ->orderBy('apellidos')
            ->paginate(20);

        return $this->ok($clientes);
    }

    public function show(int $id)
    {
        $cliente = Usuario::with(['bodas' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return $this->ok(['cliente' => $cliente]);
    }
}

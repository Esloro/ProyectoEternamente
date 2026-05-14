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

    /**
     * Verifica manualmente el email de un cliente (util para demos y
     * entornos de desarrollo donde Mailhog intercepta los correos).
     */
    public function verificarEmail(int $id)
    {
        $cliente = Usuario::findOrFail($id);

        if (! $cliente->hasVerifiedEmail()) {
            $cliente->markEmailAsVerified();
        }

        return $this->ok(['cliente' => $cliente], 'Email marcado como verificado.');
    }

    /**
     * El admin elimina (anonimiza + soft delete) un cliente. A diferencia
     * de la auto-eliminacion, aqui no hay restricciones por estado de
     * boda: el admin puede borrar incluso clientes con boda activa, por
     * ejemplo cuando el cliente se lo solicita explicitamente.
     */
    public function destroy(int $id)
    {
        $cliente = Usuario::findOrFail($id);

        if ($cliente->esAdministrador()) {
            return $this->ko('No se puede eliminar a un administrador desde este endpoint.', 403);
        }

        $cliente->anonimizarYEliminar();

        return $this->ok(null, 'Cliente eliminado correctamente. Los datos asociados a su boda se conservan.');
    }
}

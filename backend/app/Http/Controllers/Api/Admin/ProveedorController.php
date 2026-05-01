<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedor::query()
            ->when($request->query('categoria'), fn ($q, $cat) => $q->where('categoria', $cat))
            ->when($request->query('q'), fn ($q, $busqueda) => $q->where('nombre', 'like', "%{$busqueda}%"))
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(50);

        return $this->ok($proveedores);
    }

    public function show(Proveedor $proveedor)
    {
        return $this->ok(['proveedor' => $proveedor]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $proveedor = Proveedor::create($datos);

        return $this->ok(['proveedor' => $proveedor], 'Proveedor creado.', 201);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validar($request, $proveedor->id);
        $proveedor->update($datos);

        return $this->ok(['proveedor' => $proveedor], 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        // Soft delete: ponemos `activo = false` en vez de borrar para
        // no romper bodas que ya lo tenian elegido.
        $proveedor->update(['activo' => false]);

        return $this->ok(null, 'Proveedor desactivado.');
    }

    private function validar(Request $request, ?int $idIgnorar = null): array
    {
        return $request->validate([
            'nombre'      => ['required', 'string', 'max:150'],
            'categoria'   => ['required', Rule::in(array_keys(Proveedor::CATEGORIAS))],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'foto'        => ['nullable', 'string', 'max:500'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'activo'      => ['boolean'],
        ]);
    }
}

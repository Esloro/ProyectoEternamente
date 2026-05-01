<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Listado completo de proveedores activos, agrupado por categoria
     * para que el frontend monte directamente las pestañas/secciones
     * del panel de personalizacion.
     */
    public function index(Request $request)
    {
        $proveedores = Proveedor::activos()->orderBy('categoria')->orderBy('nombre')->get();

        $agrupados = $proveedores->groupBy('categoria')->map(function ($items, $categoria) {
            return [
                'categoria'  => $categoria,
                'etiqueta'   => Proveedor::CATEGORIAS[$categoria] ?? ucfirst($categoria),
                'proveedores'=> $items->values(),
            ];
        })->values();

        return $this->ok(['categorias' => $agrupados]);
    }

    /**
     * Devuelve los proveedores activos de una categoria concreta.
     */
    public function porCategoria(string $categoria)
    {
        if (! array_key_exists($categoria, Proveedor::CATEGORIAS)) {
            return $this->ko('Categoria desconocida.', 404);
        }

        $proveedores = Proveedor::activos()->deCategoria($categoria)->orderBy('nombre')->get();

        return $this->ok([
            'categoria'   => $categoria,
            'etiqueta'    => Proveedor::CATEGORIAS[$categoria],
            'proveedores' => $proveedores,
        ]);
    }
}

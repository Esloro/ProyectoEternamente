<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paquete;

class PaqueteController extends Controller
{
    /**
     * Lista publica de paquetes para la landing. Ordena destacados primero.
     */
    public function index()
    {
        $paquetes = Paquete::orderByDesc('destacado')->orderBy('precio')->get();

        return $this->ok(['paquetes' => $paquetes]);
    }
}

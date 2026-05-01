<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;

class TestimonioController extends Controller
{
    /**
     * Solo se devuelven testimonios verificados al publico.
     */
    public function index()
    {
        $testimonios = Testimonio::verificados()->latest()->get();
        return $this->ok(['testimonios' => $testimonios]);
    }
}

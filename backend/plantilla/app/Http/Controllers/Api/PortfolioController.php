<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    public function index()
    {
        $entradas = Portfolio::orderBy('orden')->get();
        return $this->ok(['portfolio' => $entradas]);
    }
}

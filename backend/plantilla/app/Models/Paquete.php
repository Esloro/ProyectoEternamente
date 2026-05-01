<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;

    protected $table = 'paquetes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'caracteristicas',
        'destacado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'caracteristicas' => 'array',
        'destacado' => 'boolean',
    ];
}

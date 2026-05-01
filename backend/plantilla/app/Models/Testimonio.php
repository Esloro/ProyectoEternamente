<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
    use HasFactory;

    protected $table = 'testimonios';

    protected $fillable = [
        'nombre_cliente',
        'foto',
        'valoracion',
        'comentario',
        'verificado',
    ];

    protected $casts = [
        'valoracion' => 'integer',
        'verificado' => 'boolean',
    ];

    public function scopeVerificados($consulta)
    {
        return $consulta->where('verificado', true);
    }
}

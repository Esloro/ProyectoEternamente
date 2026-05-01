<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $table = 'portfolio';

    protected $fillable = [
        'titulo',
        'descripcion',
        'foto',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];
}

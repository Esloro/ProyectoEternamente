<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'foto',
        'precio',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Lista de categorías válidas, usada en formularios y validación.
    public const CATEGORIAS = [
        'lugar'        => 'Lugar de celebración',
        'floristeria'  => 'Floristería',
        'musica'       => 'Música y DJ',
        'fotografia'   => 'Fotografía y vídeo',
        'catering'     => 'Catering y menú',
        'decoracion'   => 'Decoración y temática',
        'vestuario'    => 'Vestuario y trajes',
        'transporte'   => 'Coches y transporte',
        'detalles'     => 'Detalles para invitados',
        'tarta'        => 'Tarta nupcial',
    ];

    public function bodas(): BelongsToMany
    {
        return $this->belongsToMany(Boda::class, 'boda_proveedor')
            ->withPivot('notas')
            ->withTimestamps();
    }

    public function scopeActivos($consulta)
    {
        return $consulta->where('activo', true);
    }

    public function scopeDeCategoria($consulta, string $categoria)
    {
        return $consulta->where('categoria', $categoria);
    }
}

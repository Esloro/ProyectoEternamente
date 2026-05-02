<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = [
        'boda_id',
        'numero',
        'capacidad',
    ];

    protected $casts = [
        'numero' => 'integer',
        'capacidad' => 'integer',
    ];

    public function boda(): BelongsTo
    {
        return $this->belongsTo(Boda::class, 'boda_id');
    }

    public function invitados(): HasMany
    {
        return $this->hasMany(Invitado::class, 'mesa_id');
    }

    /**
     * Cada invitado ocupa 1 plaza + sus acompañantes.
     */
    public function plazasOcupadas(): int
    {
        return (int) ($this->invitados()->sum('num_acompanantes') + $this->invitados()->count());
    }

    public function plazasLibres(): int
    {
        return max(0, $this->capacidad - $this->plazasOcupadas());
    }
}

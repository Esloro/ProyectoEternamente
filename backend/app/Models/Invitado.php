<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitado extends Model
{
    use HasFactory;

    protected $table = 'invitados';

    protected $fillable = [
        'boda_id',
        'nombre',
        'alergias',
        'num_acompanantes',
        'mesa_id',
    ];

    protected $casts = [
        'num_acompanantes' => 'integer',
    ];

    /**
     * Plazas que ocupa el invitado en una mesa: el propio invitado mas sus
     * acompañantes.
     */
    public function plazasQueOcupa(): int
    {
        return 1 + (int) $this->num_acompanantes;
    }

    public function boda(): BelongsTo
    {
        return $this->belongsTo(Boda::class, 'boda_id');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
}

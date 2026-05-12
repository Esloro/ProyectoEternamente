<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    protected $fillable = [
        'boda_id',
        'emisor_id',
        'receptor_id',
        'contenido',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function boda(): BelongsTo
    {
        return $this->belongsTo(Boda::class, 'boda_id');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'emisor_id');
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'receptor_id');
    }
}

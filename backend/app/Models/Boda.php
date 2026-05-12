<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boda extends Model
{
    use HasFactory;

    protected $table = 'bodas';

    protected $fillable = [
        'usuario_id',
        'nombre_pareja',
        'tipo_ceremonia',
        'lugar_celebracion',
        'fecha_boda',
        'num_invitados',
        'franja_horaria',
        'tematica',
        'tipo_comida',
        'presupuesto_orientativo',
        'estado',
        'presupuesto_estimado',
        'presupuesto_definitivo',
    ];

    protected $casts = [
        // Serializamos como 'Y-m-d' para que el JSON no incluya hora ni
        // zona horaria. Si no, con APP_TIMEZONE=Europe/Madrid Carbon
        // convierte a UTC y le resta dos horas, lo que en el frontend
        // (substring 10) hace que la fecha pierda un dia en cada save.
        'fecha_boda' => 'date:Y-m-d',
        'num_invitados' => 'integer',
        'presupuesto_estimado' => 'decimal:2',
        'presupuesto_definitivo' => 'decimal:2',
    ];

    // Estados posibles del flujo de negocio.
    public const ESTADO_PENDIENTE = 'pendiente_reunion';
    public const ESTADO_ACTIVA    = 'activa';
    public const ESTADO_FINALIZADA = 'finalizada';
    public const ESTADO_CANCELADA  = 'cancelada';

    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    /**
     * Recalcula el presupuesto estimado sumando el precio de todos
     * los proveedores elegidos. Se llama al guardar elecciones.
     */
    public function recalcularPresupuesto(): void
    {
        $total = $this->proveedores()->sum('precio');
        $this->update(['presupuesto_estimado' => $total]);
    }

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(Proveedor::class, 'boda_proveedor')
            ->withPivot('notas')
            ->withTimestamps();
    }

    public function invitados(): HasMany
    {
        return $this->hasMany(Invitado::class, 'boda_id');
    }

    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class, 'boda_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'boda_id');
    }
}

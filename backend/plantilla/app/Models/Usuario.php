<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verificado_en' => 'datetime',
        'password' => 'hashed',
    ];

    // ----------------------------------------------------------------
    // Sobreescribimos los métodos de MustVerifyEmail para usar la
    // columna en español `email_verificado_en` en lugar del estándar
    // de Laravel (`email_verified_at`).
    // ----------------------------------------------------------------

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verificado_en);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verificado_en' => $this->freshTimestamp(),
        ])->save();
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function bodas(): HasMany
    {
        return $this->hasMany(Boda::class, 'usuario_id');
    }

    public function mensajesEnviados(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'emisor_id');
    }

    public function mensajesRecibidos(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'receptor_id');
    }

    // ----------------------------------------------------------------
    // Helpers de rol
    // ----------------------------------------------------------------

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esCliente(): bool
    {
        return $this->rol === 'cliente';
    }
}

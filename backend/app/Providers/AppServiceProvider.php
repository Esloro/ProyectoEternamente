<?php

namespace App\Providers;

use App\Models\Usuario;
use App\Notifications\RecuperarPasswordNotification;
use App\Notifications\VerificarEmailNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Sustituimos las notificaciones por defecto de Laravel por las
        // nuestras (en español, con el branding del Wedding Planner).
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new VerificarEmailNotification)->toMail($notifiable);
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $notification = new RecuperarPasswordNotification($token);
            return $notification->toMail($notifiable);
        });

        // Hacemos que Laravel use el modelo Usuario donde implicitamente
        // espera "User" (por ejemplo en el comando make:auth tools).
        $this->app['config']->set('auth.providers.users.model', Usuario::class);
    }
}

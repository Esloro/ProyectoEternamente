<?php

namespace App\Providers;

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
            return (new RecuperarPasswordNotification($token))->toMail($notifiable);
        });
    }
}

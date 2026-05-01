<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email de recuperacion de contraseña en español. La URL apunta al
 * frontend (Angular) con el token y el email como query params; alli
 * se muestra un formulario que envia esos datos al endpoint de reset.
 */
class RecuperarPasswordNotification extends ResetPasswordBase
{
    public function toMail($notifiable): MailMessage
    {
        $url = config('app.frontend_url', 'http://localhost:4200')
             . '/recuperar-password?token=' . $this->token
             . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Restablece tu contraseña - Wedding Planner')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace caduca en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.')
            ->salutation('Un saludo, el equipo de Wedding Planner.');
    }
}

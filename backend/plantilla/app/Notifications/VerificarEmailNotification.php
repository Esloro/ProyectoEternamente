<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email de verificacion en español. Sustituye al notification por defecto
 * de Laravel y construye una URL firmada que apunta al endpoint de la API
 * (que despues redirige al frontend a una pagina de "email verificado").
 */
class VerificarEmailNotification extends VerifyEmailBase
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirma tu correo electronico - Wedding Planner')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Gracias por registrarte en Wedding Planner. Por favor, confirma tu correo electronico haciendo clic en el siguiente boton.')
            ->action('Confirmar correo', $url)
            ->line('Este enlace caduca en 60 minutos.')
            ->line('Si no creaste una cuenta, puedes ignorar este mensaje.')
            ->salutation('Un saludo, el equipo de Wedding Planner.');
    }
}

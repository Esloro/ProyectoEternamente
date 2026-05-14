<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Email con URL firmada para confirmar la eliminacion de la cuenta.
 * El enlace caduca en 30 minutos y solo puede usarlo el destinatario
 * que abrio la solicitud desde el panel cliente.
 */
class EliminarCuentaNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'cuenta.eliminar',
            now()->addMinutes(30),
            ['id' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject('Confirma la eliminación de tu cuenta - Eternamente')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Hemos recibido una solicitud para eliminar tu cuenta de Eternamente.')
            ->line('Si confirmas, tus datos personales (nombre, email y teléfono) serán anonimizados y no podrás volver a iniciar sesión con esta cuenta. La información asociada a tu boda se conservará en nuestros registros internos.')
            ->action('Confirmar eliminación de la cuenta', $url)
            ->line('Este enlace caduca en 30 minutos.')
            ->line('Si no has solicitado esto, ignora este mensaje: tu cuenta no se eliminará.')
            ->salutation('Un saludo, el equipo de Eternamente.');
    }
}

<?php

namespace App\Mail;

use App\Models\MensajeContacto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail enviado al administrador cada vez que alguien rellena el
 * formulario publico de contacto en la landing.
 */
class MensajeContactoRecibido extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MensajeContacto $mensaje) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de contacto: ' . $this->mensaje->nombre,
            replyTo: [$this->mensaje->email],
        );
    }

    public function content(): Content
    {
        // markdown: en lugar de view: porque la plantilla usa componentes
        // de Markdown (mail::message, mail::button) que requieren que
        // Laravel registre el namespace `mail::` automaticamente.
        return new Content(markdown: 'emails.mensaje_contacto_recibido');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $correo;

    public function __construct($token, $correo)
    {
        $this->token = $token;
        $this->correo = $correo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Generamos la URL absoluta con el token y el correo
        $url = route('password.reset', [
            'token' => $this->token,
            'correo' => $this->correo,
        ]);

        return (new MailMessage)
            ->subject('Recuperación de Contraseña - Alcaldía de Carrizal')
            ->view('emails.plantilla-base', [
                'titulo'      => 'Recuperación de Contraseña — Alcaldía de Carrizal',
                'subtitulo'   => 'Restablecimiento de credenciales',
                'contenido'   => 'Has solicitado restablecer tu contraseña para acceder a la plataforma de Ecosocialismo. Haz clic en el botón inferior para proceder con el cambio.<br><br>Este enlace de seguridad expirará en 60 minutos.',
                'textoAccion' => 'Restablecer Contraseña',
                'urlAccion'   => $url
            ]);
    }
}
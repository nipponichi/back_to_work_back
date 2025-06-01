<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userData;
    protected $templateType;
    protected $customSubject;

    const TEMPLATE_WELCOME = 'welcome';
    const TEMPLATE_NOTIFICATION = 'notification';
    const TEMPLATE_RESET_PASSWORD = 'reset_password';

    public function __construct(array $userData, string $templateType = self::TEMPLATE_NOTIFICATION, string $customSubject = null)
    {
        $this->userData = $userData;
        $this->customSubject = $customSubject;
        $this->templateType = $templateType;
    }

public function envelope(): Envelope
{
    if ($this->customSubject !== null) {
        return new Envelope(subject: $this->customSubject);
    }

    $subject = match($this->templateType) {
        self::TEMPLATE_WELCOME => 'Bienvenido a We Agree, verifica tu cuenta',
        self::TEMPLATE_NOTIFICATION => 'Notificación importante',
        self::TEMPLATE_RESET_PASSWORD => 'Restablece tu contraseña',
        default => 'Notificación importante'
    };

    return new Envelope(subject: $subject);
}

    public function content(): Content
    {
        $html = match($this->templateType) {
            self::TEMPLATE_WELCOME => $this->getWelcomeTemplate(),
            self::TEMPLATE_NOTIFICATION => $this->getNotificationTemplate(),
            self::TEMPLATE_RESET_PASSWORD => $this->getResetPasswordTemplate(),
            default => $this->getDefaultTemplate()
        };

        return new Content(htmlString: $html);
    }

    protected function getWelcomeTemplate(): string
    {
        $currentYear = date('Y');
        $frontendUrl = config('app.frontend_url').'verify-email?'.http_build_query([
            'id' => $this->userData['id'],
            'hash' => sha1($this->userData['email']),
            'signature' => $this->userData['signature']
        ]);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { max-width: 150px; height: auto; }
                .button { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background-color: #3490dc; 
                    color: white !important; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    margin: 15px 0;
                }
                .footer { 
                    margin-top: 30px; 
                    padding-top: 20px; 
                    border-top: 1px solid #eee; 
                    font-size: 12px; 
                    color: #777; 
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f9/Logo_Williams_F1.png" alt="WeAgree Logo" class="logo">
            </div>
            
            <h1>¡Bienvenido, {$this->userData['name']}!</h1>
            
            <p>Gracias por registrarte en WeAgree. Por favor verifica tu dirección de email:</p>
            
            <div style="text-align: center;">
                <a href="{$frontendUrl}" class="button">Verificar mi Email</a>
            </div>
            
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
            <small>{$frontendUrl}</small></p>
            
            <div class="footer">
                <p>© {$currentYear} WeAgree. Todos los derechos reservados.</p>
                <p>Si no solicitaste este registro, por favor ignora este mensaje.</p>
            </div>
        </body>
        </html>
        HTML;
    }

    protected function getNotificationTemplate(): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body>
            <div class="header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f9/Logo_Williams_F1.png" alt="WeAgree Logo" class="logo"/>
            </div>
            <h1>Hola, {$this->userData['nombre']}</h1>
            <p>{$this->userData['mensaje']}</p>
        </body>
        </html>
        HTML;
    }

    protected function getResetPasswordTemplate(): string
    {
        $currentYear = date('Y');
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { max-width: 150px; height: auto; }
                .button { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background-color: #3490dc; 
                    color: white !important; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    margin: 15px 0;
                }
                .footer { 
                    margin-top: 30px; 
                    padding-top: 20px; 
                    border-top: 1px solid #eee; 
                    font-size: 12px; 
                    color: #777; 
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f9/Logo_Williams_F1.png" alt="WeAgree Logo" class="logo">
            </div>
            
            <h1>Restablecimiento de contraseña</h1>
            
            <p>Hola, {$this->userData['name']}!</p>
            
            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el siguiente botón para continuar:</p>
            
            <div style="text-align: center;">
                <a href="{$this->userData['reset_link']}" class="button">Restablecer contraseña</a>
            </div>
            
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
            <small>{$this->userData['reset_link']}</small></p>
            
            <p><em>Este enlace expirará en 60 minutos por motivos de seguridad.</em></p>
            
            <div class="footer">
                <p>© {$currentYear} WeAgree. Todos los derechos reservados.</p>
                <p>Si no solicitaste este restablecimiento, por favor ignora este mensaje.</p>
            </div>
        </body>
        </html>
        HTML;
    }

    public function attachments(): array
    {
        return [];
    }
}
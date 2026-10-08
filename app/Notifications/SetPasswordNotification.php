<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Définir votre mot de passe')
            ->greeting('Bonjour,')
            ->line('Un compte a été créé pour vous. Choisissez un mot de passe pour accéder à votre espace.')
            ->action('Définir mon mot de passe', route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]))
            ->line('Ce lien expirera dans '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes.')
            ->line('Si vous n’attendiez pas ce courriel, vous pouvez l’ignorer.');
    }
}

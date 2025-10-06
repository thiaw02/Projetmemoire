<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomePatient extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Bienvenue sur SmartHealth')
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Votre inscription sur notre plateforme médicale a été réussie.')
                    ->line('Nous sommes ravis de vous compter parmi nos patients.')
                    ->action('Se connecter', url('/login'))
                    ->line('Merci d’utiliser notre plateforme SmartHealth !');
    }
}

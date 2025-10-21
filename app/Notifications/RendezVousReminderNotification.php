<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Rendez_vous;
use Carbon\Carbon;

class RendezVousReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Rendez_vous $rdv,
        public string $type // 'tomorrow' | 'today'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = Carbon::parse($this->rdv->date)->format('d/m/Y');
        $heure = $this->rdv->heure;
        $medecin = optional($this->rdv->medecin)->name ?? 'votre médecin';

        $mail = (new MailMessage())
            ->greeting('Bonjour '.($notifiable->name ?? ''))
            ->line('Ceci est un rappel de votre rendez-vous médical.')
            ->line('Médecin: '.$medecin)
            ->line('Date: '.$date.' à '.$heure)
            ->action('Voir mes rendez-vous', url('/patient/rendezvous'))
            ->line('Merci d’utiliser notre plateforme.');

        if ($this->type === 'tomorrow') {
            $mail->subject('Rappel: votre rendez-vous est demain');
        } else {
            $mail->subject('Rappel: votre rendez-vous est aujourd\'hui');
        }
        return $mail;
    }
}

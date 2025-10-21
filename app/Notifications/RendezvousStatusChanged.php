<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Rendez_vous;
use Carbon\Carbon;

class RendezvousStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Rendez_vous $rdv) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $date = $this->rdv->date ? Carbon::parse($this->rdv->date)->translatedFormat('d F Y') : '';
        $heure = $this->rdv->heure ?: '';
        $medecin = $this->rdv->medecin->name ?? '—';
        $status = ucfirst(str_replace('_',' ', $this->rdv->statut ?? ''));

        $subject = match (strtolower($this->rdv->statut)) {
            'confirmé', 'confirme' => 'Votre rendez-vous a été confirmé',
            'annulé', 'annule' => 'Votre rendez-vous a été annulé',
            default => 'Mise à jour de votre rendez-vous',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour,')
            ->line("Statut: $status")
            ->line("Date: $date à $heure")
            ->line("Médecin: $medecin")
            ->action('Voir mes rendez-vous', url(route('patient.rendezvous')))
            ->line('Merci d\'utiliser SMART-HEALTH.');
    }
}
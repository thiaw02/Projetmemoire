<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Rendez_vous;
use Carbon\Carbon;

class NewRendezvousRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Rendez_vous $rdv) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $patient = $this->rdv->patient;
        $date = $this->rdv->date ? Carbon::parse($this->rdv->date)->translatedFormat('d F Y') : '';
        $heure = $this->rdv->heure ?: '';
        $medecin = $this->rdv->medecin->name ?? '—';
        $patientName = $patient ? $patient->nom . ' ' . $patient->prenom : 'Patient inconnu';

        return (new MailMessage)
            ->subject('Nouvelle demande de rendez-vous - ' . $patientName)
            ->greeting('Bonjour,')
            ->line('Une nouvelle demande de rendez-vous a été reçue.')
            ->line("**Patient:** $patientName")
            ->line("**Date demandée:** $date à $heure")
            ->line("**Médecin:** $medecin")
            ->line("**Motif:** " . ($this->rdv->motif ?: 'Non spécifié'))
            ->action('Gérer les rendez-vous', url(route('secretaire.rendezvous')))
            ->line('Veuillez traiter cette demande rapidement.')
            ->line('SMART-HEALTH System');
    }
}
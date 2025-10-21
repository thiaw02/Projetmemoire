<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ordonnances;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdonnanceCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ordonnances $ordonnance) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ord = $this->ordonnance->loadMissing(['patient','medecin']);
        $patient = $ord->patient;
        $medecin = $ord->medecin;

        $data = [
            'ordonnance' => $ord,
            'patient' => $patient,
            'medecin' => $medecin,
            'generatedAt' => now(),
        ];

        $mail = (new MailMessage)
            ->subject('Votre ordonnance médicale')
            ->greeting('Bonjour '.($notifiable->name ?? ''))
            ->line('Veuillez trouver ci-joint votre ordonnance médicale.')
            ->action('Voir mon espace patient', url('/patient/dashboard'))
            ->line('Prenez bien soin de vous.');

        // Générer un PDF et l’attacher si possible
        try {
            if (class_exists(Pdf::class)) {
                $pdf = Pdf::loadView('ordonnances.pdf', $data)->output();
                $filename = 'Ordonnance_'.$patient->nom.'_'.$patient->prenom.'_'.$ord->id.'.pdf';
                $mail->attachData($pdf, $filename, ['mime' => 'application/pdf']);
            } else {
                $html = view('ordonnances.pdf', $data)->render();
                $filename = 'Ordonnance_'.$patient->nom.'_'.$patient->prenom.'_'.$ord->id.'.html';
                $mail->attachData($html, $filename, ['mime' => 'text/html']);
            }
        } catch (\Throwable $e) {
            // En cas d’échec de génération, on envoie sans pièce jointe
        }

        return $mail;
    }
}

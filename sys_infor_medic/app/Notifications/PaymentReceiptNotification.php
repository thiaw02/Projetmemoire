<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceiptNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Votre quittance - ' . config('app.name'))
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Nous vous remercions pour votre paiement.')
            ->line('Montant: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' XOF')
            ->line('Référence: ' . ($this->order->provider_ref ?? '-'))
            ->action('Voir la quittance', route('payments.receipt', $this->order->id));

        // Joindre le PDF si DomPDF est dispo
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $data = [
                'order' => $this->order->loadMissing('items','user'),
                'hospital' => [
                    'name' => config('app.name', 'SMART-HEALTH'),
                    'address' => \App\Models\Setting::getValue('hospital.address', 'Adresse'),
                    'phone' => \App\Models\Setting::getValue('hospital.phone', ''),
                ],
                'generatedAt' => now(),
            ];
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt', $data)->output();
            $message->attachData($pdf, 'Quittance_'.$this->order->id.'.pdf', ['mime' => 'application/pdf']);
        }

        return $message;
    }
}

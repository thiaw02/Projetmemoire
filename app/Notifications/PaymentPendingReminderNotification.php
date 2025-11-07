<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentPendingReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->order->payment_url ?: route('patient.dashboard');
        return (new MailMessage)
            ->subject('Rappel de paiement en attente - ' . config('app.name'))
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Vous avez un paiement en attente.')
            ->line('Montant: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' XOF')
            ->action('Régler maintenant', $url)
            ->line('Merci de votre confiance.');
    }
}

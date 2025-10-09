<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentLinkNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->order->payment_url ?: url('/');
        return (new MailMessage)
            ->subject('Lien de paiement - ' . config('app.name'))
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Un lien de paiement a été généré pour votre règlement.')
            ->line('Montant: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' XOF')
            ->action('Payer maintenant', $url)
            ->line('Merci pour votre confiance.');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Message;

class NewChatMessage extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $sender = $this->message->sender->name ?? 'Un utilisateur';
        $preview = $this->message->body ?: ($this->message->file_type === 'image' ? 'Image envoyée' : 'Fichier envoyé');
        return (new MailMessage)
            ->subject('Nouveau message de chat')
            ->greeting('Bonjour,')
            ->line("Vous avez reçu un nouveau message de $sender.")
            ->line("Message: $preview")
            ->action('Ouvrir le chat', url(route('chat.index', ['partner_id' => $this->otherParticipantId($notifiable->id)])))
            ->line('Merci d\'utiliser SMART-HEALTH');
    }

    private function otherParticipantId(int $notifiableId): int
    {
        $c = $this->message->conversation;
        return $c->user_one_id === $notifiableId ? $c->user_two_id : $c->user_one_id;
    }
}

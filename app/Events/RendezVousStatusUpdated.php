<?php

namespace App\Events;

use App\Models\Rendez_vous;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RendezVousStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Rendez_vous $rendezvous, public string $oldStatus) {}

    public function broadcastOn()
    {
        return [
            new Channel('rendezvous-updates'),
            new PrivateChannel('patient.' . $this->rendezvous->user_id)
        ];
    }

    public function broadcastWith()
    {
        $patient = $this->rendezvous->patient;
        $patientName = $patient ? $patient->nom . ' ' . $patient->prenom : 'Patient inconnu';
        
        return [
            'id' => $this->rendezvous->id,
            'patient_name' => $patientName,
            'medecin_name' => $this->rendezvous->medecin->name ?? '—',
            'date' => $this->rendezvous->date,
            'heure' => $this->rendezvous->heure,
            'motif' => $this->rendezvous->motif,
            'old_status' => $this->oldStatus,
            'new_status' => $this->rendezvous->statut,
            'updated_at' => $this->rendezvous->updated_at->format('d/m/Y à H:i'),
        ];
    }

    public function broadcastAs()
    {
        return 'rendezvous.status.updated';
    }
}
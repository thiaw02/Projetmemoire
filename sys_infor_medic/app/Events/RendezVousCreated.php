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

class RendezVousCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Rendez_vous $rendezvous) {}

    public function broadcastOn()
    {
        return new Channel('rendezvous-updates');
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
            'statut' => $this->rendezvous->statut,
            'created_at' => $this->rendezvous->created_at->format('d/m/Y à H:i'),
        ];
    }

    public function broadcastAs()
    {
        return 'rendezvous.created';
    }
}
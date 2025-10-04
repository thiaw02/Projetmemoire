<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendez_vous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'user_id',
        'medecin_id',
        'date',
        'heure',
        'motif',
        'statut',
    ];

    // Le patient
    public function patient()
    {
        // Lien via le user_id du patient (rendez_vous.user_id = patients.user_id)
        return $this->belongsTo(Patient::class, 'user_id', 'user_id');
    }

    // Le médecin
    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}


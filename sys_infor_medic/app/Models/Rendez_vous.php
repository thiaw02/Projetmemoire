<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendez_vous extends Model
{
    use HasFactory;

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
        return $this->belongsTo(User::class, 'user_id');
    }

    // Le médecin
    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}


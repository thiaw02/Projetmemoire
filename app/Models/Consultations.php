<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultations extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'date_consultation',
        'symptomes',
        'diagnostic',
        'traitement',
        'statut',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
     public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'consultation_id');
    }

    // 🔹 Relation avec Analyses
    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'consultation_id');
    }
}


<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultations extends Model
{
    use HasFactory;

    protected $table = 'consultations';

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
        // Rattache les ordonnances par patient, faute de colonne consultation_id
        return $this->hasMany(Ordonnances::class, 'patient_id', 'patient_id');
    }

    public function analyses()
    {
        // Rattache les analyses par patient, faute de colonne consultation_id
        return $this->hasMany(Analyses::class, 'patient_id', 'patient_id');
    }

    public function evaluation()
    {
        return $this->hasOne(EvaluationMedecin::class, 'consultation_id');
    }

    // Vérifier si cette consultation peut être évaluée
    public function peutEtreEvaluee(): bool
    {
        return $this->statut === 'terminee' 
               && !$this->evaluation 
               && $this->date_consultation >= now()->subMonths(3);
    }

}


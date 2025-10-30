<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_dossier',
        'nom',
        'prenom',
        'user_id',
        'secretary_user_id',
        'sexe',
        'date_naissance',
        'adresse',
        'email',
        'telephone',
        'groupe_sanguin',
        'antecedents',
    ];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultations::class, 'patient_id');
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class, 'patient_id');
    }

    public function rendez_vous()
    {
        // Relier les rendez-vous via le user_id du patient (Rendez_vous.user_id = patients.user_id)
        return $this->hasMany(Rendez_vous::class, 'user_id', 'user_id');
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnances::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'patient_service');
    }

    public function dossierpatients()
    {
        return $this->hasMany(Dossier_medicaux::class);
    }

    public function dossier_administratifs()
    {
        return $this->hasMany(Dossier_administratifs::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyses::class, 'patient_id');
    }

    public function secretaire()
    {
        return $this->belongsTo(User::class, 'secretary_user_id');
    }

    public function evaluations()
    {
        return $this->hasMany(EvaluationMedecin::class, 'patient_id');
    }
}

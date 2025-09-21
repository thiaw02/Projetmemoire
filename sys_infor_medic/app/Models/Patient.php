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
        'nom',
        'prenom',
        'user_id',
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
 public function rendez_vous()
    {
        return $this->hasMany(Rendez_vous::class, 'user_id');
        // 'user_id' est la colonne de la table rendez_vous qui référence le patient
    }

    public function consultations()
{
    return $this->hasMany(Consultations::class);
}
    public function ordonnances()
    {
        return $this->hasMany(Ordonnances::class);
    
    }
    public function dossierpatients()
    {
        return $this->hasMany(Dossier_medicaux::class);
    }
    public function dossier_administratifs()
    {
        return $this->hasMany(Dossier_administratifs::class);
    }
}
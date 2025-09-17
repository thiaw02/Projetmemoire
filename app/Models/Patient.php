<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Dossier_medicaux;
use App\Models\Rendez_vous;
use App\Models\Ordonnances;
use App\Models\Analyses;

class Patient extends Model
{
    use HasFactory;

    // Colonnes remplissables
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

    // Relation avec les rendez-vous via user_id
    public function rendez_vous()
    {
        // user_id dans rendez_vous correspond au user_id dans patients
        return $this->hasMany(Rendez_vous::class, 'user_id', 'user_id');
    }

    // Relation avec les ordonnances
    public function Ordonnances()
    {
        return $this->hasMany(Ordonnances::class);
    }

    // Relation avec les analyses
    public function analyses()
    {
        return $this->hasMany(Analyses::class);
    }
    public function dossiers()
    {
        return $this->hasMany(Dossier_medicaux::class); // référence le bon modèle
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Consultations;
use App\Models\Dossier_medicaux;
use App\Models\Ordonnances;

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

    /**
     * Relation avec le modèle User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les consultations
     */
    public function consultations()
    {
        return $this->hasMany(Consultations::class);
    }

    /**
     * Relation avec les dossiers médicaux
     */
    public function dossiers()
    {
        return $this->hasMany(Dossier_medicaux::class);
    }

    /**
     * Relation avec les ordonnances
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnances::class);
    }
}

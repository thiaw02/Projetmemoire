<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Attributs modifiables en masse
    protected $fillable = [
    'name',
    'email',
    'password',
    'telephone',
    'adresse',
    'date_naissance',
    'sexe',
    'role',
    'specialite',
];


    // Attributs masqués lors de la sérialisation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Types de données castés
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hachage automatique du mot de passe à la sauvegarde
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Hash::needsRehash($value) ? Hash::make($value) : $value,
        );
    }

    /**
     * Vérifie si l'utilisateur a un rôle donné
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Relation : Si le user est un patient
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Ajoute d'autres relations selon les besoins
     * Exemple :
     * - medecin() si tu as un modèle Medecin
     * - infirmier() si tu veux un modèle Infirmier
     */
}

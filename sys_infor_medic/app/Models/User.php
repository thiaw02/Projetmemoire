<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Auditable, SoftDeletes;

    // Attributs modifiables en masse
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'specialite',
        'pro_phone',
        'matricule',
        'cabinet',
        'horaires',
        'avatar_url',
        'active',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'department',
        'hire_date',
        'salary',
        'notes',
    ];

    // Attributs masqués lors de la sérialisation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Types de données castés
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
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
        return $this->hasOne(Patient::class,'user_id');
    }
    public function consultations()
    {
        return $this->hasMany(Consultations::class, 'medecin_id');
    }

    public function evaluationsRecues()
    {
        return $this->hasMany(EvaluationMedecin::class, 'medecin_id');
    }

    public function evaluationsVisibles()
    {
        return $this->hasMany(EvaluationMedecin::class, 'medecin_id')
                    ->where('visible_publiquement', true)
                    ->where('statut', 'validee');
    }

    // Obtenir la note moyenne du médecin
    public function getNoteMoyenneAttribute(): ?float
    {
        return EvaluationMedecin::noteMoyenneMedecin($this->id);
    }

    // Obtenir le nombre d'évaluations
    public function getNombreEvaluationsAttribute(): int
    {
        return EvaluationMedecin::nombreEvaluationsMedecin($this->id);
    }

    // Obtenir le pourcentage de recommandations
    public function getPourcentageRecommandationsAttribute(): ?float
    {
        return EvaluationMedecin::pourcentageRecommandationsMedecin($this->id);
    }

    /**
     * Infirmiers affectés à ce médecin
     */
    public function nurses()
    {
        return $this->belongsToMany(User::class, 'medecin_infirmier', 'medecin_id', 'infirmier_id')
            ->where('users.role', 'infirmier');
    }

    /**
     * Médecins auxquels cet infirmier est affecté
     */
    public function doctors()
    {
        return $this->belongsToMany(User::class, 'medecin_infirmier', 'infirmier_id', 'medecin_id')
            ->where('users.role', 'medecin');
    }

    /**
     * Configuration de l'audit pour le modèle User
     */
    protected function getAuditableCriticalFields(): array
    {
        return [
            'password',
            'email',
            'role',
            'active',
        ];
    }

    protected function getAuditableImportantFields(): array
    {
        return [
            'name',
            'pro_phone',
            'specialite',
            'matricule',
        ];
    }

    protected function getAuditableExcludes(): array
    {
        // Utiliser les exclusions par défaut du trait et ajouter les spécifiques
        return array_merge($this->getDefaultAuditableExcludes(), [
            'avatar_url', // Changements d'avatar moins critiques pour les utilisateurs
        ]);
    }

    /**
     * Contexte métadonnées spécifique aux utilisateurs
     */
    public function getAuditContextMetadata(): array
    {
        return [
            'user_role' => $this->role,
            'user_active' => $this->active,
            'specialite' => $this->specialite,
        ];
    }

    /**
     * Ajoute d'autres relations selon les besoins
     * Exemple :
     * - medecin() si tu as un modèle Medecin
     * - infirmier() si tu veux un modèle Infirmier
     */
}

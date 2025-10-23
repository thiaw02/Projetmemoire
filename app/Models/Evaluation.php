<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'evaluated_user_id',
        'type_evaluation',
        'note',
        'commentaire',
        'consultation_id'
    ];

    protected $casts = [
        'note' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation vers le patient qui évalue
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Alias pour patient (utilisé dans certaines vues)
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Relation vers le professionnel évalué (médecin ou infirmier)
     */
    public function evaluatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_user_id');
    }

    /**
     * Relation vers la consultation (optionnelle)
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultations::class);
    }

    /**
     * Scope pour filtrer par type d'évaluation
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type_evaluation', $type);
    }

    /**
     * Scope pour les évaluations d'un professionnel spécifique
     */
    public function scopeForProfessional($query, $userId)
    {
        return $query->where('evaluated_user_id', $userId);
    }

    /**
     * Scope pour les évaluations d'un patient spécifique
     */
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Retourne les étoiles sous forme de HTML
     */
    public function getStarsHtmlAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->note) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $stars;
    }

    /**
     * Calcule la moyenne des notes pour un professionnel
     */
    public static function averageRatingForProfessional($userId, $type = null)
    {
        $query = static::where('evaluated_user_id', $userId);
        if ($type) {
            $query->where('type_evaluation', $type);
        }
        $average = $query->avg('note');
        return $average ? round($average, 1) : 0;
    }

    /**
     * Compte le nombre d'évaluations pour un professionnel
     */
    public static function countForProfessional($userId, $type = null)
    {
        $query = static::where('evaluated_user_id', $userId);
        if ($type) {
            $query->where('type_evaluation', $type);
        }
        return $query->count();
    }
}

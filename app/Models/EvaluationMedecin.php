<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EvaluationMedecin extends Model
{
    use HasFactory;

    protected $table = 'evaluations_medecin';

    protected $fillable = [
        'patient_id',
        'medecin_id', 
        'consultation_id',
        'note_competence',
        'note_communication',
        'note_ponctualite',
        'note_ecoute',
        'note_disponibilite',
        'note_globale',
        'commentaire_positif',
        'commentaire_amelioration',
        'commentaire_general',
        'recommande_medecin',
        'niveau_satisfaction',
        'statut',
        'visible_publiquement',
        'date_evaluation',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'note_competence' => 'decimal:1',
        'note_communication' => 'decimal:1',
        'note_ponctualite' => 'decimal:1',
        'note_ecoute' => 'decimal:1',
        'note_disponibilite' => 'decimal:1',
        'note_globale' => 'decimal:1',
        'recommande_medecin' => 'boolean',
        'visible_publiquement' => 'boolean',
        'date_evaluation' => 'datetime'
    ];

    /**
     * Relations
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultations::class, 'consultation_id');
    }

    /**
     * Accesseurs et Mutators
     */
    
    // Calculer automatiquement la note globale
    public function calculerNoteGlobale(): float
    {
        $notes = [
            $this->note_competence,
            $this->note_communication,
            $this->note_ponctualite,
            $this->note_ecoute,
            $this->note_disponibilite
        ];

        $moyenneNotes = array_sum($notes) / count($notes);
        
        return round($moyenneNotes, 1);
    }

    // Mettre à jour la note globale avant sauvegarde
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $evaluation->note_globale = $evaluation->calculerNoteGlobale();
        });
    }

    /**
     * Scopes
     */
    public function scopeVisibles($query)
    {
        return $query->where('visible_publiquement', true)
                    ->where('statut', 'validee');
    }

    public function scopePourMedecin($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId);
    }

    public function scopeRecentes($query, $jours = 30)
    {
        return $query->where('date_evaluation', '>=', Carbon::now()->subDays($jours));
    }

    public function scopeParNoteMinimum($query, $noteMin)
    {
        return $query->where('note_globale', '>=', $noteMin);
    }

    /**
     * Méthodes utilitaires
     */
    
    // Obtenir le niveau de satisfaction en français
    public function getNiveauSatisfactionFrAttribute()
    {
        $traductions = [
            'très_insatisfait' => 'Très insatisfait',
            'insatisfait' => 'Insatisfait', 
            'neutre' => 'Neutre',
            'satisfait' => 'Satisfait',
            'très_satisfait' => 'Très satisfait'
        ];

        return $traductions[$this->niveau_satisfaction] ?? $this->niveau_satisfaction;
    }

    // Obtenir le statut en français
    public function getStatutFrAttribute()
    {
        $traductions = [
            'en_attente' => 'En attente',
            'soumise' => 'Soumise',
            'validee' => 'Validée',
            'archivee' => 'Archivée'
        ];

        return $traductions[$this->statut] ?? $this->statut;
    }

    // Obtenir la note globale avec étoiles
    public function getNoteAvecEtoilesAttribute()
    {
        $noteEntiere = floor($this->note_globale);
        $etoiles = str_repeat('★', $noteEntiere);
        
        if ($this->note_globale - $noteEntiere >= 0.5) {
            $etoiles .= '☆';
            $noteEntiere++;
        }
        
        $etoiles .= str_repeat('☆', 5 - $noteEntiere);
        
        return $etoiles . ' (' . $this->note_globale . '/5)';
    }

    // Vérifier si l'évaluation peut être modifiée
    public function peutEtreModifiee(): bool
    {
        return in_array($this->statut, ['en_attente', 'soumise']) 
               && $this->date_evaluation->diffInDays(now()) <= 7;
    }

    // Obtenir les points forts (notes >= 4)
    public function getPointsForts(): array
    {
        $aspects = [
            'note_competence' => 'Compétence médicale',
            'note_communication' => 'Communication',
            'note_ponctualite' => 'Ponctualité',
            'note_ecoute' => 'Qualité d\'écoute',
            'note_disponibilite' => 'Disponibilité'
        ];

        $pointsForts = [];
        foreach ($aspects as $attribut => $nom) {
            if ($this->$attribut >= 4.0) {
                $pointsForts[] = [
                    'aspect' => $nom,
                    'note' => $this->$attribut
                ];
            }
        }

        return $pointsForts;
    }

    // Obtenir les points à améliorer (notes < 3)
    public function getPointsAmeliorer(): array
    {
        $aspects = [
            'note_competence' => 'Compétence médicale',
            'note_communication' => 'Communication', 
            'note_ponctualite' => 'Ponctualité',
            'note_ecoute' => 'Qualité d\'écoute',
            'note_disponibilite' => 'Disponibilité'
        ];

        $pointsAmeliorer = [];
        foreach ($aspects as $attribut => $nom) {
            if ($this->$attribut < 3.0) {
                $pointsAmeliorer[] = [
                    'aspect' => $nom,
                    'note' => $this->$attribut
                ];
            }
        }

        return $pointsAmeliorer;
    }

    /**
     * Méthodes statiques utilitaires
     */
    
    // Calculer la note moyenne d'un médecin
    public static function noteMoyenneMedecin($medecinId): ?float
    {
        $moyenne = self::visibles()
                      ->pourMedecin($medecinId)
                      ->avg('note_globale');

        return $moyenne ? round($moyenne, 1) : null;
    }

    // Obtenir le nombre total d'évaluations d'un médecin
    public static function nombreEvaluationsMedecin($medecinId): int
    {
        return self::visibles()->pourMedecin($medecinId)->count();
    }

    // Obtenir le pourcentage de recommandations d'un médecin
    public static function pourcentageRecommandationsMedecin($medecinId): ?float
    {
        $total = self::visibles()->pourMedecin($medecinId)->count();
        
        if ($total == 0) return null;
        
        $recommandations = self::visibles()
                              ->pourMedecin($medecinId)
                              ->where('recommande_medecin', true)
                              ->count();

        return round(($recommandations / $total) * 100, 1);
    }

    // Obtenir les statistiques détaillées d'un médecin
    public static function statistiquesMedecin($medecinId): array
    {
        $evaluations = self::visibles()->pourMedecin($medecinId);
        
        if ($evaluations->count() == 0) {
            return [
                'nombre_evaluations' => 0,
                'note_moyenne' => null,
                'pourcentage_recommandations' => null,
                'repartition_notes' => [],
                'aspects' => []
            ];
        }

        $stats = [
            'nombre_evaluations' => $evaluations->count(),
            'note_moyenne' => round($evaluations->avg('note_globale'), 1),
            'pourcentage_recommandations' => self::pourcentageRecommandationsMedecin($medecinId),
            'repartition_notes' => [
                '5_etoiles' => $evaluations->where('note_globale', '>=', 4.5)->count(),
                '4_etoiles' => $evaluations->whereBetween('note_globale', [3.5, 4.4])->count(),
                '3_etoiles' => $evaluations->whereBetween('note_globale', [2.5, 3.4])->count(),
                '2_etoiles' => $evaluations->whereBetween('note_globale', [1.5, 2.4])->count(),
                '1_etoile' => $evaluations->where('note_globale', '<', 1.5)->count(),
            ],
            'aspects' => [
                'competence' => round($evaluations->avg('note_competence'), 1),
                'communication' => round($evaluations->avg('note_communication'), 1),
                'ponctualite' => round($evaluations->avg('note_ponctualite'), 1),
                'ecoute' => round($evaluations->avg('note_ecoute'), 1),
                'disponibilite' => round($evaluations->avg('note_disponibilite'), 1),
            ]
        ];

        return $stats;
    }
}
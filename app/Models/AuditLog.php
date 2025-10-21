<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'event_type',
        'severity',
        'auditable_type',
        'auditable_id',
        'changes',
        'metadata',
        'ip_address',
        'user_agent',
        'expires_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les types d'événements
    public const EVENT_TYPES = [
        'create' => 'Création',
        'update' => 'Modification',
        'delete' => 'Suppression',
        'login' => 'Connexion',
        'logout' => 'Déconnexion',
        'view' => 'Consultation',
        'export' => 'Export',
        'import' => 'Import',
        'payment' => 'Paiement',
        'backup' => 'Sauvegarde',
        'restore' => 'Restauration',
    ];

    // Constantes pour les niveaux de sévérité
    public const SEVERITIES = [
        'low' => 'Faible',
        'medium' => 'Moyen',
        'high' => 'Elevé',
        'critical' => 'Critique',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique avec l'entité auditée
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope pour filtrer par type d'événement
     */
    public function scopeEventType(Builder $query, string $eventType): Builder
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope pour filtrer par sévérité
     */
    public function scopeSeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeInPeriod(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope pour filtrer par IP
     */
    public function scopeFromIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope pour les logs récents
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Scope pour les logs expirés
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Scope pour optimiser les requêtes avec jointures
     */
    public function scopeWithUser(Builder $query): Builder
    {
        return $query->with(['user:id,name,email']);
    }

    /**
     * Méthode statique pour créer un log d'audit optimisé
     */
    public static function createLog(
        string $action,
        string $eventType,
        ?Model $auditable = null,
        ?array $changes = null,
        string $severity = 'low',
        ?array $metadata = null,
        ?int $expiresInDays = null
    ): self {
        $data = [
            'user_id' => auth()->id(),
            'action' => $action,
            'event_type' => $eventType,
            'severity' => $severity,
            'changes' => $changes,
            'metadata' => array_merge($metadata ?? [], [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($auditable) {
            $data['auditable_type'] = get_class($auditable);
            $data['auditable_id'] = $auditable->getKey();
        }

        if ($expiresInDays) {
            $data['expires_at'] = Carbon::now()->addDays($expiresInDays);
        }

        return self::create($data);
    }

    /**
     * Obtenir le libellé du type d'événement
     */
    public function getEventTypeLabel(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }

    /**
     * Obtenir le libellé de la sévérité
     */
    public function getSeverityLabel(): string
    {
        return self::SEVERITIES[$this->severity] ?? $this->severity;
    }

    /**
     * Vérifier si le log est critique
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Obtenir les différences de manière formatée
     */
    public function getFormattedChanges(): array
    {
        if (!$this->changes || !is_array($this->changes)) {
            return [];
        }

        $formatted = [];
        foreach ($this->changes as $field => $change) {
            if (is_array($change) && isset($change['old'], $change['new'])) {
                $formatted[$field] = [
                    'label' => ucfirst(str_replace('_', ' ', $field)),
                    'old' => $change['old'],
                    'new' => $change['new'],
                    'changed' => $change['old'] !== $change['new'],
                ];
            }
        }

        return $formatted;
    }

    /**
     * Nettoyer automatiquement les logs expirés
     */
    public static function cleanupExpired(): int
    {
        return self::expired()->delete();
    }
}

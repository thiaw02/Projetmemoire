<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot le trait Auditable
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->auditAction('create', 'create', 'medium');
        });

        static::updated(function (Model $model) {
            if ($model->wasChanged()) {
                $changes = $model->getAuditableChanges();
                $severity = $model->determineUpdateSeverity($changes);
                $model->auditAction('update', 'update', $severity, $changes);
            }
        });

        static::deleted(function (Model $model) {
            // Vérifier si le modèle utilise SoftDeletes avant d'appeler isForceDeleting()
            $severity = 'high'; // Valeur par défaut
            
            if (method_exists($model, 'isForceDeleting')) {
                $severity = $model->isForceDeleting() ? 'critical' : 'high';
            }
            
            $model->auditAction('delete', 'delete', $severity);
        });
    }

    /**
     * Créer un log d'audit pour cette instance de modèle
     */
    public function auditAction(
        string $action,
        string $eventType,
        string $severity = 'low',
        ?array $changes = null,
        ?array $metadata = null
    ): AuditLog {
        return AuditLog::createLog(
            $this->getAuditActionName($action),
            $eventType,
            $this,
            $changes,
            $severity,
            array_merge($metadata ?? [], $this->getAuditMetadata()),
            $this->getAuditExpirationDays($severity)
        );
    }

    /**
     * Obtenir les changements auditables
     */
    public function getAuditableChanges(): array
    {
        $changes = [];
        $dirty = $this->getDirty();
        $original = $this->getOriginal();

        foreach ($dirty as $key => $newValue) {
            // Exclure les champs sensibles en utilisant une approche défensive
            $excludes = method_exists($this, 'getAuditableExcludes') 
                ? $this->getAuditableExcludes() 
                : $this->getDefaultAuditableExcludes();
                
            if (in_array($key, $excludes)) {
                continue;
            }

            $oldValue = $original[$key] ?? null;

            // Masquer les mots de passe
            if (str_contains($key, 'password')) {
                $changes[$key] = [
                    'old' => $oldValue ? '***masked***' : null,
                    'new' => $newValue ? '***masked***' : null,
                ];
            } else {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Déterminer la sévérité d'une mise à jour
     */
    public function determineUpdateSeverity(array $changes): string
    {
        // Champs critiques qui augmentent la sévérité
        $criticalFields = $this->getAuditableCriticalFields();
        $importantFields = $this->getAuditableImportantFields();

        foreach ($changes as $field => $change) {
            if (in_array($field, $criticalFields)) {
                return 'critical';
            }
            if (in_array($field, $importantFields)) {
                return 'high';
            }
        }

        // Vérifier le nombre de changements
        if (count($changes) > 5) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Obtenir le nom d'action pour l'audit
     */
    protected function getAuditActionName(string $action): string
    {
        $modelName = class_basename(static::class);
        $actionLabels = [
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
        ];

        return ($actionLabels[$action] ?? ucfirst($action)) . ' ' . strtolower($modelName);
    }

    /**
     * Obtenir les métadonnées d'audit spécifiques au modèle
     */
    protected function getAuditMetadata(): array
    {
        $metadata = [
            'model_class' => static::class,
            'model_id' => $this->getKey(),
        ];

        // Ajouter des informations contextuelles si disponibles
        if (method_exists($this, 'getAuditContextMetadata')) {
            $metadata = array_merge($metadata, $this->getAuditContextMetadata());
        }

        return $metadata;
    }

    /**
     * Obtenir la durée d'expiration selon la sévérité
     */
    protected function getAuditExpirationDays(string $severity): int
    {
        return match ($severity) {
            'critical' => 730, // 2 ans pour les actions critiques
            'high' => 365,     // 1 an
            'medium' => 180,   // 6 mois
            'low' => 90,       // 3 mois
            default => 90,
        };
    }

    /**
     * Champs par défaut à exclure de l'audit
     */
    protected function getDefaultAuditableExcludes(): array
    {
        return [
            'updated_at',
            'created_at',
            'deleted_at',
            'remember_token',
            'email_verified_at',
        ];
    }
    
    /**
     * Champs à exclure de l'audit (à override dans le modèle)
     * Cette méthode peut être surchargée dans les modèles
     */
    protected function getAuditableExcludes(): array
    {
        return $this->getDefaultAuditableExcludes();
    }

    /**
     * Champs critiques (à override dans le modèle)
     */
    protected function getAuditableCriticalFields(): array
    {
        return [
            'password',
            'email',
            'status',
            'role_id',
            'permissions',
        ];
    }

    /**
     * Champs importants (à override dans le modèle)
     */
    protected function getAuditableImportantFields(): array
    {
        return [
            'name',
            'phone',
            'address',
            'price',
            'amount',
        ];
    }

    /**
     * Vérifier si un modèle a des logs d'audit
     */
    public function hasAuditLogs(): bool
    {
        return $this->auditLogs()->exists();
    }

    /**
     * Relation avec les logs d'audit
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }

    /**
     * Obtenir les logs d'audit récents
     */
    public function getRecentAuditLogs(int $limit = 10)
    {
        return $this->auditLogs()
            ->withUser()
            ->take($limit)
            ->get();
    }

    /**
     * Obtenir les logs d'audit par type
     */
    public function getAuditLogsByEvent(string $eventType)
    {
        return $this->auditLogs()
            ->eventType($eventType)
            ->withUser()
            ->get();
    }

    /**
     * Audit manuel d'une action personnalisée
     */
    public function auditCustomAction(
        string $actionName,
        string $eventType = 'update',
        string $severity = 'low',
        ?array $metadata = null
    ): AuditLog {
        return $this->auditAction($actionName, $eventType, $severity, null, $metadata);
    }
}
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activation de l'audit
    |--------------------------------------------------------------------------
    |
    | Détermine si le système d'audit est activé globalement.
    |
    */

    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Modèles auditables
    |--------------------------------------------------------------------------
    |
    | Liste des modèles qui utilisent le trait Auditable et doivent être
    | automatiquement audités.
    |
    */

    'auditable_models' => [
        \App\Models\User::class,
        \App\Models\Patient::class,
        // Ajoutez d'autres modèles ici
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes sensibles
    |--------------------------------------------------------------------------
    |
    | Patterns des routes qui doivent être automatiquement auditées
    | par le middleware AuditMiddleware.
    |
    */

    'sensitive_routes' => [
        'admin.*',
        'audit.*',
        'users.destroy',
        'users.store',
        'users.update',
        'patients.*',
        'consultations.*',
        'ordonnances.*',
        'payments.*',
        'backup.*',
        'export.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Méthodes HTTP auditables
    |--------------------------------------------------------------------------
    |
    | Méthodes HTTP qui doivent déclencher un audit.
    |
    */

    'auditable_methods' => [
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
    ],

    /*
    |--------------------------------------------------------------------------
    | Durées de rétention par sévérité
    |--------------------------------------------------------------------------
    |
    | Nombre de jours de conservation des logs selon leur niveau de sévérité.
    |
    */

    'retention_days' => [
        'critical' => env('AUDIT_RETENTION_CRITICAL', 730), // 2 ans
        'high' => env('AUDIT_RETENTION_HIGH', 365),         // 1 an
        'medium' => env('AUDIT_RETENTION_MEDIUM', 180),     // 6 mois
        'low' => env('AUDIT_RETENTION_LOW', 90),            // 3 mois
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration du cache
    |--------------------------------------------------------------------------
    |
    | Durée de mise en cache des statistiques d'audit (en minutes).
    |
    */

    'cache' => [
        'statistics_ttl' => env('AUDIT_CACHE_TTL', 15), // 15 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Détection d'activités suspectes
    |--------------------------------------------------------------------------
    |
    | Seuils pour détecter les activités anormales.
    |
    */

    'suspicious_activity' => [
        'max_actions_per_hour' => env('AUDIT_MAX_ACTIONS_HOUR', 100),
        'max_critical_actions_2h' => env('AUDIT_MAX_CRITICAL_2H', 5),
        'max_failed_logins' => env('AUDIT_MAX_FAILED_LOGINS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nettoyage automatique
    |--------------------------------------------------------------------------
    |
    | Configuration du nettoyage automatique des anciens logs.
    |
    */

    'auto_cleanup' => [
        'enabled' => env('AUDIT_AUTO_CLEANUP', true),
        'schedule' => 'daily', // daily, weekly, monthly
        'batch_size' => env('AUDIT_CLEANUP_BATCH_SIZE', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Export et archivage
    |--------------------------------------------------------------------------
    |
    | Configuration des options d'export et d'archivage.
    |
    */

    'export' => [
        'max_records' => env('AUDIT_EXPORT_MAX', 10000),
        'formats' => ['csv', 'xlsx', 'json'],
        'compression' => env('AUDIT_EXPORT_COMPRESSION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclusions globales
    |--------------------------------------------------------------------------
    |
    | Champs à exclure automatiquement de tous les audits.
    |
    */

    'global_excludes' => [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Champs sensibles à masquer
    |--------------------------------------------------------------------------
    |
    | Champs contenant des données sensibles qui doivent être masqués
    | dans les logs d'audit.
    |
    */

    'masked_fields' => [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'api_key',
        'credit_card',
        'ssn',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications d'audit
    |--------------------------------------------------------------------------
    |
    | Configuration des notifications pour les événements critiques.
    |
    */

    'notifications' => [
        'enabled' => env('AUDIT_NOTIFICATIONS', true),
        'channels' => ['mail', 'database'],
        'critical_events' => [
            'user_deleted',
            'mass_data_export',
            'permission_escalation',
            'suspicious_activity',
        ],
        'recipients' => [
            env('AUDIT_ADMIN_EMAIL', 'admin@example.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Intégrations externes
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration avec des services externes de monitoring.
    |
    */

    'integrations' => [
        'syslog' => [
            'enabled' => env('AUDIT_SYSLOG_ENABLED', false),
            'facility' => LOG_USER,
            'level' => LOG_INFO,
        ],
        'elasticsearch' => [
            'enabled' => env('AUDIT_ELASTICSEARCH_ENABLED', false),
            'hosts' => env('ELASTICSEARCH_HOSTS', 'localhost:9200'),
            'index' => env('AUDIT_ELASTICSEARCH_INDEX', 'audit_logs'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    |
    | Paramètres de performance pour optimiser le système d'audit.
    |
    */

    'performance' => [
        'async_logging' => env('AUDIT_ASYNC_LOGGING', true),
        'batch_insert' => env('AUDIT_BATCH_INSERT', true),
        'batch_size' => env('AUDIT_BATCH_SIZE', 100),
        'queue_connection' => env('AUDIT_QUEUE_CONNECTION', 'database'),
    ],

];
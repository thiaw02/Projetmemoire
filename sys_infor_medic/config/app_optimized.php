<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration d'optimisation de l'application
    |--------------------------------------------------------------------------
    */

    'cache_ttl' => [
        'dashboard' => env('CACHE_TTL_DASHBOARD', 600), // 10 minutes
        'user_data' => env('CACHE_TTL_USER_DATA', 300), // 5 minutes
        'stats' => env('CACHE_TTL_STATS', 1800), // 30 minutes
        'performance' => env('CACHE_TTL_PERFORMANCE', 180), // 3 minutes
    ],

    'pagination' => [
        'default' => 15,
        'admin' => 25,
        'large_datasets' => 50,
    ],

    'performance' => [
        'enable_monitoring' => env('ENABLE_PERFORMANCE_MONITORING', true),
        'slow_query_threshold' => 100, // ms
        'slow_request_threshold' => 2000, // ms
        'enable_compression' => env('ENABLE_HTTP_COMPRESSION', true),
        'compression_min_size' => 1024, // bytes
    ],

    'security' => [
        'enable_audit_logging' => env('ENABLE_AUDIT_LOGGING', true),
        'sanitize_inputs' => env('SANITIZE_INPUTS', true),
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],

    'features' => [
        'payments' => env('ENABLE_PAYMENTS', true),
        'chat' => env('ENABLE_CHAT', false),
        'analytics' => env('ENABLE_ANALYTICS', true),
    ],

    'cleanup' => [
        'old_logs_days' => 30,
        'old_audit_days' => 90,
        'cache_cleanup_schedule' => '0 2 * * *', // Tous les jours à 2h
    ],
];
@extends('admin.layouts.app')

@section('title', 'Monitoring des Performances')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="fas fa-tachometer-alt me-2"></i>
                Monitoring des Performances
            </h1>
        </div>
    </div>

    <!-- Alertes de performance -->
    @if($stats1h['slow_requests_percentage'] > 20)
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Alerte Performance !</strong> 
            {{ $stats1h['slow_requests_percentage'] }}% des requêtes sont lentes (>2s) dans la dernière heure.
        </div>
    @elseif($stats1h['slow_requests_percentage'] > 10)
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Attention !</strong> 
            {{ $stats1h['slow_requests_percentage'] }}% des requêtes sont lentes (>2s) dans la dernière heure.
        </div>
    @endif

    <!-- Métriques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Temps de réponse moyen</h6>
                            <h3 class="mb-0">{{ $stats1h['avg_execution_time'] }}ms</h3>
                            <small>Dernière heure</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Requêtes totales</h6>
                            <h3 class="mb-0">{{ number_format($stats1h['total_requests']) }}</h3>
                            <small>Dernière heure</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card {{ $stats1h['slow_requests_percentage'] > 15 ? 'bg-danger' : ($stats1h['slow_requests_percentage'] > 5 ? 'bg-warning' : 'bg-success') }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Requêtes lentes</h6>
                            <h3 class="mb-0">{{ $stats1h['slow_requests_percentage'] }}%</h3>
                            <small>{{ $stats1h['slow_requests'] }} / {{ $stats1h['total_requests'] }}</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Requêtes DB moyennes</h6>
                            <h3 class="mb-0">{{ $stats1h['avg_queries'] }}</h3>
                            <small>Par requête</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-database fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et statistiques -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Top des Routes Lentes
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($stats1h['top_slow_routes']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Route</th>
                                        <th class="text-end">Temps moyen (ms)</th>
                                        <th class="text-end">Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats1h['top_slow_routes'] as $route => $avgTime)
                                        <tr>
                                            <td><code>{{ $route }}</code></td>
                                            <td class="text-end">
                                                <span class="badge {{ $avgTime > 2000 ? 'bg-danger' : ($avgTime > 1000 ? 'bg-warning' : 'bg-success') }}">
                                                    {{ number_format($avgTime, 1) }}ms
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($avgTime > 2000)
                                                    <span class="text-danger">Critique</span>
                                                @elseif($avgTime > 1000)
                                                    <span class="text-warning">Lent</span>
                                                @else
                                                    <span class="text-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="text-muted">Aucune donnée de performance disponible pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-server me-2"></i>
                        État du Système
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Cache Status -->
                    <div class="mb-3">
                        <h6 class="mb-1">Cache</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">{{ $cacheStatus['driver'] }}</span>
                            <span class="badge {{ $cacheStatus['status'] === 'OK' ? 'bg-success' : 'bg-danger' }}">
                                {{ $cacheStatus['status'] }}
                            </span>
                        </div>
                        @if($cacheStatus['status'] === 'OK')
                            <small class="text-muted">
                                Écriture: {{ $cacheStatus['write_time'] }} | 
                                Lecture: {{ $cacheStatus['read_time'] }}
                            </small>
                        @endif
                    </div>

                    <!-- Database Status -->
                    <div class="mb-3">
                        <h6 class="mb-1">Base de données</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">{{ $dbStatus['driver'] ?? 'MySQL' }}</span>
                            <span class="badge {{ $dbStatus['status'] === 'OK' ? 'bg-success' : 'bg-danger' }}">
                                {{ $dbStatus['status'] }}
                            </span>
                        </div>
                        @if($dbStatus['status'] === 'OK')
                            <small class="text-muted">
                                Réponse: {{ $dbStatus['response_time'] }}
                                @if(isset($dbStatus['connections']))
                                    | Connexions: {{ $dbStatus['connections'] }}
                                @endif
                            </small>
                        @endif
                    </div>

                    <!-- Memory Usage -->
                    <div class="mb-3">
                        <h6 class="mb-1">Mémoire PHP</h6>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Actuelle</span>
                            <span>{{ $systemMetrics['memory_usage']['current'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Pic</span>
                            <span>{{ $systemMetrics['memory_usage']['peak'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Limite</span>
                            <span>{{ $systemMetrics['memory_usage']['limit'] }}</span>
                        </div>
                    </div>

                    <!-- Version Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">Versions</h6>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">PHP</span>
                            <span>{{ $systemMetrics['php_version'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Laravel</span>
                            <span>{{ $systemMetrics['laravel_version'] }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="refreshStats">
                            <i class="fas fa-sync-alt me-1"></i>
                            Actualiser
                        </button>
                        <button class="btn btn-outline-warning btn-sm" id="clearCache">
                            <i class="fas fa-trash me-1"></i>
                            Vider le cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaison 1h vs 24h -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Comparaison des performances
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Dernière heure</h6>
                            <ul class="list-unstyled">
                                <li><strong>Temps de réponse :</strong> {{ $stats1h['avg_execution_time'] }}ms</li>
                                <li><strong>Total requêtes :</strong> {{ number_format($stats1h['total_requests']) }}</li>
                                <li><strong>Requêtes lentes :</strong> {{ $stats1h['slow_requests_percentage'] }}%</li>
                                <li><strong>Requêtes DB :</strong> {{ $stats1h['avg_queries'] }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">24 dernières heures</h6>
                            <ul class="list-unstyled">
                                <li><strong>Temps de réponse :</strong> {{ $stats24h['avg_execution_time'] }}ms</li>
                                <li><strong>Total requêtes :</strong> {{ number_format($stats24h['total_requests']) }}</li>
                                <li><strong>Requêtes lentes :</strong> {{ $stats24h['slow_requests_percentage'] }}%</li>
                                <li><strong>Requêtes DB :</strong> {{ $stats24h['avg_queries'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshStats');
    const clearCacheBtn = document.getElementById('clearCache');

    // Actualisation automatique toutes les 30 secondes
    setInterval(() => {
        location.reload();
    }, 30000);

    // Bouton d'actualisation manuelle
    refreshBtn?.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualisation...';
        location.reload();
    });

    // Bouton de nettoyage du cache
    clearCacheBtn?.addEventListener('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir vider le cache ?')) {
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Nettoyage...';

        fetch('/admin/performance/clear-cache', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache vidé avec succès !');
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            alert('Erreur de connexion : ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-trash me-1"></i>Vider le cache';
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.opacity-75 {
    opacity: 0.75;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

code {
    color: #e83e8c;
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 87.5%;
}
</style>
@endpush
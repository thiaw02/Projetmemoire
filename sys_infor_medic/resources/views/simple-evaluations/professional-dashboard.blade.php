@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    @php
                        $dashboardRoute = auth()->user()->role === 'medecin' ? 'medecin.dashboard' : 'infirmier.dashboard';
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-secondary me-3" title="Retour au dashboard">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Retour</span>
                    </a>
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>
                            Mes Évaluations
                        </h1>
                        <p class="text-muted mb-0">Votre réputation et les retours des patients</p>
                    </div>
                </div>
                <div>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            <!-- Statistiques principales -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-star-fill display-4 mb-3"></i>
                            <h2 class="display-5 fw-bold">{{ number_format($stats['average_rating'], 1) }}</h2>
                            <p class="mb-0">Note Moyenne</p>
                            <small class="opacity-75">sur 5 étoiles</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-heart-fill display-4 mb-3"></i>
                            <h2 class="display-5 fw-bold">{{ $stats['total_count'] }}</h2>
                            <p class="mb-0">Évaluations</p>
                            <small class="opacity-75">au total</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-month-fill display-4 mb-3"></i>
                            <h2 class="display-5 fw-bold">{{ $stats['this_month'] }}</h2>
                            <p class="mb-0">Ce Mois</p>
                            <small class="opacity-75">nouvelles évaluations</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-trophy-fill display-4 mb-3"></i>
                            <h2 class="display-5 fw-bold">{{ $stats['rank'] }}</h2>
                            <p class="mb-0">Classement</p>
                            <small class="opacity-75">parmi les {{ $stats['total_professionals'] }} professionnels</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique de répartition des notes -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Répartition des Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="ratingsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pie-chart me-2"></i>
                                Évolution
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($stats['evolution'] > 0)
                                <div class="text-success text-center">
                                    <i class="bi bi-arrow-up-circle-fill display-4"></i>
                                    <h3 class="mt-3">+{{ number_format($stats['evolution'], 1) }}</h3>
                                    <p>Amélioration ce mois</p>
                                </div>
                            @elseif($stats['evolution'] < 0)
                                <div class="text-danger text-center">
                                    <i class="bi bi-arrow-down-circle-fill display-4"></i>
                                    <h3 class="mt-3">{{ number_format($stats['evolution'], 1) }}</h3>
                                    <p>Baisse ce mois</p>
                                </div>
                            @else
                                <div class="text-muted text-center">
                                    <i class="bi bi-dash-circle-fill display-4"></i>
                                    <h3 class="mt-3">Stable</h3>
                                    <p>Pas de changement</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Évaluations récentes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Évaluations Récentes
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="filterEvaluations('all')">
                            Toutes
                        </button>
                        <button class="btn btn-outline-success" onclick="filterEvaluations('positive')">
                            Positives (4-5★)
                        </button>
                        <button class="btn btn-outline-warning" onclick="filterEvaluations('neutral')">
                            Neutres (3★)
                        </button>
                        <button class="btn btn-outline-danger" onclick="filterEvaluations('negative')">
                            Négatives (1-2★)
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($evaluations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Note</th>
                                        <th>Commentaire</th>
                                        <th>Date</th>
                                        <th>Consultation</th>
                                    </tr>
                                </thead>
                                <tbody id="evaluationsTable">
                                    @foreach($evaluations as $evaluation)
                                        <tr data-rating="{{ $evaluation->note }}" class="evaluation-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        {{ substr($evaluation->patient->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $evaluation->patient->name }}</div>
                                                        <small class="text-muted">Patient</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {!! $evaluation->stars_html !!}
                                                    <span class="ms-2 fw-bold">{{ $evaluation->note }}/5</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($evaluation->commentaire)
                                                    <div class="comment-preview">
                                                        <span class="text-truncate d-inline-block" style="max-width: 300px;">
                                                            {{ $evaluation->commentaire }}
                                                        </span>
                                                        @if(strlen($evaluation->commentaire) > 100)
                                                            <button class="btn btn-link btn-sm p-0 ms-1" onclick="showFullComment({{ $evaluation->id }})">
                                                                <i class="bi bi-arrows-expand"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">Aucun commentaire</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ $evaluation->created_at->format('d/m/Y') }}<br>
                                                    <span class="text-muted">{{ $evaluation->created_at->diffForHumans() }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($evaluation->consultation)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Consultation
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($evaluations->hasPages())
                            <div class="d-flex justify-content-center p-3">
                                {{ $evaluations->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-star display-1 text-muted"></i>
                            <h3 class="text-muted mt-3">Aucune évaluation</h3>
                            <p class="text-muted">Vous n'avez pas encore reçu d'évaluations des patients.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour commentaire complet -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commentaire complet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commentModalBody">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #3b82f6, #1d4ed8);
}
.bg-gradient-success {
    background: linear-gradient(45deg, #10b981, #059669);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #06b6d4, #0891b2);
}
.bg-gradient-warning {
    background: linear-gradient(45deg, #f59e0b, #d97706);
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.comment-preview {
    max-width: 350px;
}

.evaluation-row.d-none {
    display: none !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de répartition des notes
    const ctx = document.getElementById('ratingsChart').getContext('2d');
    const ratingsData = @json($stats['ratings_breakdown']);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1 étoile', '2 étoiles', '3 étoiles', '4 étoiles', '5 étoiles'],
            datasets: [{
                label: 'Nombre d\'évaluations',
                data: [
                    ratingsData[1] || 0,
                    ratingsData[2] || 0,
                    ratingsData[3] || 0,
                    ratingsData[4] || 0,
                    ratingsData[5] || 0
                ],
                backgroundColor: [
                    '#ef4444',
                    '#f97316',
                    '#eab308',
                    '#22c55e',
                    '#10b981'
                ],
                borderColor: [
                    '#dc2626',
                    '#ea580c',
                    '#ca8a04',
                    '#16a34a',
                    '#059669'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Filtrage des évaluations
function filterEvaluations(type) {
    const rows = document.querySelectorAll('.evaluation-row');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    // Mettre à jour les boutons
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filtrer les lignes
    rows.forEach(row => {
        const rating = parseInt(row.dataset.rating);
        let show = false;
        
        switch(type) {
            case 'all':
                show = true;
                break;
            case 'positive':
                show = rating >= 4;
                break;
            case 'neutral':
                show = rating === 3;
                break;
            case 'negative':
                show = rating <= 2;
                break;
        }
        
        if (show) {
            row.classList.remove('d-none');
        } else {
            row.classList.add('d-none');
        }
    });
}

// Afficher commentaire complet
function showFullComment(evaluationId) {
    // Récupérer le commentaire complet via AJAX
    const evaluation = @json($evaluations->keyBy('id'));
    const comment = evaluation[evaluationId].commentaire;
    const patient = evaluation[evaluationId].patient.name;
    const rating = evaluation[evaluationId].note;
    const date = new Date(evaluation[evaluationId].created_at).toLocaleDateString('fr-FR');
    
    document.getElementById('commentModalBody').innerHTML = `
        <div class="mb-3">
            <h6>Patient: ${patient}</h6>
            <div class="mb-2">
                ${'<i class="bi bi-star-fill text-warning"></i>'.repeat(rating)}
                ${'<i class="bi bi-star text-muted"></i>'.repeat(5-rating)}
                <span class="ms-2">${rating}/5</span>
            </div>
            <small class="text-muted">Évalué le ${date}</small>
        </div>
        <div class="border-start border-3 border-primary ps-3">
            <p class="mb-0">${comment}</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('commentModal'));
    modal.show();
}
</script>
@endsection
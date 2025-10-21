@extends('layouts.app')

@section('head')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="container-fluid px-4">

{{-- Header avec bouton retour --}}
<div class="evaluations-admin-header">
  <div class="header-content">
    <div class="header-title">
      <a href="{{ route('admin.dashboard') }}" class="btn-back" title="Retour au dashboard">
        <i class="bi bi-arrow-left"></i>
      </a>
      <i class="bi bi-star-fill"></i>
      <span>Gestion des Évaluations</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-light btn-sm" onclick="exportEvaluations()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
        <i class="bi bi-download me-1"></i>
        Exporter
      </button>
      <button class="btn btn-light btn-sm" onclick="showAdvancedStatistics()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
        <i class="bi bi-bar-chart me-1"></i>
        Statistiques
      </button>
      <div class="header-badge">
        <i class="bi bi-shield-check"></i>
        <span>{{ Auth::user()->name }}</span>
      </div>
    </div>
  </div>
</div>

            <!-- Répartition des notes - version compacte -->
            <div class="row mb-4">
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-pie-chart me-2"></i>
                                Répartition par Notes
                            </h6>
                        </div>
                        <div class="card-body py-2">
                            <canvas id="ratingDistribution" width="300" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classement des professionnels -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-trophy me-2"></i>
                                Classement des Professionnels
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary active" onclick="sortProfessionals('rating')">
                                    Par Note
                                </button>
                                <button class="btn btn-outline-success" onclick="sortProfessionals('count')">
                                    Par Nombre
                                </button>
                                <button class="btn btn-outline-info" onclick="sortProfessionals('recent')">
                                    Plus Récents
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="professionalsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rang</th>
                                            <th>Professionnel</th>
                                            <th>Note Moyenne</th>
                                            <th>Total Évaluations</th>
                                            <th>Dernière Évaluation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($professionalRanking as $index => $prof)
                                            <tr data-rating="{{ $prof['average_rating'] }}" 
                                                data-count="{{ $prof['total_count'] }}"
                                                data-recent="{{ $prof['last_evaluation_timestamp'] }}">
                                                <td>
                                                    <span class="badge 
                                                        @if($index == 0) bg-warning
                                                        @elseif($index == 1) bg-secondary  
                                                        @elseif($index == 2) bg-info
                                                        @else bg-light text-dark
                                                        @endif
                                                        rank-badge">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-pro bg-{{ $prof['role'] === 'medecin' ? 'primary' : 'success' }} text-white me-2">
                                                            {{ substr($prof['name'], 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">Dr. {{ $prof['name'] }}</div>
                                                            <small class="text-muted">{{ ucfirst($prof['role']) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($prof['average_rating']))
                                                                <i class="bi bi-star-fill text-warning"></i>
                                                            @else
                                                                <i class="bi bi-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                        <span class="ms-2 fw-bold">{{ number_format($prof['average_rating'], 1) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $prof['total_count'] }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $prof['last_evaluation'] ? $prof['last_evaluation']->diffForHumans() : 'Aucune' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-info" 
                                                                onclick="viewProfessionalDetails({{ $prof['id'] }})">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary"
                                                                onclick="showProfessionalEvaluations({{ $prof['id'] }})">
                                                            <i class="bi bi-chat-dots"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                    <div class="d-flex gap-2">
                        <!-- Filtres -->
                        <select class="form-select form-select-sm" id="roleFilter" onchange="applyFilters()">
                            <option value="">Tous les rôles</option>
                            <option value="medecin">Médecins</option>
                            <option value="infirmier">Infirmiers</option>
                        </select>
                        <select class="form-select form-select-sm" id="ratingFilter" onchange="applyFilters()">
                            <option value="">Toutes les notes</option>
                            <option value="5">5 étoiles</option>
                            <option value="4">4 étoiles</option>
                            <option value="3">3 étoiles</option>
                            <option value="2">2 étoiles</option>
                            <option value="1">1 étoile</option>
                        </select>
                        <input type="text" class="form-control form-control-sm" 
                               id="searchFilter" placeholder="Rechercher..." 
                               onkeyup="applyFilters()" style="width: 200px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="evaluationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>Professionnel</th>
                                    <th>Note</th>
                                    <th>Commentaire</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEvaluations as $evaluation)
                                    <tr class="evaluation-row" 
                                        data-role="{{ $evaluation->type_evaluation }}"
                                        data-rating="{{ $evaluation->note }}"
                                        data-search="{{ strtolower($evaluation->patient->name . ' ' . $evaluation->evaluatedUser->name . ' ' . $evaluation->commentaire) }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info text-white me-2">
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
                                                <div class="avatar-sm bg-{{ $evaluation->type_evaluation === 'medecin' ? 'primary' : 'success' }} text-white me-2">
                                                    {{ substr($evaluation->evaluatedUser->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium">Dr. {{ $evaluation->evaluatedUser->name }}</div>
                                                    <small class="text-muted">{{ ucfirst($evaluation->type_evaluation) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {!! $evaluation->stars_html !!}
                                                <span class="ms-2 fw-bold">{{ $evaluation->note }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($evaluation->commentaire)
                                                <div class="comment-cell">
                                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                                        {{ $evaluation->commentaire }}
                                                    </span>
                                                    @if(strlen($evaluation->commentaire) > 50)
                                                        <button class="btn btn-link btn-sm p-0 ms-1" 
                                                                onclick="showFullComment('{{ $evaluation->id }}', '{{ addslashes($evaluation->commentaire) }}', '{{ $evaluation->patient->name }}', {{ $evaluation->note }}, '{{ $evaluation->created_at->format('d/m/Y') }}')">
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
                                                {{ $evaluation->created_at->format('d/m/Y H:i') }}<br>
                                                <span class="text-muted">{{ $evaluation->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" 
                                                        onclick="viewEvaluationDetails({{ $evaluation->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteEvaluation({{ $evaluation->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($recentEvaluations->hasPages())
                        <div class="d-flex justify-content-center p-3">
                            {{ $recentEvaluations->links() }}
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- Modal commentaire complet -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commentaire Complet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commentModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal détails professionnel -->
<div class="modal fade" id="professionalModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Professionnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="professionalModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Optimisation espace plein écran pour dashboard évaluations admin */
.container-fluid {
    max-width: 100% !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

body {
  padding-top: 90px !important;
}

.container.mt-4,
.container {
  margin-top: 0 !important;
  padding-top: 0 !important;
}

.row {
  margin-top: 0 !important;
}

/* Header des évaluations admin */
.evaluations-admin-header {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 16px;
  margin-bottom: 1.5rem;
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.5rem;
  font-weight: 600;
}

.btn-back {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
  color: white;
  transform: translateX(-2px);
}

.header-title i:not(.btn-back i) {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem;
  border-radius: 10px;
  font-size: 1.2rem;
}

.header-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.15);
  padding: 0.5rem 1rem;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.9rem;
}

.avatar-pro {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
}

.rank-badge {
    font-size: 0.9rem;
    padding: 0.4rem 0.6rem;
}

.comment-cell {
    max-width: 300px;
}

.evaluation-row.d-none {
    display: none !important;
}

/* Optimisations plein écran */
.card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.table th, .table td {
    padding: 0.6rem 0.75rem;
    font-size: 0.9rem;
}

@media (min-width: 1400px) {
    .container-fluid {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }
    
    .evaluations-admin-header {
        padding: 1.25rem 2rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de répartition
    const ctx = document.getElementById('ratingDistribution').getContext('2d');
    const distributionData = @json($globalStats['rating_distribution']);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['5 étoiles', '4 étoiles', '3 étoiles', '2 étoiles', '1 étoile'],
            datasets: [{
                data: [
                    distributionData[5] || 0,
                    distributionData[4] || 0,
                    distributionData[3] || 0,
                    distributionData[2] || 0,
                    distributionData[1] || 0
                ],
                backgroundColor: ['#10b981', '#22c55e', '#eab308', '#f97316', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true,
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
});

// Triage des professionnels
function sortProfessionals(type) {
    const table = document.getElementById('professionalsTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Mise à jour boutons
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    rows.sort((a, b) => {
        switch(type) {
            case 'rating':
                return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            case 'count':
                return parseInt(b.dataset.count) - parseInt(a.dataset.count);
            case 'recent':
                return parseInt(b.dataset.recent) - parseInt(a.dataset.recent);
            default:
                return 0;
        }
    });
    
    // Réorganiser et mettre à jour les rangs
    rows.forEach((row, index) => {
        tbody.appendChild(row);
        const rankBadge = row.querySelector('.rank-badge');
        rankBadge.textContent = `#${index + 1}`;
        
        // Mettre à jour les couleurs des rangs
        rankBadge.className = 'badge rank-badge';
        if (index === 0) rankBadge.classList.add('bg-warning');
        else if (index === 1) rankBadge.classList.add('bg-secondary');
        else if (index === 2) rankBadge.classList.add('bg-info');
        else rankBadge.classList.add('bg-light', 'text-dark');
    });
}

// Filtrage des évaluations
function applyFilters() {
    const roleFilter = document.getElementById('roleFilter').value;
    const ratingFilter = document.getElementById('ratingFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    const rows = document.querySelectorAll('.evaluation-row');
    
    rows.forEach(row => {
        const role = row.dataset.role;
        const rating = row.dataset.rating;
        const searchText = row.dataset.search;
        
        let show = true;
        
        if (roleFilter && role !== roleFilter) show = false;
        if (ratingFilter && rating !== ratingFilter) show = false;
        if (searchFilter && !searchText.includes(searchFilter)) show = false;
        
        if (show) {
            row.classList.remove('d-none');
        } else {
            row.classList.add('d-none');
        }
    });
}

// Afficher commentaire complet
function showFullComment(evaluationId, comment, patient, rating, date) {
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

// Voir détails d'un professionnel
function viewProfessionalDetails(professionalId) {
    fetch(`/api/simple-evaluations/professional/${professionalId}/stats`)
        .then(response => response.json())
        .then(data => {
            // Afficher les détails dans la modal
            console.log('Détails professionnel:', data);
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des détails');
        });
}

// Afficher évaluations d'un professionnel
function showProfessionalEvaluations(professionalId) {
    window.location.href = `/simple-evaluations/professional/${professionalId}`;
}

// Voir détails d'une évaluation
function viewEvaluationDetails(evaluationId) {
    window.location.href = `/simple-evaluations/${evaluationId}`;
}

// Supprimer une évaluation
function deleteEvaluation(evaluationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')) {
        fetch(`/admin/evaluations/${evaluationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

// Exporter les données en CSV
function exportEvaluations() {
    // Créer les données CSV à partir du tableau actuel
    const table = document.getElementById('evaluationsTable');
    const rows = table.querySelectorAll('tbody tr:not(.d-none)');
    
    let csv = 'Patient,Professionnel,Rôle,Note,Commentaire,Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const patient = cells[0].querySelector('.fw-medium').textContent.trim();
        const professional = cells[1].querySelector('.fw-medium').textContent.trim();
        const role = cells[1].querySelector('.text-muted').textContent.trim();
        const rating = cells[2].querySelector('.fw-bold').textContent.trim();
        const comment = cells[3].textContent.trim().replace(/,/g, ';').replace(/\n/g, ' ');
        const date = cells[4].textContent.trim().split('\n')[0];
        
        csv += `"${patient}","${professional}","${role}","${rating}","${comment}","${date}"\n`;
    });
    
    // Télécharger le fichier CSV
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `evaluations_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Notification de succès
    showNotification('Évaluations exportées avec succès!', 'success');
}

// Afficher statistiques avancées
function showAdvancedStatistics() {
    const modalHtml = `
        <div class="modal fade" id="statisticsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title"><i class="bi bi-graph-up-arrow me-2"></i>Statistiques Avancées</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <!-- KPIs détaillés -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ number_format($globalStats['average_rating'], 2) }}</h4>
                                                <p class="mb-0">Note Moyenne Exacte</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ number_format(($globalStats['rating_distribution'][5] ?? 0) / max($globalStats['total_evaluations'], 1) * 100, 1) }}%</h4>
                                                <p class="mb-0">Taux d'Évaluations 5★</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ number_format($globalStats['total_evaluations'] / max($globalStats['evaluated_professionals'], 1), 1) }}</h4>
                                                <p class="mb-0">Éval./Professionnel</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ number_format(($globalStats['rating_distribution'][1] ?? 0) + ($globalStats['rating_distribution'][2] ?? 0)) }}</h4>
                                                <p class="mb-0">Évaluations < 3★</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Graphiques détaillés -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-calendar-month me-1"></i>Évolution Mensuelle</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="monthlyTrendChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-people me-1"></i>Top 10 Professionnels</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="topProfessionalsChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tableau comparatif par rôle -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-bar-chart me-1"></i>Comparaison par Rôle</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Médecins</h6>
                                                <div class="d-flex justify-content-between">
                                                    <span>Note moyenne:</span>
                                                    <strong id="medecinAverage">-</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Total évaluations:</span>
                                                    <strong id="medecinCount">-</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-success">Infirmiers</h6>
                                                <div class="d-flex justify-content-between">
                                                    <span>Note moyenne:</span>
                                                    <strong id="infirmierAverage">-</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Total évaluations:</span>
                                                    <strong id="infirmierCount">-</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="exportAdvancedStats()">Exporter Stats</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au DOM s'il n'existe pas déjà
    if (!document.getElementById('statisticsModal')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    // Afficher la modal
    const modal = new bootstrap.Modal(document.getElementById('statisticsModal'));
    modal.show();
    
    // Calculer et afficher les statistiques par rôle
    calculateRoleStatistics();
    
    // Générer les graphiques avancés
    setTimeout(() => {
        generateAdvancedCharts();
    }, 500);
}

// Calculer les statistiques par rôle
function calculateRoleStatistics() {
    const professionalRows = document.querySelectorAll('#professionalsTable tbody tr');
    let medecinTotal = 0, medecinCount = 0, infirmierTotal = 0, infirmierCount = 0;
    
    professionalRows.forEach(row => {
        const role = row.querySelector('.text-muted').textContent.trim().toLowerCase();
        const rating = parseFloat(row.dataset.rating);
        
        if (role === 'medecin' && !isNaN(rating)) {
            medecinTotal += rating;
            medecinCount++;
        } else if (role === 'infirmier' && !isNaN(rating)) {
            infirmierTotal += rating;
            infirmierCount++;
        }
    });
    
    document.getElementById('medecinAverage').textContent = medecinCount > 0 ? (medecinTotal / medecinCount).toFixed(1) + '/5' : 'N/A';
    document.getElementById('medecinCount').textContent = medecinCount;
    document.getElementById('infirmierAverage').textContent = infirmierCount > 0 ? (infirmierTotal / infirmierCount).toFixed(1) + '/5' : 'N/A';
    document.getElementById('infirmierCount').textContent = infirmierCount;
}

// Générer les graphiques avancés
function generateAdvancedCharts() {
    // Graphique d'évolution mensuelle (simulation)
    const monthlyCtx = document.getElementById('monthlyTrendChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Évaluations',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    
    // Graphique top professionnels
    const topCtx = document.getElementById('topProfessionalsChart')?.getContext('2d');
    if (topCtx) {
        const topProfData = Array.from(document.querySelectorAll('#professionalsTable tbody tr'))
            .slice(0, 10)
            .map(row => ({
                name: row.querySelector('.fw-medium').textContent.trim().split(' ').pop(),
                rating: parseFloat(row.dataset.rating) || 0
            }));
        
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: topProfData.map(p => p.name),
                datasets: [{
                    label: 'Note moyenne',
                    data: topProfData.map(p => p.rating),
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 5 }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

// Exporter les statistiques avancées
function exportAdvancedStats() {
    const statsData = {
        date: new Date().toISOString().split('T')[0],
        note_moyenne_globale: {{ number_format($globalStats['average_rating'], 2) }},
        total_evaluations: {{ $globalStats['total_evaluations'] }},
        professionnels_evalues: {{ $globalStats['evaluated_professionals'] }},
        evaluations_ce_mois: {{ $globalStats['this_month'] }},
        repartition_notes: @json($globalStats['rating_distribution']),
        taux_5_etoiles: {{ number_format(($globalStats['rating_distribution'][5] ?? 0) / max($globalStats['total_evaluations'], 1) * 100, 1) }}
    };
    
    const json = JSON.stringify(statsData, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `statistiques_evaluations_${statsData.date}.json`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Statistiques exportées avec succès!', 'success');
}

// Afficher notification
function showNotification(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>

</div> <!-- Fermeture container-fluid -->
@endsection

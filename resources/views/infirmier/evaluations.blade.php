@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

{{-- Header avec bouton retour --}}
<div class="infirmier-evaluations-header">
  <div class="header-content">
    <div class="header-title">
      <a href="{{ route('infirmier.dashboard') }}" class="btn-back" title="Retour au dashboard">
        <i class="bi bi-arrow-left"></i>
      </a>
      <i class="bi bi-star-fill"></i>
      <span>Mes Évaluations</span>
    </div>
    <div class="header-badge">
      <i class="bi bi-clipboard2-pulse"></i>
      <span>{{ Auth::user()->name }}</span>
    </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- KPIs des évaluations de l'infirmier --}}
@php
    $myEvaluations = \App\Models\Evaluation::where('evaluated_user_id', Auth::id())->get();
    $evalStats = [
        'total' => $myEvaluations->count(),
        'moyenne' => $myEvaluations->avg('note'),
        'ce_mois' => $myEvaluations->where('created_at', '>=', now()->startOfMonth())->count(),
        'excellentes' => $myEvaluations->where('note', '>=', 4.5)->count(),
        'mediocres' => $myEvaluations->where('note', '<=', 2.5)->count(),
        'recentes' => $myEvaluations->where('created_at', '>=', now()->subDays(7))->count(),
    ];
    $recentEvaluations = $myEvaluations->sortByDesc('created_at')->take(10);
@endphp

<div class="row g-3 mb-4">
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card total">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-star-fill"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ $evalStats['total'] }}</div>
        <div class="infirmier-eval-kpi-label">Total</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card moyenne">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-star-half"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ number_format($evalStats['moyenne'] ?? 0, 1) }}</div>
        <div class="infirmier-eval-kpi-label">Moyenne</div>
        <div class="infirmier-eval-kpi-sub">/5</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card month">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-calendar-month"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ $evalStats['ce_mois'] }}</div>
        <div class="infirmier-eval-kpi-label">Ce Mois</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card excellent">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-emoji-smile"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ $evalStats['excellentes'] }}</div>
        <div class="infirmier-eval-kpi-label">Excellentes</div>
        <div class="infirmier-eval-kpi-sub">≥4.5</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card poor">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-emoji-frown"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ $evalStats['mediocres'] }}</div>
        <div class="infirmier-eval-kpi-label">Médiocres</div>
        <div class="infirmier-eval-kpi-sub">≤2.5</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2">
    <div class="infirmier-eval-kpi-card recent">
      <div class="infirmier-eval-kpi-icon">
        <i class="bi bi-clock-history"></i>
      </div>
      <div class="infirmier-eval-kpi-content">
        <div class="infirmier-eval-kpi-value">{{ $evalStats['recentes'] }}</div>
        <div class="infirmier-eval-kpi-label">Cette Semaine</div>
      </div>
    </div>
  </div>
</div>

{{-- Graphique de performance et liste des évaluations --}}
<div class="row">
  <div class="col-lg-5">
    <div class="infirmier-eval-chart-card">
      <div class="infirmier-eval-chart-header">
        <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Répartition de mes Notes</h6>
      </div>
      <div class="infirmier-eval-chart-body">
        @if($evalStats['total'] > 0)
          <canvas id="myRatingDistributionChart" height="200"></canvas>
          <div class="mt-3">
            <div class="rating-summary">
              <div class="rating-item">
                <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                <span class="rating-count">{{ $myEvaluations->where('note', '>=', 4.5)->count() }}</span>
              </div>
              <div class="rating-item">
                <span class="rating-stars">⭐⭐⭐⭐</span>
                <span class="rating-count">{{ $myEvaluations->whereBetween('note', [3.5, 4.49])->count() }}</span>
              </div>
              <div class="rating-item">
                <span class="rating-stars">⭐⭐⭐</span>
                <span class="rating-count">{{ $myEvaluations->whereBetween('note', [2.5, 3.49])->count() }}</span>
              </div>
              <div class="rating-item">
                <span class="rating-stars">⭐⭐</span>
                <span class="rating-count">{{ $myEvaluations->whereBetween('note', [1.5, 2.49])->count() }}</span>
              </div>
              <div class="rating-item">
                <span class="rating-stars">⭐</span>
                <span class="rating-count">{{ $myEvaluations->where('note', '<', 1.5)->count() }}</span>
              </div>
            </div>
          </div>
        @else
          <div class="text-center py-4">
            <i class="bi bi-star text-muted" style="font-size: 3rem;"></i>
            <h6 class="text-muted mt-2">Aucune évaluation</h6>
            <p class="text-muted small">Vous n'avez pas encore été évalué</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="infirmier-eval-table-container">
      <div class="infirmier-eval-table-header">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Mes Évaluations Récentes</h5>
        <div class="d-flex gap-2">
          <input type="text" id="searchMyEvaluations" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 180px;">
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-funnel me-1"></i>Note
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" data-my-eval-filter="all">Toutes</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#" data-my-eval-filter="5">⭐⭐⭐⭐⭐</a></li>
              <li><a class="dropdown-item" href="#" data-my-eval-filter="4">⭐⭐⭐⭐</a></li>
              <li><a class="dropdown-item" href="#" data-my-eval-filter="3">⭐⭐⭐</a></li>
              <li><a class="dropdown-item" href="#" data-my-eval-filter="2">⭐⭐</a></li>
              <li><a class="dropdown-item" href="#" data-my-eval-filter="1">⭐</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table infirmier-eval-table" id="myEvaluationsTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Évaluateur</th>
              <th>Note</th>
              <th>Commentaire</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentEvaluations as $eval)
              <tr data-my-eval-rating="{{ floor($eval->note) }}">
                <td>
                  <div>
                    <div class="fw-medium">{{ $eval->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $eval->created_at->format('H:i') }}</small>
                  </div>
                </td>
                <td>
                  @if($eval->evaluator)
                    <div class="d-flex align-items-center gap-2">
                      @php $avatar = $eval->evaluator->avatar_url ? asset($eval->evaluator->avatar_url) : 'https://ui-avatars.com/api/?size=32&name=' . urlencode($eval->evaluator->name); @endphp
                      <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                      <div>
                        <div class="fw-medium">{{ $eval->evaluator->name }}</div>
                        <small class="text-muted">{{ ucfirst($eval->evaluator->role) }}</small>
                      </div>
                    </div>
                  @else
                    <span class="text-muted">Évaluateur supprimé</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold {{ $eval->note >= 4 ? 'text-success' : ($eval->note >= 3 ? 'text-warning' : 'text-danger') }}">
                      {{ number_format($eval->note, 1) }}
                    </span>
                    <div class="stars-display">
                      @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($eval->note))
                          <i class="bi bi-star-fill text-warning"></i>
                        @elseif($i <= $eval->note)
                          <i class="bi bi-star-half text-warning"></i>
                        @else
                          <i class="bi bi-star text-muted"></i>
                        @endif
                      @endfor
                    </div>
                  </div>
                </td>
                <td>
                  @if($eval->commentaire)
                    <div class="comment-preview" title="{{ $eval->commentaire }}">
                      {{ Str::limit($eval->commentaire, 60) }}
                    </div>
                  @else
                    <span class="text-muted fst-italic">Aucun commentaire</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4">
                  <i class="bi bi-chat-quote text-muted" style="font-size: 2rem;"></i>
                  <h6 class="text-muted mt-2">Aucune évaluation</h6>
                  <p class="text-muted small">Vous n'avez pas encore été évalué</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Section d'amélioration --}}
@if($evalStats['total'] > 0)
<div class="row mt-4">
  <div class="col-12">
    <div class="improvement-section">
      <div class="improvement-header">
        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Points d'Amélioration</h5>
      </div>
      <div class="improvement-body">
        <div class="row g-3">
          @if($evalStats['moyenne'] < 3.0)
            <div class="col-md-4">
              <div class="improvement-tip alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                  <strong>Attention</strong>
                  <p>Votre note moyenne est faible. Concentrez-vous sur l'amélioration de vos services.</p>
                </div>
              </div>
            </div>
          @elseif($evalStats['moyenne'] >= 4.0)
            <div class="col-md-4">
              <div class="improvement-tip alert-success">
                <i class="bi bi-check-circle"></i>
                <div>
                  <strong>Excellent</strong>
                  <p>Vous maintenez un excellent niveau de service. Continuez ainsi !</p>
                </div>
              </div>
            </div>
          @endif
          
          @if($evalStats['recentes'] > 0)
            <div class="col-md-4">
              <div class="improvement-tip alert-info">
                <i class="bi bi-info-circle"></i>
                <div>
                  <strong>Activité récente</strong>
                  <p>{{ $evalStats['recentes'] }} nouvelle(s) évaluation(s) cette semaine.</p>
                </div>
              </div>
            </div>
          @endif
          
          <div class="col-md-4">
            <div class="improvement-tip alert-primary">
              <i class="bi bi-graph-up"></i>
              <div>
                <strong>Progression</strong>
                <p>Continuez à offrir des soins de qualité pour améliorer votre réputation.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

  </div>
</div>

{{-- Styles spécifiques pour les évaluations infirmier --}}
<style>
  /* Suppression complète de l'espacement excessif */
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
  
  /* Header des évaluations infirmier */
  .infirmier-evaluations-header {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 25px rgba(39, 174, 96, 0.15);
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
  
  /* KPIs des évaluations infirmier */
  .infirmier-eval-kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(39, 174, 96, 0.08);
    border: 1px solid rgba(39, 174, 96, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    height: 90px;
    position: relative;
    overflow: hidden;
  }
  
  .infirmier-eval-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
  }
  
  .infirmier-eval-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.15);
  }
  
  .infirmier-eval-kpi-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
  }
  
  .infirmier-eval-kpi-card.total .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #27ae60, #2ecc71); }
  .infirmier-eval-kpi-card.moyenne .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .infirmier-eval-kpi-card.month .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .infirmier-eval-kpi-card.excellent .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #10b981, #047857); }
  .infirmier-eval-kpi-card.poor .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
  .infirmier-eval-kpi-card.recent .infirmier-eval-kpi-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .infirmier-eval-kpi-content {
    flex: 1;
  }
  
  .infirmier-eval-kpi-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 2px;
  }
  
  .infirmier-eval-kpi-label {
    color: #374151;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
  }
  
  .infirmier-eval-kpi-sub {
    color: #6b7280;
    font-size: 0.65rem;
    font-weight: 400;
    margin-top: 1px;
  }
  
  /* Charts et tables */
  .infirmier-eval-chart-card, .infirmier-eval-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(39, 174, 96, 0.1);
    margin-bottom: 1.5rem;
  }
  
  .infirmier-eval-chart-header, .infirmier-eval-table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .infirmier-eval-chart-header h6, .infirmier-eval-table-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .infirmier-eval-chart-body {
    padding: 1.5rem;
  }
  
  .infirmier-eval-table {
    margin: 0;
  }
  
  .infirmier-eval-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 0.8rem 0.6rem;
    border: none;
    font-size: 0.8rem;
  }
  
  .infirmier-eval-table td {
    padding: 0.6rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 0.85rem;
  }
  
  .infirmier-eval-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .stars-display {
    display: flex;
    gap: 1px;
  }
  
  .stars-display i {
    font-size: 0.75rem;
  }
  
  .comment-preview {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .rating-summary {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .rating-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
  }
  
  .rating-count {
    font-weight: 600;
    color: #27ae60;
  }
  
  /* Section d'amélioration */
  .improvement-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(39, 174, 96, 0.1);
  }
  
  .improvement-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .improvement-body {
    padding: 1.5rem;
  }
  
  .improvement-tip {
    border-radius: 8px;
    border: none;
    padding: 1rem;
    margin-bottom: 0;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .improvement-tip i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
  }
  
  .improvement-tip strong {
    display: block;
    margin-bottom: 0.25rem;
  }
  
  .improvement-tip p {
    margin: 0;
    font-size: 0.875rem;
  }
  
  .alert-success { background-color: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
  .alert-warning { background-color: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b; }
  .alert-info { background-color: #dbeafe; color: #1e40af; border-left: 4px solid #3b82f6; }
  .alert-primary { background-color: #ede9fe; color: #5b21b6; border-left: 4px solid #8b5cf6; }
  
  /* Responsive */
  @media (max-width: 768px) {
    .infirmier-eval-kpi-value { font-size: 1.5rem; }
    .infirmier-eval-kpi-label { font-size: 0.7rem; }
    .infirmier-eval-kpi-icon { width: 40px; height: 40px; font-size: 16px; }
    .infirmier-eval-kpi-card { padding: 16px; height: 80px; gap: 10px; }
    
    .header-content {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
  }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique de répartition des notes
    @php
        $myRatingCounts = [
            '5' => $myEvaluations->where('note', '>=', 4.5)->count(),
            '4' => $myEvaluations->whereBetween('note', [3.5, 4.49])->count(),
            '3' => $myEvaluations->whereBetween('note', [2.5, 3.49])->count(),
            '2' => $myEvaluations->whereBetween('note', [1.5, 2.49])->count(),
            '1' => $myEvaluations->where('note', '<', 1.5)->count(),
        ];
    @endphp
    
    const myRatingData = @json(array_values($myRatingCounts));
    const totalEvaluations = {{ $evalStats['total'] }};
    
    // Graphique répartition de mes notes
    const myRatingCtx = document.getElementById('myRatingDistributionChart');
    if (myRatingCtx && totalEvaluations > 0) {
        new Chart(myRatingCtx, {
            type: 'doughnut',
            data: {
                labels: ['5 étoiles', '4 étoiles', '3 étoiles', '2 étoiles', '1 étoile'],
                datasets: [{
                    data: myRatingData,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6b7280'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // Fonction de recherche
    const searchMyEvaluations = document.getElementById('searchMyEvaluations');
    if (searchMyEvaluations) {
        searchMyEvaluations.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#myEvaluationsTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
    
    // Filtrage par note
    document.querySelectorAll('[data-my-eval-filter]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-my-eval-filter');
            const rows = document.querySelectorAll('#myEvaluationsTable tbody tr[data-my-eval-rating]');
            
            rows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-my-eval-rating') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection

@endsection
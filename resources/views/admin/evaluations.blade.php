@extends('layouts.app')

@section('body_class', 'admin-page')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="admin-intelligent-sidebar sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">
    <div class="admin-main-content">

{{-- Header avec bouton retour --}}
<div class="evaluations-header scroll-fade-in">
  <div class="header-content">
    <div class="header-title">
      <a href="{{ route('admin.dashboard') }}" class="btn-back" title="Retour au dashboard">
        <i class="bi bi-arrow-left"></i>
      </a>
      <i class="bi bi-star-fill"></i>
      <span>Gestion des Évaluations</span>
    </div>
    <div class="header-badge">
      <i class="bi bi-shield-check"></i>
      <span>{{ Auth::user()->name }}</span>
    </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- KPIs complets des évaluations --}}
@php
    $evalStats = [
        'total' => \App\Models\Evaluation::count(),
        'moyenne' => \App\Models\Evaluation::avg('note'),
        'ce_mois' => \App\Models\Evaluation::whereMonth('created_at', now()->month)->count(),
        'professionnels_evalues' => \App\Models\Evaluation::distinct('evaluated_user_id')->count(),
        'excellentes' => \App\Models\Evaluation::where('note', '>=', 4.5)->count(),
        'mediocres' => \App\Models\Evaluation::where('note', '<=', 2.5)->count(),
    ];
    $topProfessionals = \App\Models\User::whereIn('role', ['medecin', 'infirmier'])
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'moyenne' => \App\Models\Evaluation::where('evaluated_user_id', $user->id)->avg('note'),
                'total' => \App\Models\Evaluation::where('evaluated_user_id', $user->id)->count()
            ];
        })
        ->where('total', '>', 0)
        ->sortByDesc('moyenne')
        ->take(10);
    $recentEvaluations = \App\Models\Evaluation::with(['evaluator', 'evaluatedUser'])
        ->orderByDesc('created_at')
        ->take(20)
        ->get();
@endphp
  
<div class="row g-3 mb-4 scroll-slide-left">
  <div class="col">
    <div class="eval-kpi-card total scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-star-fill"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ $evalStats['total'] }}</div>
        <div class="eval-kpi-label">Total Évaluations</div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="eval-kpi-card moyenne scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-star-half"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ number_format($evalStats['moyenne'] ?? 0, 1) }}</div>
        <div class="eval-kpi-label">Note Moyenne</div>
        <div class="eval-kpi-sub">/5 étoiles</div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="eval-kpi-card month scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-calendar-month"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ $evalStats['ce_mois'] }}</div>
        <div class="eval-kpi-label">Ce Mois</div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="eval-kpi-card professionals scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-people"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ $evalStats['professionnels_evalues'] }}</div>
        <div class="eval-kpi-label">Professionnels Évalués</div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="eval-kpi-card excellent scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-emoji-smile"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ $evalStats['excellentes'] }}</div>
        <div class="eval-kpi-label">Excellentes</div>
        <div class="eval-kpi-sub">≥ 4.5 étoiles</div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="eval-kpi-card poor scroll-card-hover gpu-accelerated">
      <div class="eval-kpi-icon">
        <i class="bi bi-emoji-frown"></i>
      </div>
      <div class="eval-kpi-content">
        <div class="eval-kpi-value">{{ $evalStats['mediocres'] }}</div>
        <div class="eval-kpi-label">Médiocres</div>
        <div class="eval-kpi-sub">≤ 2.5 étoiles</div>
      </div>
    </div>
  </div>
</div>
</div>

{{-- Navigation par onglets pour les données --}}
<ul class="nav nav-tabs eval-tabs mb-3 scroll-fade-in" id="evalTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="top-professionals-tab" data-bs-toggle="tab" data-bs-target="#top-professionals" type="button" role="tab">
      <i class="bi bi-trophy me-1"></i> Top Professionnels
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="recent-evals-tab" data-bs-toggle="tab" data-bs-target="#recent-evals" type="button" role="tab">
      <i class="bi bi-clock-history me-1"></i> Évaluations Récentes
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="stats-details-tab" data-bs-toggle="tab" data-bs-target="#stats-details" type="button" role="tab">
      <i class="bi bi-graph-up me-1"></i> Statistiques Détaillées
    </button>
  </li>
</ul>

<div class="tab-content scroll-scale-in" id="evalTabContent">
  {{-- Top Professionnels --}}
  <div class="tab-pane fade show active" id="top-professionals" role="tabpanel">
    <div class="eval-table-container">
      <div class="eval-table-header">
        <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Classement des Professionnels</h5>
        <div class="d-flex gap-2">
          <input type="text" id="searchProfessionals" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 200px;">
          <button class="btn btn-outline-success btn-sm" title="Exporter le classement">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table eval-table">
          <thead>
            <tr>
              <th>Rang</th>
              <th>Professionnel</th>
              <th>Spécialité</th>
              <th>Note Moyenne</th>
              <th>Nb Évaluations</th>
              <th>Dernière Évaluation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topProfessionals as $index => $prof)
              <tr>
                <td>
                  @if($index == 0)
                    <span class="badge bg-warning text-dark">🥇 #1</span>
                  @elseif($index == 1)
                    <span class="badge bg-secondary">🥈 #2</span>
                  @elseif($index == 2)
                    <span class="badge bg-info">🥉 #3</span>
                  @else
                    <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @php $user = \App\Models\User::find($prof['id']); @endphp
                    @if($user)
                      @php $avatar = $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?size=32&name=' . urlencode($user->name); @endphp
                      <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                    @endif
                    <div>
                      <div class="fw-medium">Dr. {{ $prof['name'] }}</div>
                      <small class="text-muted">{{ ucfirst($prof['role']) }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  @if($user)
                    <span class="badge bg-light text-dark">{{ $user->specialite ?? 'Non renseignée' }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-warning">{{ number_format($prof['moyenne'], 1) }}</span>
                    <div class="stars-display">
                      @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($prof['moyenne']))
                          <i class="bi bi-star-fill text-warning"></i>
                        @elseif($i <= $prof['moyenne'])
                          <i class="bi bi-star-half text-warning"></i>
                        @else
                          <i class="bi bi-star text-muted"></i>
                        @endif
                      @endfor
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary">{{ $prof['total'] }} éval{{ $prof['total'] > 1 ? 's' : '' }}</span>
                </td>
                <td>
                  @php 
                    $lastEval = \App\Models\Evaluation::where('evaluated_user_id', $prof['id'])->latest('id')->first(); 
                  @endphp
                  @if($lastEval)
                    @php
                      try {
                        $dateText = $lastEval->created_at ? $lastEval->created_at->diffForHumans() : \Carbon\Carbon::parse($lastEval->updated_at ?? 'now')->diffForHumans();
                      } catch (\Exception $e) {
                        $dateText = 'Récemment';
                      }
                    @endphp
                    <small class="text-muted">{{ $dateText }}</small>
                  @else
                    <small class="text-muted">-</small>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-outline-info btn-sm" title="Voir détails">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" title="Voir évaluations">
                      <i class="bi bi-list-ul"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="bi bi-star text-muted" style="font-size: 3rem;"></i>
                  <h6 class="text-muted mt-2">Aucun professionnel évalué</h6>
                  <p class="text-muted small">Les évaluations apparaîtront ici</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Évaluations Récentes --}}
  <div class="tab-pane fade" id="recent-evals" role="tabpanel">
    <div class="eval-table-container">
      <div class="eval-table-header">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Évaluations Récentes</h5>
        <div class="d-flex gap-2">
          <input type="text" id="searchEvaluations" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 200px;">
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" data-eval-filter="all">Toutes les évaluations</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#" data-eval-filter="5">5 étoiles</a></li>
              <li><a class="dropdown-item" href="#" data-eval-filter="4">4 étoiles</a></li>
              <li><a class="dropdown-item" href="#" data-eval-filter="3">3 étoiles</a></li>
              <li><a class="dropdown-item" href="#" data-eval-filter="2">2 étoiles</a></li>
              <li><a class="dropdown-item" href="#" data-eval-filter="1">1 étoile</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table eval-table" id="evaluationsTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Évaluateur</th>
              <th>Professionnel Évalué</th>
              <th>Note</th>
              <th>Commentaire</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentEvaluations as $eval)
              <tr data-eval-rating="{{ floor($eval->note) }}">
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
                    <span class="text-muted">Utilisateur supprimé</span>
                  @endif
                </td>
                <td>
                  @if($eval->evaluatedUser)
                    <div class="d-flex align-items-center gap-2">
                      @php $avatar = $eval->evaluatedUser->avatar_url ? asset($eval->evaluatedUser->avatar_url) : 'https://ui-avatars.com/api/?size=32&name=' . urlencode($eval->evaluatedUser->name); @endphp
                      <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                      <div>
                        <div class="fw-medium">Dr. {{ $eval->evaluatedUser->name }}</div>
                        <small class="text-muted">{{ $eval->evaluatedUser->specialite ?? 'Spécialité non renseignée' }}</small>
                      </div>
                    </div>
                  @else
                    <span class="text-muted">Professionnel supprimé</span>
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
                      {{ Str::limit($eval->commentaire, 50) }}
                    </div>
                  @else
                    <span class="text-muted fst-italic">Aucun commentaire</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-outline-info btn-sm" title="Voir détails">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" title="Supprimer">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <i class="bi bi-chat-quote text-muted" style="font-size: 3rem;"></i>
                  <h6 class="text-muted mt-2">Aucune évaluation récente</h6>
                  <p class="text-muted small">Les évaluations apparaîtront ici</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Statistiques Détaillées --}}
  <div class="tab-pane fade" id="stats-details" role="tabpanel">
    <div class="row">
      <div class="col-lg-6">
        <div class="eval-chart-card">
          <div class="eval-chart-header">
            <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Répartition des Notes</h6>
          </div>
          <div class="eval-chart-body">
            <canvas id="ratingDistributionChart" height="200"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="eval-chart-card">
          <div class="eval-chart-header">
            <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Évolution Mensuelle</h6>
          </div>
          <div class="eval-chart-body">
            <canvas id="monthlyEvaluationsChart" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  </div>
</div>

    </div> {{-- Fin admin-main-content --}}
  </div> {{-- Fin col-lg-9 --}}
</div> {{-- Fin row --}}

{{-- Styles spécifiques pour les évaluations --}}
<style>
  /* Suppression complète de l'espacement excessif pour dashboard évaluations */
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
  
  /* Header des évaluations */

  
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
  
  /* KPIs des évaluations */
  .eval-kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
    height: 100px;
    position: relative;
    overflow: hidden;
  }
  
  .eval-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #d97706);
  }
  
  .eval-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(245, 158, 11, 0.15);
  }
  
  .eval-kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .eval-kpi-card.total .eval-kpi-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .eval-kpi-card.moyenne .eval-kpi-icon { background: linear-gradient(135deg, #10b981, #059669); }
  .eval-kpi-card.month .eval-kpi-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .eval-kpi-card.professionals .eval-kpi-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .eval-kpi-card.excellent .eval-kpi-icon { background: linear-gradient(135deg, #10b981, #047857); }
  .eval-kpi-card.poor .eval-kpi-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
  
  .eval-kpi-content {
    flex: 1;
  }
  
  .eval-kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }
  
  .eval-kpi-label {
    color: #374151;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
  }
  
  .eval-kpi-sub {
    color: #6b7280;
    font-size: 0.7rem;
    font-weight: 400;
    margin-top: 2px;
  }
  
  /* Onglets des évaluations */
  .eval-tabs { 
    display: flex; 
    flex-wrap: nowrap; 
    gap: 0.25rem; 
    background: #f8fafc; 
    padding: 0.5rem; 
    border-radius: 12px;
    margin-bottom: 1rem;
  }
  
  .eval-tabs .nav-link { 
    flex: 0 0 auto; 
    min-width: 160px; 
    text-align: center; 
    padding: 0.7rem 0.9rem; 
    border: none; 
    border-radius: 8px;
    white-space: nowrap; 
    color: #64748b; 
    font-weight: 500;
    font-size: 0.85rem;
    background: transparent;
    transition: all 0.2s ease;
  }
  
  .eval-tabs .nav-link:hover {
    background: #e2e8f0;
    color: #475569;
  }
  
  .eval-tabs .nav-link.active { 
    background: #f59e0b; 
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
  }
  
  /* Tables des évaluations */
  .eval-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(245, 158, 11, 0.1);
  }
  
  .eval-table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .eval-table-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .eval-table {
    margin: 0;
  }
  
  .eval-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 0.75rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .eval-table td {
    padding: 0.75rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .eval-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .stars-display {
    display: flex;
    gap: 1px;
  }
  
  .stars-display i {
    font-size: 0.8rem;
  }
  
  .comment-preview {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  /* Cartes de graphiques */
  .eval-chart-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(245, 158, 11, 0.1);
    margin-bottom: 1.5rem;
  }
  
  .eval-chart-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .eval-chart-body {
    padding: 1.5rem;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .eval-kpi-value { font-size: 2rem; }
    .eval-kpi-icon { width: 50px; height: 50px; font-size: 20px; }
    .eval-kpi-card { padding: 20px; height: 90px; }
  }
  
  @media (max-width: 768px) {
    .eval-kpi-value { font-size: 1.75rem; }
    .eval-kpi-label { font-size: 0.75rem; }
    .eval-kpi-icon { width: 45px; height: 45px; font-size: 18px; }
    .eval-kpi-card { padding: 16px; height: 80px; gap: 12px; }
    
    .header-content {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .eval-tabs {
      flex-wrap: wrap;
    }
    
    .eval-tabs .nav-link {
      min-width: 120px;
      font-size: 0.8rem;
    }
  }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques
    @php
        $ratingCounts = [
            '5' => \App\Models\Evaluation::where('note', '>=', 4.5)->count(),
            '4' => \App\Models\Evaluation::whereBetween('note', [3.5, 4.49])->count(),
            '3' => \App\Models\Evaluation::whereBetween('note', [2.5, 3.49])->count(),
            '2' => \App\Models\Evaluation::whereBetween('note', [1.5, 2.49])->count(),
            '1' => \App\Models\Evaluation::where('note', '<', 1.5)->count(),
        ];
        
        $monthlyData = [];
        for($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'count' => \App\Models\Evaluation::whereMonth('created_at', $date->month)
                                                ->whereYear('created_at', $date->year)
                                                ->count()
            ];
        }
    @endphp
    
    const ratingData = @json(array_values($ratingCounts));
    const monthlyLabels = @json(array_column($monthlyData, 'month'));
    const monthlyCounts = @json(array_column($monthlyData, 'count'));
    
    // Graphique répartition des notes
    const ratingCtx = document.getElementById('ratingDistributionChart');
    if (ratingCtx) {
        new Chart(ratingCtx, {
            type: 'doughnut',
            data: {
                labels: ['5 étoiles', '4 étoiles', '3 étoiles', '2 étoiles', '1 étoile'],
                datasets: [{
                    data: ratingData,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6b7280'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Graphique évolution mensuelle
    const monthlyCtx = document.getElementById('monthlyEvaluationsChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Évaluations',
                    data: monthlyCounts,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b'
                }]
            },
            options: {
                responsive: true,
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
    }
    
    // Fonctions de recherche et filtrage
    const searchProfessionals = document.getElementById('searchProfessionals');
    if (searchProfessionals) {
        searchProfessionals.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#top-professionals table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
    
    const searchEvaluations = document.getElementById('searchEvaluations');
    if (searchEvaluations) {
        searchEvaluations.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#evaluationsTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
    
    // Filtrage par note
    document.querySelectorAll('[data-eval-filter]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-eval-filter');
            const rows = document.querySelectorAll('#evaluationsTable tbody tr[data-eval-rating]');
            
            rows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-eval-rating') === filter) {
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
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
{{-- Header moderne pour gestion des paiements admin --}}
<div class="payments-admin-header scroll-fade-in">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-credit-card-2-front"></i>
      <span>Gestion des Paiements</span>
    </div>
    <div class="header-actions">
      <a href="{{ route('admin.users.index', ['role' => 'secretaire']) }}" class="action-btn">
        <i class="bi bi-person-workspace"></i>
        Gérer Secrétaires
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour Dashboard
      </a>
    </div>
  </div>
</div>

{{-- Statistiques de paiement --}}
<div class="stats-grid mb-4 scroll-slide-left">
  <div class="stat-card stat-total scroll-card-hover gpu-accelerated">
    <div class="stat-icon">
      <i class="bi bi-cash-stack"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ number_format($stats['total_amount'] ?? 0) }}</div>
      <div class="stat-label">Total revenus (XOF)</div>
    </div>
  </div>
  
  <div class="stat-card stat-today scroll-card-hover gpu-accelerated">
    <div class="stat-icon">
      <i class="bi bi-calendar-day"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['today_payments'] ?? 0 }}</div>
      <div class="stat-label">Paiements aujourd'hui</div>
    </div>
  </div>
  
  <div class="stat-card stat-secretaires scroll-card-hover gpu-accelerated">
    <div class="stat-icon">
      <i class="bi bi-people-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['active_secretaires'] ?? 0 }}</div>
      <div class="stat-label">Secrétaires actives</div>
    </div>
  </div>
  
  <div class="stat-card stat-settings scroll-card-hover gpu-accelerated">
    <div class="stat-icon">
      <i class="bi bi-gear-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['tarifs_configured'] ?? 0 }}</div>
      <div class="stat-label">Tarifs configurés</div>
    </div>
  </div>
</div>

{{-- Actions principales --}}
<div class="admin-actions-grid scroll-scale-in">
  <div class="action-card tarifs-card">
    <div class="card-header">
      <i class="bi bi-currency-exchange"></i>
      <span>Configuration des Tarifs</span>
    </div>
    <div class="card-body">
      <p>Définissez les prix des consultations, analyses et actes médicaux</p>
      <div class="current-tarifs">
        <div class="tarif-item">
          <span>Consultation:</span>
          <strong>{{ number_format($tarifs['consultation'] ?? 5000) }} XOF</strong>
        </div>
        <div class="tarif-item">
          <span>Analyse:</span>
          <strong>{{ number_format($tarifs['analyse'] ?? 10000) }} XOF</strong>
        </div>
      </div>
    </div>
    <div class="card-actions">
      <a href="{{ route('admin.payments.settings') }}" class="btn-card-action btn-tarifs">
        <i class="bi bi-pencil-square"></i>
        Modifier les tarifs
      </a>
    </div>
  </div>
  
  <div class="action-card secretaires-card">
    <div class="card-header">
      <i class="bi bi-person-workspace"></i>
      <span>Gestion des Secrétaires</span>
    </div>
    <div class="card-body">
      <p>Gérez les comptes des secrétaires autorisées à effectuer des paiements</p>
      <div class="secretaires-list">
        @forelse($secretaires as $secretaire)
          <div class="secretaire-item">
            <div class="secretaire-avatar">{{ strtoupper(substr($secretaire->name, 0, 1)) }}</div>
            <div class="secretaire-info">
              <span>{{ $secretaire->name }}</span>
              <small class="{{ $secretaire->active ? 'text-success' : 'text-danger' }}">
                {{ $secretaire->active ? 'Active' : 'Inactive' }}
              </small>
            </div>
          </div>
        @empty
          <small class="text-muted">Aucune secrétaire configurée</small>
        @endforelse
      </div>
    </div>
    <div class="card-actions">
      <a href="{{ route('admin.users.index', ['role' => 'secretaire']) }}" class="btn-card-action btn-secretaires">
        <i class="bi bi-people"></i>
        Gérer les secrétaires
      </a>
    </div>
  </div>
  
  <div class="action-card reports-card">
    <div class="card-header">
      <i class="bi bi-bar-chart-line"></i>
      <span>Rapports & Statistiques</span>
    </div>
    <div class="card-body">
      <p>Consultez les rapports de paiements et statistiques financières</p>
      <div class="quick-stats">
        <div class="quick-stat">
          <span>Aujourd'hui:</span>
          <strong>{{ number_format($stats['today_amount'] ?? 0) }} XOF</strong>
        </div>
        <div class="quick-stat">
          <span>Ce mois:</span>
          <strong>{{ number_format($stats['month_amount'] ?? 0) }} XOF</strong>
        </div>
      </div>
    </div>
    <div class="card-actions">
      <a href="{{ route('admin.payments.reports') }}" class="btn-card-action btn-reports">
        <i class="bi bi-file-earmark-bar-graph"></i>
        Voir les rapports
      </a>
    </div>
  </div>
  
  <div class="action-card audit-card">
    <div class="card-header">
      <i class="bi bi-shield-check"></i>
      <span>Audit & Logs</span>
    </div>
    <div class="card-body">
      <p>Suivez toutes les transactions et modifications effectuées dans le système</p>
      <div class="recent-activity">
        @forelse($recent_activities as $activity)
          <div class="activity-item">
            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
            <span>{{ $activity->description }}</span>
          </div>
        @empty
          <small class="text-muted">Aucune activité récente</small>
        @endforelse
      </div>
    </div>
    <div class="card-actions">
      <a href="{{ route('admin.audit.index') }}" class="btn-card-action btn-audit">
        <i class="bi bi-eye"></i>
        Voir les logs
      </a>
    </div>
  </div>
</div>

{{-- Styles modernes pour la gestion des paiements admin --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1400px !important; }
  
  /* Header moderne paiements admin */
  .payments-admin-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
    font-size: 1.2rem;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .action-btn:hover {
    background: white;
    color: #059669;
    transform: translateY(-2px);
  }
  
  /* Grille des statistiques */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(5, 150, 105, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(5, 150, 105, 0.15);
  }
  
  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .stat-total .stat-icon { background: linear-gradient(135deg, #059669, #047857); }
  .stat-today .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-secretaires .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .stat-settings .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  
  .stat-content {
    flex: 1;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }
  
  .stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  /* Grille des actions */
  .admin-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
  }
  
  .action-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 2px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .tarifs-card:hover { border-color: #059669; }
  .secretaires-card:hover { border-color: #8b5cf6; }
  .reports-card:hover { border-color: #3b82f6; }
  .audit-card:hover { border-color: #f59e0b; }
  
  .card-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 1.1rem;
    color: white;
  }
  
  .tarifs-card .card-header { background: linear-gradient(135deg, #059669, #047857); }
  .secretaires-card .card-header { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .reports-card .card-header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .audit-card .card-header { background: linear-gradient(135deg, #f59e0b, #d97706); }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .card-body p {
    color: #6b7280;
    margin-bottom: 1rem;
  }
  
  .current-tarifs, .quick-stats {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .tarif-item, .quick-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
  }
  
  .secretaires-list {
    max-height: 120px;
    overflow-y: auto;
  }
  
  .secretaire-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: #f8fafc;
  }
  
  .secretaire-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.9rem;
  }
  
  .card-actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
  }
  
  .btn-card-action {
    width: 100%;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 600;
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-tarifs { background: linear-gradient(135deg, #059669, #047857); }
  .btn-secretaires { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .btn-reports { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .btn-audit { background: linear-gradient(135deg, #f59e0b, #d97706); }
  
  .btn-card-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    color: white;
  }
  
  .recent-activity {
    max-height: 100px;
    overflow-y: auto;
  }
  
  .activity-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.5rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    background: #f8fafc;
    font-size: 0.9rem;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .admin-actions-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

    </div> {{-- Fin admin-main-content --}}
  </div> {{-- Fin col-lg-9 --}}
</div> {{-- Fin row --}}
@endsection

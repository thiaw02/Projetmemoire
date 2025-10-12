@extends('layouts.app')

@section('content')
{{-- Header moderne pour statistiques --}}
<div class="statistics-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-graph-up"></i>
      <div>
        <span>Tableau de Bord - Statistiques</span>
        <small>Vue d'ensemble des performances du système</small>
      </div>
    </div>
    <div class="header-actions">
      <div class="period-selector">
        <select id="periodSelector" class="form-select">
          <option value="today">Aujourd'hui</option>
          <option value="week" selected>Cette semaine</option>
          <option value="month">Ce mois</option>
          <option value="quarter">Ce trimestre</option>
          <option value="year">Cette année</option>
        </select>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour
      </a>
    </div>
  </div>
</div>

{{-- KPI Cards principales --}}
<div class="kpi-grid">
  <div class="kpi-card revenue-card">
    <div class="kpi-icon">
      <i class="bi bi-cash-stack"></i>
    </div>
    <div class="kpi-content">
      <div class="kpi-value" data-target="{{ $kpis['total_revenue'] ?? 0 }}">0</div>
      <div class="kpi-label">Chiffre d'affaires</div>
      <div class="kpi-unit">XOF</div>
      <div class="kpi-trend {{ ($kpis['revenue_trend'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
        <i class="bi {{ ($kpis['revenue_trend'] ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
        {{ abs($kpis['revenue_trend'] ?? 0) }}% vs période précédente
      </div>
    </div>
  </div>
  
  <div class="kpi-card patients-card">
    <div class="kpi-icon">
      <i class="bi bi-people"></i>
    </div>
    <div class="kpi-content">
      <div class="kpi-value" data-target="{{ $kpis['total_patients'] ?? 0 }}">0</div>
      <div class="kpi-label">Patients traités</div>
      <div class="kpi-unit">personnes</div>
      <div class="kpi-trend {{ ($kpis['patients_trend'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
        <i class="bi {{ ($kpis['patients_trend'] ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
        {{ abs($kpis['patients_trend'] ?? 0) }}% vs période précédente
      </div>
    </div>
  </div>
  
  <div class="kpi-card consultations-card">
    <div class="kpi-icon">
      <i class="bi bi-calendar-check"></i>
    </div>
    <div class="kpi-content">
      <div class="kpi-value" data-target="{{ $kpis['consultations'] ?? 0 }}">0</div>
      <div class="kpi-label">Consultations</div>
      <div class="kpi-unit">rdv</div>
      <div class="kpi-trend {{ ($kpis['consultations_trend'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
        <i class="bi {{ ($kpis['consultations_trend'] ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
        {{ abs($kpis['consultations_trend'] ?? 0) }}% vs période précédente
      </div>
    </div>
  </div>
  
  <div class="kpi-card efficiency-card">
    <div class="kpi-icon">
      <i class="bi bi-speedometer2"></i>
    </div>
    <div class="kpi-content">
      <div class="kpi-value" data-target="{{ $kpis['efficiency'] ?? 85 }}">0</div>
      <div class="kpi-label">Taux d'efficacité</div>
      <div class="kpi-unit">%</div>
      <div class="kpi-trend {{ ($kpis['efficiency_trend'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
        <i class="bi {{ ($kpis['efficiency_trend'] ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
        {{ abs($kpis['efficiency_trend'] ?? 0) }}% vs période précédente
      </div>
    </div>
  </div>
</div>

{{-- Graphiques et analyses --}}
<div class="analytics-grid">
  {{-- Graphique revenus --}}
  <div class="chart-card revenue-chart-card">
    <div class="chart-header">
      <h3><i class="bi bi-bar-chart-line"></i> Évolution des revenus</h3>
      <div class="chart-controls">
        <button class="btn-chart-type active" data-type="line">Ligne</button>
        <button class="btn-chart-type" data-type="bar">Barres</button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="revenueChart" height="300"></canvas>
    </div>
  </div>
  
  {{-- Graphique consultations --}}
  <div class="chart-card consultations-chart-card">
    <div class="chart-header">
      <h3><i class="bi bi-pie-chart"></i> Répartition des consultations</h3>
      <div class="chart-legend" id="consultationsLegend">
        <!-- Légende générée dynamiquement -->
      </div>
    </div>
    <div class="chart-container">
      <canvas id="consultationsChart" height="300"></canvas>
    </div>
  </div>
  
  {{-- Top médecins --}}
  <div class="ranking-card doctors-ranking">
    <div class="ranking-header">
      <h3><i class="bi bi-award"></i> Top Médecins</h3>
      <span class="ranking-period">Cette semaine</span>
    </div>
    <div class="ranking-list">
      @forelse($top_doctors ?? [] as $index => $doctor)
        <div class="ranking-item">
          <div class="rank-position {{ $index < 3 ? 'top-three' : '' }}">
            @if($index == 0)
              <i class="bi bi-trophy-fill gold"></i>
            @elseif($index == 1)
              <i class="bi bi-trophy-fill silver"></i>
            @elseif($index == 2)
              <i class="bi bi-trophy-fill bronze"></i>
            @else
              {{ $index + 1 }}
            @endif
          </div>
          <div class="doctor-info">
            <div class="doctor-avatar">{{ substr($doctor['name'], 0, 1) }}</div>
            <div class="doctor-details">
              <span class="doctor-name">{{ $doctor['name'] }}</span>
              <small class="doctor-specialty">{{ $doctor['specialty'] ?? 'Généraliste' }}</small>
            </div>
          </div>
          <div class="doctor-stats">
            <div class="stat-value">{{ $doctor['consultations'] }}</div>
            <div class="stat-label">consultations</div>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class="bi bi-info-circle"></i>
          <span>Aucune donnée disponible</span>
        </div>
      @endforelse
    </div>
  </div>
  
  {{-- Activités récentes --}}
  <div class="activity-card">
    <div class="activity-header">
      <h3><i class="bi bi-clock-history"></i> Activité en temps réel</h3>
      <button class="btn-refresh" onclick="refreshActivity()">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
    <div class="activity-feed" id="activityFeed">
      @forelse($recent_activities ?? [] as $activity)
        <div class="activity-item">
          <div class="activity-icon {{ $activity['type'] }}">
            <i class="bi {{ $activity['icon'] }}"></i>
          </div>
          <div class="activity-content">
            <span class="activity-text">{{ $activity['message'] }}</span>
            <small class="activity-time">{{ $activity['time'] }}</small>
          </div>
        </div>
      @empty
        <div class="empty-activity">
          <i class="bi bi-clock"></i>
          <span>Aucune activité récente</span>
        </div>
      @endforelse
    </div>
  </div>
</div>

{{-- Métriques détaillées --}}
<div class="metrics-section">
  <div class="section-header">
    <h2><i class="bi bi-clipboard-data"></i> Métriques détaillées</h2>
    <div class="section-actions">
      <button class="btn-filter" onclick="toggleFilters()">
        <i class="bi bi-funnel"></i>
        Filtres
      </button>
    </div>
  </div>
  
  <div class="filters-panel" id="filtersPanel" style="display: none;">
    <div class="filter-group">
      <label>Département:</label>
      <select class="form-select" id="departmentFilter">
        <option value="">Tous les départements</option>
        <option value="cardiology">Cardiologie</option>
        <option value="neurology">Neurologie</option>
        <option value="pediatry">Pédiatrie</option>
        <option value="general">Médecine générale</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Type de consultation:</label>
      <select class="form-select" id="consultationTypeFilter">
        <option value="">Tous les types</option>
        <option value="regular">Consultation normale</option>
        <option value="emergency">Urgence</option>
        <option value="follow_up">Suivi</option>
      </select>
    </div>
    <button class="btn-apply-filters" onclick="applyFilters()">Appliquer</button>
  </div>
  
  <div class="metrics-grid">
    <div class="metric-card">
      <h4>Temps d'attente moyen</h4>
      <div class="metric-value">{{ $metrics['avg_wait_time'] ?? '15 min' }}</div>
      <div class="metric-comparison">
        <span class="positive">-2 min vs hier</span>
      </div>
    </div>
    
    <div class="metric-card">
      <h4>Taux de satisfaction</h4>
      <div class="metric-value">{{ $metrics['satisfaction_rate'] ?? '92%' }}</div>
      <div class="metric-comparison">
        <span class="positive">+3% vs semaine dernière</span>
      </div>
    </div>
    
    <div class="metric-card">
      <h4>Durée moyenne consultation</h4>
      <div class="metric-value">{{ $metrics['avg_consultation_time'] ?? '25 min' }}</div>
      <div class="metric-comparison">
        <span class="neutral">= vs moyenne</span>
      </div>
    </div>
    
    <div class="metric-card">
      <h4>Taux d'absentéisme</h4>
      <div class="metric-value">{{ $metrics['absence_rate'] ?? '8%' }}</div>
      <div class="metric-comparison">
        <span class="negative">+1% vs mois dernier</span>
      </div>
    </div>
  </div>
</div>

<style>
  /* Variables CSS */
  :root {
    --primary-color: #3b82f6;
    --success-color: #059669;
    --warning-color: #f59e0b;
    --danger-color: #dc2626;
    --purple-color: #8b5cf6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-800: #1f2937;
    --gray-900: #111827;
  }
  
  /* Conteneur principal */
  body > .container { max-width: 1600px !important; }
  
  /* Header statistiques */
  .statistics-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
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
    gap: 1rem;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.75rem;
    border-radius: 12px;
    font-size: 1.5rem;
  }
  
  .header-title span {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  
  .header-title small {
    font-size: 1rem;
    opacity: 0.9;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .period-selector .form-select {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-weight: 500;
  }
  
  /* Grille KPI */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .kpi-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
  }
  
  .revenue-card::before { background: linear-gradient(90deg, var(--success-color), #047857); }
  .patients-card::before { background: linear-gradient(90deg, var(--primary-color), #1d4ed8); }
  .consultations-card::before { background: linear-gradient(90deg, var(--purple-color), #7c3aed); }
  .efficiency-card::before { background: linear-gradient(90deg, var(--warning-color), #d97706); }
  
  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .kpi-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
  }
  
  .revenue-card .kpi-icon { background: linear-gradient(135deg, var(--success-color), #047857); }
  .patients-card .kpi-icon { background: linear-gradient(135deg, var(--primary-color), #1d4ed8); }
  .consultations-card .kpi-icon { background: linear-gradient(135deg, var(--purple-color), #7c3aed); }
  .efficiency-card .kpi-icon { background: linear-gradient(135deg, var(--warning-color), #d97706); }
  
  .kpi-content {
    flex: 1;
  }
  
  .kpi-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 0.25rem;
  }
  
  .kpi-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .kpi-unit {
    font-size: 0.8rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem;
  }
  
  .kpi-trend {
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .kpi-trend.positive { color: var(--success-color); }
  .kpi-trend.negative { color: var(--danger-color); }
  
  /* Grille des graphiques */
  .analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 2rem;
    margin-bottom: 3rem;
  }
  
  .chart-card, .ranking-card, .activity-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }
  
  .revenue-chart-card {
    grid-column: 1 / -1;
  }
  
  .chart-header, .ranking-header, .activity-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .chart-header h3, .ranking-header h3, .activity-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .chart-controls {
    display: flex;
    gap: 0.5rem;
  }
  
  .btn-chart-type {
    background: var(--gray-100);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    color: var(--gray-600);
    transition: all 0.2s ease;
  }
  
  .btn-chart-type.active, .btn-chart-type:hover {
    background: var(--primary-color);
    color: white;
  }
  
  .chart-container {
    padding: 1.5rem;
    position: relative;
  }
  
  /* Classement médecins */
  .ranking-list {
    padding: 1rem 0;
  }
  
  .ranking-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    transition: all 0.2s ease;
  }
  
  .ranking-item:hover {
    background: var(--gray-50);
  }
  
  .rank-position {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    background: var(--gray-100);
    color: var(--gray-600);
  }
  
  .rank-position.top-three {
    background: none;
    font-size: 1.5rem;
  }
  
  .bi-trophy-fill.gold { color: #ffd700; }
  .bi-trophy-fill.silver { color: #c0c0c0; }
  .bi-trophy-fill.bronze { color: #cd7f32; }
  
  .doctor-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
  }
  
  .doctor-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--primary-color), var(--purple-color));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
  }
  
  .doctor-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .doctor-name {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .doctor-specialty {
    color: var(--gray-600);
    font-size: 0.85rem;
  }
  
  .doctor-stats {
    text-align: right;
  }
  
  .stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
  }
  
  .stat-label {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  /* Feed d'activité */
  .activity-feed {
    padding: 1rem 0;
    max-height: 400px;
    overflow-y: auto;
  }
  
  .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1.5rem;
    transition: all 0.2s ease;
  }
  
  .activity-item:hover {
    background: var(--gray-50);
  }
  
  .activity-icon {
    width: 35px;
    height: 35px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
  }
  
  .activity-icon.consultation { background: var(--primary-color); }
  .activity-icon.payment { background: var(--success-color); }
  .activity-icon.user { background: var(--purple-color); }
  .activity-icon.system { background: var(--warning-color); }
  
  .activity-content {
    flex: 1;
  }
  
  .activity-text {
    color: var(--gray-800);
    font-weight: 500;
  }
  
  .activity-time {
    color: var(--gray-600);
    font-size: 0.8rem;
  }
  
  .btn-refresh {
    background: none;
    border: none;
    color: var(--gray-600);
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  
  .btn-refresh:hover {
    background: var(--gray-100);
    color: var(--primary-color);
    transform: rotate(90deg);
  }
  
  /* Section métriques */
  .metrics-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--gray-200);
  }
  
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  
  .section-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .btn-filter {
    background: var(--gray-100);
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-filter:hover {
    background: var(--primary-color);
    color: white;
  }
  
  .filters-panel {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    gap: 2rem;
    align-items: end;
  }
  
  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .filter-group label {
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.9rem;
  }
  
  .btn-apply-filters {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  
  .btn-apply-filters:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
  }
  
  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .metric-card {
    background: var(--gray-50);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
  }
  
  .metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .metric-card h4 {
    margin: 0 0 1rem 0;
    color: var(--gray-600);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .metric-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
  }
  
  .metric-comparison {
    font-size: 0.85rem;
    font-weight: 500;
  }
  
  .metric-comparison .positive { color: var(--success-color); }
  .metric-comparison .negative { color: var(--danger-color); }
  .metric-comparison .neutral { color: var(--gray-600); }
  
  .empty-state, .empty-activity {
    padding: 2rem;
    text-align: center;
    color: var(--gray-600);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  .empty-state i, .empty-activity i {
    font-size: 2rem;
    opacity: 0.5;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .analytics-grid {
      grid-template-columns: 1fr;
    }
    
    .revenue-chart-card {
      grid-column: 1;
    }
  }
  
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }
    
    .header-actions {
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .kpi-grid {
      grid-template-columns: 1fr;
    }
    
    .filters-panel {
      flex-direction: column;
      align-items: stretch;
    }
    
    .metrics-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

{{-- Scripts pour graphiques et animations --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Animation des compteurs KPI
  function animateCounters() {
    document.querySelectorAll('.kpi-value').forEach(counter => {
      const target = parseInt(counter.getAttribute('data-target'));
      const increment = target / 50;
      let current = 0;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        counter.textContent = Math.floor(current).toLocaleString();
      }, 40);
    });
  }
  
  // Graphique des revenus
  function initRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul'],
        datasets: [{
          label: 'Revenus (XOF)',
          data: [120000, 190000, 300000, 250000, 320000, 380000, 420000],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return value.toLocaleString() + ' XOF';
              }
            }
          }
        }
      }
    });
    
    // Gestion des boutons de type de graphique
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-chart-type').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        revenueChart.config.type = this.getAttribute('data-type');
        revenueChart.update();
      });
    });
  }
  
  // Graphique des consultations
  function initConsultationsChart() {
    const ctx = document.getElementById('consultationsChart').getContext('2d');
    const consultationsChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Consultation générale', 'Urgence', 'Spécialisée', 'Suivi'],
        datasets: [{
          data: [45, 25, 20, 10],
          backgroundColor: [
            'rgb(59, 130, 246)',
            'rgb(239, 68, 68)', 
            'rgb(139, 92, 246)',
            'rgb(5, 150, 105)'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
    
    // Génération de la légende personnalisée
    const legend = document.getElementById('consultationsLegend');
    consultationsChart.data.labels.forEach((label, index) => {
      const legendItem = document.createElement('div');
      legendItem.innerHTML = `
        <span style="background: ${consultationsChart.data.datasets[0].backgroundColor[index]}"></span>
        ${label}
      `;
      legend.appendChild(legendItem);
    });
  }
  
  // Fonctions utilitaires
  function exportReport() {
    alert('Fonction d\'export en développement');
  }
  
  function refreshActivity() {
    const btn = document.querySelector('.btn-refresh i');
    btn.style.transform = 'rotate(180deg)';
    setTimeout(() => {
      btn.style.transform = 'rotate(0deg)';
      // Ici on rechargerait les données via AJAX
    }, 1000);
  }
  
  function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
  }
  
  function applyFilters() {
    alert('Application des filtres en cours...');
  }
  
  // Changement de période
  document.getElementById('periodSelector').addEventListener('change', function() {
    // Ici on rechargerait les données selon la période sélectionnée
    console.log('Période changée:', this.value);
  });
  
  // Initialisation au chargement
  document.addEventListener('DOMContentLoaded', function() {
    animateCounters();
    initRevenueChart();
    initConsultationsChart();
  });
</script>
@endsection
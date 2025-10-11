@extends('layouts.app')

@section('content')
{{-- Header moderne pour supervision des rôles --}}
<div class="supervision-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-shield-check"></i>
      <div>
        <span>Supervision des Rôles et Utilisateurs</span>
        <small>Surveillance en temps réel des activités par rôle</small>
      </div>
    </div>
    <div class="header-actions">
      <div class="live-indicator">
        <span class="status-dot"></span>
        <span>En temps réel</span>
      </div>
      <button class="btn-refresh" onclick="refreshData()">
        <i class="bi bi-arrow-clockwise"></i>
        Actualiser
      </button>
      <a href="{{ route('admin.roles.manage') }}" class="action-btn">
        <i class="bi bi-gear"></i>
        Gérer les rôles
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour
      </a>
    </div>
  </div>
</div>

{{-- Vue d'ensemble des rôles --}}
<div class="roles-overview">
  <div class="overview-header">
    <h2><i class="bi bi-people-fill"></i> Vue d'ensemble des rôles</h2>
    <div class="overview-stats">
      <span>{{ $total_users ?? 0 }} utilisateurs actifs</span>
      <span>{{ $total_roles ?? 4 }} rôles configurés</span>
    </div>
  </div>
  
  <div class="roles-grid">
    {{-- Admin --}}
    <div class="role-card admin-card">
      <div class="role-header">
        <div class="role-icon">
          <i class="bi bi-shield-fill"></i>
        </div>
        <div class="role-info">
          <h3>Administrateurs</h3>
          <span class="role-count">{{ $roles['admin']['count'] ?? 2 }} utilisateurs</span>
        </div>
        <div class="role-status active">
          <i class="bi bi-circle-fill"></i>
        </div>
      </div>
      
      <div class="role-body">
        <div class="role-metrics">
          <div class="metric">
            <span class="metric-value">{{ $roles['admin']['online'] ?? 1 }}</span>
            <span class="metric-label">En ligne</span>
          </div>
          <div class="metric">
            <span class="metric-value">{{ $roles['admin']['actions_today'] ?? 15 }}</span>
            <span class="metric-label">Actions aujourd'hui</span>
          </div>
        </div>
        
        <div class="recent-activity">
          <h5>Activité récente:</h5>
          <div class="activity-list">
            @forelse($roles['admin']['recent_activities'] ?? [] as $activity)
              <div class="activity-item">
                <span class="activity-user">{{ $activity['user'] }}</span>
                <span class="activity-action">{{ $activity['action'] }}</span>
                <small class="activity-time">{{ $activity['time'] }}</small>
              </div>
            @empty
              <small class="text-muted">Aucune activité récente</small>
            @endforelse
          </div>
        </div>
      </div>
      
      <div class="role-actions">
        <button class="btn-details" onclick="viewRoleDetails('admin')">
          <i class="bi bi-eye"></i>
          Détails
        </button>
        <button class="btn-monitor" onclick="monitorRole('admin')">
          <i class="bi bi-activity"></i>
          Surveillance
        </button>
      </div>
    </div>
    
    {{-- Médecins --}}
    <div class="role-card doctor-card">
      <div class="role-header">
        <div class="role-icon">
          <i class="bi bi-person-badge"></i>
        </div>
        <div class="role-info">
          <h3>Médecins</h3>
          <span class="role-count">{{ $roles['doctor']['count'] ?? 8 }} utilisateurs</span>
        </div>
        <div class="role-status active">
          <i class="bi bi-circle-fill"></i>
        </div>
      </div>
      
      <div class="role-body">
        <div class="role-metrics">
          <div class="metric">
            <span class="metric-value">{{ $roles['doctor']['online'] ?? 5 }}</span>
            <span class="metric-label">En ligne</span>
          </div>
          <div class="metric">
            <span class="metric-value">{{ $roles['doctor']['consultations_today'] ?? 34 }}</span>
            <span class="metric-label">Consultations</span>
          </div>
        </div>
        
        <div class="performance-bar">
          <div class="bar-header">
            <span>Performance moyenne</span>
            <span class="percentage">{{ $roles['doctor']['performance'] ?? 87 }}%</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $roles['doctor']['performance'] ?? 87 }}%"></div>
          </div>
        </div>
      </div>
      
      <div class="role-actions">
        <button class="btn-details" onclick="viewRoleDetails('doctor')">
          <i class="bi bi-eye"></i>
          Détails
        </button>
        <button class="btn-schedule" onclick="viewSchedules('doctor')">
          <i class="bi bi-calendar"></i>
          Planning
        </button>
      </div>
    </div>
    
    {{-- Secrétaires --}}
    <div class="role-card secretary-card">
      <div class="role-header">
        <div class="role-icon">
          <i class="bi bi-person-workspace"></i>
        </div>
        <div class="role-info">
          <h3>Secrétaires</h3>
          <span class="role-count">{{ $roles['secretary']['count'] ?? 4 }} utilisateurs</span>
        </div>
        <div class="role-status active">
          <i class="bi bi-circle-fill"></i>
        </div>
      </div>
      
      <div class="role-body">
        <div class="role-metrics">
          <div class="metric">
            <span class="metric-value">{{ $roles['secretary']['online'] ?? 3 }}</span>
            <span class="metric-label">En ligne</span>
          </div>
          <div class="metric">
            <span class="metric-value">{{ $roles['secretary']['payments_today'] ?? 12 }}</span>
            <span class="metric-label">Paiements</span>
          </div>
        </div>
        
        <div class="workload-indicator">
          <span>Charge de travail: </span>
          <span class="workload-level {{ ($roles['secretary']['workload'] ?? 'normal') }}">
            {{ ucfirst($roles['secretary']['workload'] ?? 'normal') }}
          </span>
        </div>
      </div>
      
      <div class="role-actions">
        <button class="btn-details" onclick="viewRoleDetails('secretary')">
          <i class="bi bi-eye"></i>
          Détails
        </button>
        <button class="btn-payments" onclick="viewPayments('secretary')">
          <i class="bi bi-credit-card"></i>
          Paiements
        </button>
      </div>
    </div>
    
    {{-- Patients --}}
    <div class="role-card patient-card">
      <div class="role-header">
        <div class="role-icon">
          <i class="bi bi-person"></i>
        </div>
        <div class="role-info">
          <h3>Patients</h3>
          <span class="role-count">{{ $roles['patient']['count'] ?? 156 }} utilisateurs</span>
        </div>
        <div class="role-status active">
          <i class="bi bi-circle-fill"></i>
        </div>
      </div>
      
      <div class="role-body">
        <div class="role-metrics">
          <div class="metric">
            <span class="metric-value">{{ $roles['patient']['online'] ?? 12 }}</span>
            <span class="metric-label">En ligne</span>
          </div>
          <div class="metric">
            <span class="metric-value">{{ $roles['patient']['new_today'] ?? 3 }}</span>
            <span class="metric-label">Nouveaux</span>
          </div>
        </div>
        
        <div class="satisfaction-score">
          <span>Satisfaction: </span>
          <div class="score">
            <span class="score-value">{{ $roles['patient']['satisfaction'] ?? 4.2 }}/5</span>
            <div class="stars">
              @for($i = 1; $i <= 5; $i++)
                <i class="bi {{ $i <= ($roles['patient']['satisfaction'] ?? 4) ? 'bi-star-fill' : 'bi-star' }}"></i>
              @endfor
            </div>
          </div>
        </div>
      </div>
      
      <div class="role-actions">
        <button class="btn-details" onclick="viewRoleDetails('patient')">
          <i class="bi bi-eye"></i>
          Détails
        </button>
        <button class="btn-feedback" onclick="viewFeedback('patient')">
          <i class="bi bi-chat-text"></i>
          Retours
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Surveillance en temps réel --}}
<div class="monitoring-section">
  <div class="section-header">
    <h2><i class="bi bi-activity"></i> Surveillance en temps réel</h2>
    <div class="monitoring-controls">
      <label class="switch">
        <input type="checkbox" id="autoRefresh" checked>
        <span class="slider"></span>
      </label>
      <span>Auto-actualisation</span>
    </div>
  </div>
  
  <div class="monitoring-grid">
    {{-- Graphique des connexions --}}
    <div class="monitor-card connections-chart">
      <div class="card-header">
        <h4><i class="bi bi-graph-up"></i> Connexions par heure</h4>
        <span class="current-time" id="currentTime"></span>
      </div>
      <div class="chart-container">
        <canvas id="connectionsChart" height="200"></canvas>
      </div>
    </div>
    
    {{-- Feed d'activité en temps réel --}}
    <div class="monitor-card activity-feed-card">
      <div class="card-header">
        <h4><i class="bi bi-list-ul"></i> Flux d'activité</h4>
        <button class="btn-clear" onclick="clearActivityFeed()">
          <i class="bi bi-trash"></i>
        </button>
      </div>
      <div class="activity-feed" id="realtimeActivity">
        @forelse($realtime_activities ?? [] as $activity)
          <div class="activity-item {{ $activity['type'] }}">
            <div class="activity-avatar">{{ substr($activity['user'], 0, 1) }}</div>
            <div class="activity-content">
              <span class="activity-user">{{ $activity['user'] }}</span>
              <span class="activity-text">{{ $activity['action'] }}</span>
              <small class="activity-timestamp">{{ $activity['timestamp'] }}</small>
            </div>
            <div class="activity-role {{ $activity['role'] }}">
              {{ ucfirst($activity['role']) }}
            </div>
          </div>
        @empty
          <div class="no-activity">
            <i class="bi bi-clock"></i>
            <span>En attente d'activité...</span>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Alertes et notifications --}}
<div class="alerts-section">
  <div class="section-header">
    <h2><i class="bi bi-exclamation-triangle"></i> Alertes et notifications</h2>
    <button class="btn-settings" onclick="configureAlerts()">
      <i class="bi bi-gear"></i>
      Configurer
    </button>
  </div>
  
  <div class="alerts-grid">
    @forelse($alerts ?? [] as $alert)
      <div class="alert-item {{ $alert['severity'] }}">
        <div class="alert-icon">
          <i class="bi {{ $alert['icon'] }}"></i>
        </div>
        <div class="alert-content">
          <h5>{{ $alert['title'] }}</h5>
          <p>{{ $alert['message'] }}</p>
          <small>{{ $alert['time'] }}</small>
        </div>
        <div class="alert-actions">
          <button class="btn-resolve" onclick="resolveAlert('{{ $alert['id'] }}')">
            <i class="bi bi-check"></i>
          </button>
          <button class="btn-dismiss" onclick="dismissAlert('{{ $alert['id'] }}')">
            <i class="bi bi-x"></i>
          </button>
        </div>
      </div>
    @empty
      <div class="no-alerts">
        <i class="bi bi-shield-check"></i>
        <span>Aucune alerte active</span>
        <small>Tout fonctionne normalement</small>
      </div>
    @endforelse
  </div>
</div>

{{-- Modal pour détails des rôles --}}
<div class="modal fade" id="roleDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Détails du rôle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="roleDetailsContent">
        <!-- Contenu généré dynamiquement -->
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
    --admin-color: #dc2626;
    --doctor-color: #059669;
    --secretary-color: #8b5cf6;
    --patient-color: #3b82f6;
  }
  
  /* Conteneur principal */
  body > .container { max-width: 1600px !important; }
  
  /* Header supervision */
  .supervision-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.15);
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
  
  .live-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    font-weight: 500;
  }
  
  .status-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
  
  .btn-refresh, .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-refresh:hover, .action-btn:hover {
    background: white;
    color: #8b5cf6;
    transform: translateY(-2px);
  }
  
  /* Vue d'ensemble des rôles */
  .roles-overview {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .overview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  
  .overview-header h2 {
    margin: 0;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .overview-stats {
    display: flex;
    gap: 2rem;
    color: #6b7280;
    font-weight: 500;
  }
  
  .roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
  }
  
  .role-card {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .role-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .admin-card:hover { border-color: var(--admin-color); }
  .doctor-card:hover { border-color: var(--doctor-color); }
  .secretary-card:hover { border-color: var(--secretary-color); }
  .patient-card:hover { border-color: var(--patient-color); }
  
  .role-header {
    background: white;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .role-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
  }
  
  .admin-card .role-icon { background: linear-gradient(135deg, var(--admin-color), #b91c1c); }
  .doctor-card .role-icon { background: linear-gradient(135deg, var(--doctor-color), #047857); }
  .secretary-card .role-icon { background: linear-gradient(135deg, var(--secretary-color), #7c3aed); }
  .patient-card .role-icon { background: linear-gradient(135deg, var(--patient-color), #1d4ed8); }
  
  .role-info {
    flex: 1;
  }
  
  .role-info h3 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  .role-count {
    color: #6b7280;
    font-size: 0.9rem;
  }
  
  .role-status {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #16a34a;
  }
  
  .role-body {
    padding: 1.5rem;
  }
  
  .role-metrics {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
  }
  
  .metric {
    text-align: center;
  }
  
  .metric-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
  }
  
  .metric-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 500;
  }
  
  .recent-activity h5 {
    margin: 0 0 1rem 0;
    color: #374151;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  .activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
    font-size: 0.85rem;
  }
  
  .activity-user {
    font-weight: 500;
    color: #374151;
  }
  
  .activity-action {
    color: #6b7280;
  }
  
  .activity-time {
    color: #9ca3af;
    font-size: 0.75rem;
  }
  
  .performance-bar {
    margin-bottom: 1rem;
  }
  
  .bar-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
  }
  
  .percentage {
    font-weight: 600;
    color: var(--success-color);
  }
  
  .progress-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
  }
  
  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-color), #10b981);
    border-radius: 4px;
    transition: width 0.5s ease;
  }
  
  .workload-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .workload-level {
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
  }
  
  .workload-level.normal {
    background: #dcfce7;
    color: #166534;
  }
  
  .workload-level.high {
    background: #fef3c7;
    color: #92400e;
  }
  
  .satisfaction-score {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .score {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .score-value {
    font-weight: 600;
    color: #f59e0b;
  }
  
  .stars {
    display: flex;
    gap: 0.125rem;
    color: #f59e0b;
    font-size: 0.8rem;
  }
  
  .role-actions {
    padding: 1rem 1.5rem;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 1rem;
  }
  
  .role-actions button {
    flex: 1;
    padding: 0.6rem;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-details {
    background: #f3f4f6;
    color: #374151;
  }
  
  .btn-details:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
  }
  
  .btn-monitor, .btn-schedule, .btn-payments, .btn-feedback {
    color: white;
  }
  
  .admin-card .btn-monitor { background: var(--admin-color); }
  .doctor-card .btn-schedule { background: var(--doctor-color); }
  .secretary-card .btn-payments { background: var(--secretary-color); }
  .patient-card .btn-feedback { background: var(--patient-color); }
  
  .btn-monitor:hover, .btn-schedule:hover, .btn-payments:hover, .btn-feedback:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  /* Section de surveillance */
  .monitoring-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  
  .section-header h2 {
    margin: 0;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .monitoring-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  /* Switch pour auto-actualisation */
  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
  }
  
  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 24px;
    transition: .4s;
  }
  
  .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: .4s;
  }
  
  input:checked + .slider {
    background-color: var(--success-color);
  }
  
  input:checked + .slider:before {
    transform: translateX(26px);
  }
  
  .monitoring-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
  
  .monitor-card {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
  }
  
  .card-header {
    padding: 1.5rem;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .card-header h4 {
    margin: 0;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .current-time {
    font-family: monospace;
    color: #6b7280;
    font-size: 0.9rem;
  }
  
  .chart-container {
    padding: 1.5rem;
  }
  
  .activity-feed {
    padding: 1rem 0;
    max-height: 400px;
    overflow-y: auto;
  }
  
  .activity-feed .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
  }
  
  .activity-feed .activity-item:hover {
    background: white;
  }
  
  .activity-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  .activity-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .activity-user {
    font-weight: 600;
    color: #1f2937;
  }
  
  .activity-text {
    color: #6b7280;
    font-size: 0.9rem;
  }
  
  .activity-timestamp {
    color: #9ca3af;
    font-size: 0.75rem;
  }
  
  .activity-role {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
  }
  
  .activity-role.admin {
    background: #fee2e2;
    color: var(--admin-color);
  }
  
  .activity-role.doctor {
    background: #dcfce7;
    color: var(--doctor-color);
  }
  
  .activity-role.secretary {
    background: #f3e8ff;
    color: var(--secretary-color);
  }
  
  .activity-role.patient {
    background: #dbeafe;
    color: var(--patient-color);
  }
  
  .btn-clear {
    background: none;
    border: none;
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  
  .btn-clear:hover {
    background: #f3f4f6;
    color: var(--danger-color);
  }
  
  .no-activity {
    padding: 2rem;
    text-align: center;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  /* Section des alertes */
  .alerts-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .btn-settings {
    background: #f3f4f6;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    color: #374151;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-settings:hover {
    background: #e5e7eb;
  }
  
  .alerts-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid;
  }
  
  .alert-item.warning {
    background: #fffbeb;
    border-color: var(--warning-color);
  }
  
  .alert-item.danger {
    background: #fef2f2;
    border-color: var(--danger-color);
  }
  
  .alert-item.info {
    background: #eff6ff;
    border-color: var(--primary-color);
  }
  
  .alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .alert-item.warning .alert-icon { background: var(--warning-color); }
  .alert-item.danger .alert-icon { background: var(--danger-color); }
  .alert-item.info .alert-icon { background: var(--primary-color); }
  
  .alert-content {
    flex: 1;
  }
  
  .alert-content h5 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  .alert-content p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
  }
  
  .alert-content small {
    color: #9ca3af;
    font-size: 0.8rem;
  }
  
  .alert-actions {
    display: flex;
    gap: 0.5rem;
  }
  
  .btn-resolve, .btn-dismiss {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 0.9rem;
  }
  
  .btn-resolve {
    background: #dcfce7;
    color: var(--success-color);
  }
  
  .btn-resolve:hover {
    background: var(--success-color);
    color: white;
  }
  
  .btn-dismiss {
    background: #f3f4f6;
    color: #6b7280;
  }
  
  .btn-dismiss:hover {
    background: #fee2e2;
    color: var(--danger-color);
  }
  
  .no-alerts {
    padding: 3rem;
    text-align: center;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  .no-alerts i {
    font-size: 3rem;
    color: var(--success-color);
    margin-bottom: 1rem;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .monitoring-grid {
      grid-template-columns: 1fr;
    }
    
    .roles-grid {
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
    
    .overview-header {
      flex-direction: column;
      align-items: start;
      gap: 1rem;
    }
    
    .roles-grid {
      grid-template-columns: 1fr;
    }
    
    .role-actions {
      flex-direction: column;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Variables globales
  let connectionsChart;
  let autoRefreshInterval;
  
  // Initialisation des graphiques
  function initConnectionsChart() {
    const ctx = document.getElementById('connectionsChart').getContext('2d');
    connectionsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
        datasets: [{
          label: 'Connexions',
          data: [5, 2, 15, 25, 30, 12],
          borderColor: '#8b5cf6',
          backgroundColor: 'rgba(139, 92, 246, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#8b5cf6',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2
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
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
  
  // Mise à jour de l'heure
  function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR', { 
      hour: '2-digit', 
      minute: '2-digit', 
      second: '2-digit' 
    });
    document.getElementById('currentTime').textContent = timeString;
  }
  
  // Actualisation des données
  function refreshData() {
    const btn = document.querySelector('.btn-refresh');
    const icon = btn.querySelector('i');
    
    icon.style.transform = 'rotate(360deg)';
    icon.style.transition = 'transform 0.5s ease';
    
    setTimeout(() => {
      icon.style.transform = 'rotate(0deg)';
      // Ici on rechargerait les données via AJAX
      console.log('Données actualisées');
    }, 500);
  }
  
  // Fonctions pour les cartes de rôles
  function viewRoleDetails(role) {
    // Simulation du contenu modal
    const content = `
      <h6>Détails du rôle: ${role}</h6>
      <div class="role-details-content">
        <p>Informations détaillées sur le rôle ${role}</p>
        <div class="permissions-list">
          <h6>Permissions:</h6>
          <ul>
            <li>Permission 1</li>
            <li>Permission 2</li>
            <li>Permission 3</li>
          </ul>
        </div>
      </div>
    `;
    
    document.getElementById('roleDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('roleDetailsModal')).show();
  }
  
  function monitorRole(role) {
    alert(`Surveillance du rôle ${role} activée`);
  }
  
  function viewSchedules(role) {
    alert(`Affichage des plannings pour ${role}`);
  }
  
  function viewPayments(role) {
    alert(`Affichage des paiements pour ${role}`);
  }
  
  function viewFeedback(role) {
    alert(`Affichage des retours pour ${role}`);
  }
  
  // Gestion des alertes
  function configureAlerts() {
    alert('Configuration des alertes');
  }
  
  function resolveAlert(alertId) {
    alert(`Alerte ${alertId} résolue`);
  }
  
  function dismissAlert(alertId) {
    alert(`Alerte ${alertId} ignorée`);
  }
  
  function clearActivityFeed() {
    if(confirm('Vider le flux d\'activité ?')) {
      document.getElementById('realtimeActivity').innerHTML = `
        <div class="no-activity">
          <i class="bi bi-clock"></i>
          <span>En attente d'activité...</span>
        </div>
      `;
    }
  }
  
  // Gestion de l'auto-actualisation
  function toggleAutoRefresh() {
    const checkbox = document.getElementById('autoRefresh');
    
    if (checkbox.checked) {
      autoRefreshInterval = setInterval(() => {
        refreshData();
        updateCurrentTime();
      }, 30000); // 30 secondes
    } else {
      clearInterval(autoRefreshInterval);
    }
  }
  
  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    initConnectionsChart();
    updateCurrentTime();
    
    // Mise à jour de l'heure chaque seconde
    setInterval(updateCurrentTime, 1000);
    
    // Gestion de l'auto-actualisation
    document.getElementById('autoRefresh').addEventListener('change', toggleAutoRefresh);
    toggleAutoRefresh(); // Démarre l'auto-actualisation si activée
  });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Journal d\'Audit')

@push('styles')
<style>
/* Styles simples pour la page d'audit */
.audit-header {
  background: linear-gradient(135deg, #27ae60, #52b788);
  color: white;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
}

.audit-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.audit-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

.kpi-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.kpi-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.kpi-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.kpi-number {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.kpi-label {
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #666;
}

.kpi-card.info .kpi-number { color: #3498db; }
.kpi-card.success .kpi-number { color: #27ae60; }
.kpi-card.warning .kpi-number { color: #f39c12; }
.kpi-card.danger .kpi-number { color: #e74c3c; }

/* Filtres */
.filters-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Table */
.audit-table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.audit-table th {
  background: #f8f9fa;
  font-weight: 600;
  border: none;
  padding: 1rem;
}

.audit-table td {
  border: none;
  padding: 1rem;
  vertical-align: middle;
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
}

/* Responsive */
@media (max-width: 768px) {
  .audit-title {
    font-size: 1.8rem;
  }
  
  .kpi-cards-grid {
    grid-template-columns: 1fr;
  }
}

</style>
@endpush

@section('content')
<div class="container mt-4">
  {{-- Header --}}
  <div class="audit-header">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="audit-title mb-0">
          <i class="bi bi-shield-check me-3"></i>
          Journal d'Audit
        </h1>
        <p class="audit-subtitle mb-0">Surveillance des activités système</p>
      </div>
      <div>
        <button class="btn btn-success me-2" title="Exporter" onclick="exportAuditLogs()">
          <i class="bi bi-download me-2"></i>Exporter
        </button>
        <button class="btn btn-warning" title="Nettoyer" data-bs-toggle="modal" data-bs-target="#cleanupModal">
          <i class="bi bi-trash3 me-2"></i>Nettoyer
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
          <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
      </div>
    </div>
  </div>

  {{-- KPI Cards --}}
  <div class="kpi-cards-grid">
    <div class="kpi-card info">
      <i class="bi bi-activity" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
      <div class="kpi-number">{{ number_format($kpiStats['total_today'] ?? 0) }}</div>
      <div class="kpi-label">Activités Aujourd'hui</div>
    </div>
    
    <div class="kpi-card success">
      <i class="bi bi-calendar-week" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
      <div class="kpi-number">{{ number_format($kpiStats['total_week'] ?? 0) }}</div>
      <div class="kpi-label">Cette Semaine</div>
    </div>
    
    <div class="kpi-card {{ ($kpiStats['critical_last_24h'] ?? 0) > 0 ? 'danger' : 'success' }}">
      <i class="bi bi-{{ ($kpiStats['critical_last_24h'] ?? 0) > 0 ? 'exclamation-triangle' : 'shield-check' }}" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
      <div class="kpi-number">{{ $kpiStats['critical_last_24h'] ?? 0 }}</div>
      <div class="kpi-label">Alertes Critiques</div>
    </div>
    
    <div class="kpi-card warning">
      <i class="bi bi-people" style="font-size: 2rem; opacity: 0.3; float: right;"></i>
      <div class="kpi-number">{{ $kpiStats['unique_users_today'] ?? 0 }}</div>
      <div class="kpi-label">Utilisateurs Actifs</div>
    </div>
  </div>

  {{-- Alertes d'activités suspectes --}}
  @if(isset($suspiciousActivities) && count($suspiciousActivities) > 0)
    <div class="alert alert-warning">
      <h6><i class="bi bi-shield-exclamation me-2"></i>Activités Suspectes Détectées</h6>
      @foreach($suspiciousActivities as $activity)
        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-2">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-{{ $activity['severity'] === 'critical' ? 'lightning' : 'exclamation-triangle' }} text-warning"></i>
            <span>{{ $activity['description'] }}</span>
          </div>
          <span class="badge bg-{{ $activity['severity'] === 'critical' ? 'danger' : 'warning' }}">
            {{ ucfirst($activity['severity']) }}
          </span>
        </div>
      @endforeach
    </div>
  @endif

  {{-- Section de filtres --}}
  <div class="filters-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5>
        <i class="bi bi-funnel me-2"></i>Filtres & Recherche
      </h5>
      <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="true">
        <i class="bi bi-chevron-down"></i>
      </button>
    </div>
    
    <div class="collapse show" id="advancedFilters">
      <form method="GET" id="auditFiltersForm">
        <div class="row g-3">
          <div class="col-lg-4">
            <label class="form-label">
              <i class="bi bi-search me-2"></i>Recherche
            </label>
            <input type="text" name="search" value="{{ $validated['search'] ?? '' }}" 
                   class="form-control" 
                   placeholder="Actions, utilisateurs, IPs...">
          </div>
          
          <div class="col-lg-4">
            <label class="form-label">
              <i class="bi bi-person-circle me-2"></i>Utilisateur
            </label>
            <select name="user_id" class="form-select">
              <option value="">Tous les utilisateurs</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ ($validated['user_id'] ?? '') == $u->id ? 'selected' : '' }}>
                  {{ $u->name }}
                </option>
              @endforeach
            </select>
          </div>
          
          <div class="col-lg-4">
            <label class="form-label">
              <i class="bi bi-tags me-2"></i>Type d'Événement
            </label>
            <select name="event_type" class="form-select">
              <option value="">Tous les types</option>
              @foreach($eventTypes as $key => $label)
                <option value="{{ $key }}" {{ ($validated['event_type'] ?? '') == $key ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>
          
          <div class="col-lg-3">
            <label class="form-label">
              <i class="bi bi-shield-exclamation me-2"></i>Sévérité
            </label>
            <select name="severity" class="form-select">
              <option value="">Tous les niveaux</option>
              @foreach($severities as $key => $label)
                <option value="{{ $key }}" {{ ($validated['severity'] ?? '') == $key ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>
          
          <div class="col-lg-3">
            <label class="form-label">
              <i class="bi bi-globe me-2"></i>Adresse IP
            </label>
            <input type="text" name="ip_address" value="{{ $validated['ip_address'] ?? '' }}" 
                   class="form-control" 
                   placeholder="192.168.1.1">
          </div>
          
          <div class="col-lg-3">
            <label class="form-label">
              <i class="bi bi-calendar-range me-2"></i>Date de début
            </label>
            <input type="date" name="date_from" value="{{ $validated['date_from'] ?? '' }}" 
                   class="form-control">
          </div>
          
          <div class="col-lg-3">
            <label class="form-label">
              <i class="bi bi-calendar-range me-2"></i>Date de fin
            </label>
            <input type="date" name="date_to" value="{{ $validated['date_to'] ?? '' }}" 
                   class="form-control">
          </div>
        </div>
        
        <div class="mt-3">
          <button type="submit" class="btn btn-primary me-2" id="searchBtn">
            <i class="bi bi-search me-2"></i>Rechercher
          </button>
          
          <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser
          </a>
          
          <button type="submit" name="export" value="1" class="btn btn-success me-2">
            <i class="bi bi-download me-2"></i>Exporter CSV
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Section des résultats --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5>
      <i class="bi bi-list-check me-2"></i>
      Résultats 
      <span class="badge bg-primary ms-2">{{ number_format($logs->total()) }} entrées</span>
    </h5>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary btn-sm" onclick="refreshAuditData()" title="Actualiser">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#" onclick="selectAllLogs()"><i class="bi bi-check-all me-2"></i>Tout sélectionner</a></li>
          <li><a class="dropdown-item" href="#" onclick="exportSelected()"><i class="bi bi-download me-2"></i>Exporter sélection</a></li>
          <li><a class="dropdown-item" href="#" onclick="printResults()"><i class="bi bi-printer me-2"></i>Imprimer</a></li>
        </ul>
      </div>
    </div>
  </div>
  
  <div class="audit-table">
    <table class="table table-hover" id="auditTable">
      <thead>
        <tr>
          <th width="5%">
            <input type="checkbox" id="selectAll" class="form-check-input">
          </th>
          <th width="15%">Date & Heure</th>
          <th width="20%">Utilisateur</th>
          <th width="15%">Action</th>
          <th width="10%">Sévérité</th>
          <th width="10%">IP</th>
          <th width="15%">Changements</th>
          <th width="10%" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          @php
            $user = $log->user;
            $severityColors = [
              'low' => 'success',
              'medium' => 'warning', 
              'high' => 'danger',
              'critical' => 'dark'
            ];
            $eventTypeIcons = [
              'create' => 'plus-circle',
              'update' => 'pencil-square',
              'delete' => 'trash',
              'login' => 'box-arrow-in-right',
              'logout' => 'box-arrow-left',
              'view' => 'eye',
              'export' => 'download'
            ];
          @endphp
          <tr data-log-id="{{ $log->id }}">
            <td>
              <input type="checkbox" class="form-check-input log-checkbox" value="{{ $log->id }}">
            </td>
            <td>
              <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
              <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
            </td>
            <td>
              @if($user)
                <div class="d-flex align-items-center">
                  <img src="https://ui-avatars.com/api/?size=32&name={{ urlencode($user->name) }}&background=27ae60&color=fff" 
                       alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                  <div>
                    <div class="fw-bold">{{ $user->name }}</div>
                    <small class="text-muted">{{ ucfirst($user->role ?? 'Unknown') }}</small>
                  </div>
                </div>
              @else
                <div class="text-muted">Utilisateur supprimé</div>
              @endif
            </td>
            <td>
              <span class="badge bg-{{ $severityColors[$log->event_type] ?? 'secondary' }}">
                <i class="bi bi-{{ $eventTypeIcons[$log->event_type] ?? 'question-circle' }} me-1"></i>
                {{ $log->getEventTypeLabel() }}
              </span>
            </td>
            <td>
              <span class="badge bg-{{ $severityColors[$log->severity] ?? 'secondary' }}">
                @switch($log->severity)
                  @case('low')<i class="bi bi-check-circle me-1"></i> @break
                  @case('medium')<i class="bi bi-exclamation-circle me-1"></i> @break
                  @case('high')<i class="bi bi-exclamation-triangle me-1"></i> @break
                  @case('critical')<i class="bi bi-lightning me-1"></i> @break
                @endswitch
                {{ $log->getSeverityLabel() }}
              </span>
            </td>
            <td>
              @if($log->ip_address)
                <code class="small">{{ $log->ip_address }}</code>
              @else
                <span class="text-muted small">Non enregistrée</span>
              @endif
            </td>
            <td>
              @if($log->changes && count($log->getFormattedChanges()) > 0)
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changesModal{{ $log->id }}">
                  <i class="bi bi-file-diff me-1"></i>{{ count($log->getFormattedChanges()) }}
                </button>
                
                {{-- Modal des changements --}}
                <div class="modal fade" id="changesModal{{ $log->id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          <i class="bi bi-file-diff me-2"></i>Détails des Changements
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        @foreach($log->getFormattedChanges() as $field => $change)
                          <div class="mb-3">
                            <h6 class="fw-bold">{{ $change['label'] }}</h6>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="p-2 bg-light rounded">
                                  <strong>Avant:</strong><br>
                                  <code>{{ $change['old'] ?? 'Vide' }}</code>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="p-2 bg-success bg-opacity-10 rounded">
                                  <strong>Après:</strong><br>
                                  <code>{{ $change['new'] ?? 'Vide' }}</code>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <span class="text-muted small">Aucun</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-outline-primary btn-sm" onclick="viewLogDetails({{ $log->id }})" title="Détails">
                <i class="bi bi-eye"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <div>
                <i class="bi bi-search text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="text-muted mt-3">Aucune entrée trouvée</h5>
                <p class="text-muted">Aucun log ne correspond aux critères.</p>
                <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-primary">
                  <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    
    {{-- Pagination --}}
      {{ $logs->appends(request()->query())->links() }}
    </div>
  </div>
</div>

{{-- Modal de Nettoyage --}}
<div class="modal fade" id="cleanupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-trash3 me-2"></i>Nettoyage des Logs
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.audit.cleanup') }}">
        @csrf
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Attention :</strong> Cette action supprimera définitivement les logs anciens.
          </div>
          
          <div class="mb-3">
            <label for="cleanup_days" class="form-label">Conserver les logs des derniers (jours) :</label>
            <input type="number" name="days" id="cleanup_days" class="form-control" value="90" min="1" max="365" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Annuler
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash3 me-1"></i>Nettoyer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Fonctions simples pour la page d'audit
function selectAllLogs() {
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const selectAll = document.getElementById('selectAll');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function exportAuditLogs() {
    window.location.href = '{{ route("admin.audit.index") }}?export=1';
}

function refreshAuditData() {
    location.reload();
}

function exportSelected() {
    const selected = [];
    document.querySelectorAll('.log-checkbox:checked').forEach(cb => {
        selected.push(cb.value);
    });
    if (selected.length === 0) {
        alert('Aucune entrée sélectionnée');
        return;
    }
    // Implementation de l'export des éléments sélectionnés
    console.log('Export selected:', selected);
}

function printResults() {
    window.print();
}

function viewLogDetails(logId) {
    // Implementation de la vue détaillée
    console.log('View details for log:', logId);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', selectAllLogs);
    }
});
</script>
@endpush

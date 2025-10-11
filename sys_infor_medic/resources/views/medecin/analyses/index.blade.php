@extends('layouts.app')

@section('content')
{{-- Header moderne pour analyses médicales --}}
<div class="analyses-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-activity"></i>
      <span>Analyses Médicales</span>
    </div>
    <div class="header-actions">
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="analysesSearch" placeholder="Rechercher une analyse..." class="form-control">
      </div>
      <a href="{{ route('medecin.analyses.create') }}" class="action-btn">
        <i class="bi bi-plus-circle"></i>
        Nouvelle Analyse
      </a>
      <a href="{{ route('medecin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour Dashboard
      </a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

{{-- Statistiques d'analyses --}}
<div class="stats-grid mb-4">
  <div class="stat-card stat-total">
    <div class="stat-icon">
      <i class="bi bi-clipboard-data"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['total'] }}</div>
      <div class="stat-label">Total analyses</div>
    </div>
  </div>
  
  <div class="stat-card stat-month">
    <div class="stat-icon">
      <i class="bi bi-calendar-month"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['ce_mois'] }}</div>
      <div class="stat-label">Ce mois</div>
    </div>
  </div>
  
  <div class="stat-card stat-pending">
    <div class="stat-icon">
      <i class="bi bi-hourglass-split"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['en_attente'] ?? 0 }}</div>
      <div class="stat-label">En attente</div>
    </div>
  </div>
  
  <div class="stat-card stat-completed">
    <div class="stat-icon">
      <i class="bi bi-check-circle"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $stats['terminees'] }}</div>
      <div class="stat-label">Terminées</div>
    </div>
  </div>
</div>

{{-- Filtres et export modernes --}}
<div class="filters-container">
  <div class="filters-header">
    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtres et Export</h5>
  </div>
  <div class="filters-body">
    <form method="GET" action="{{ route('medecin.analyses.index') }}" class="filters-form">
      <div class="filters-row">
        <div class="form-group">
          <label for="patient_id" class="form-label">Patient</label>
          <select name="patient_id" id="patient_id" class="form-control">
            <option value="">Tous les patients</option>
            @foreach($patients as $patient)
              <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                {{ $patient->nom }} {{ $patient->prenom }}
              </option>
            @endforeach
          </select>
        </div>
        
        <div class="form-group">
          <label for="type_analyse" class="form-label">Type d'analyse</label>
          <input type="text" name="type_analyse" id="type_analyse" class="form-control" 
                 value="{{ request('type_analyse') }}" placeholder="ex: Hémogramme, Glycémie...">
        </div>
        
        <div class="form-group">
          <label for="date_debut" class="form-label">Période du</label>
          <input type="date" name="date_debut" id="date_debut" class="form-control" 
                 value="{{ request('date_debut') }}">
        </div>
        
        <div class="form-group">
          <label for="date_fin" class="form-label">Au</label>
          <input type="date" name="date_fin" id="date_fin" class="form-control" 
                 value="{{ request('date_fin') }}">
        </div>
      </div>
      
      <div class="filters-actions">
        <button type="submit" class="btn-filter">
          <i class="bi bi-search"></i>
          Filtrer
        </button>
        
        <a href="{{ route('medecin.analyses.index') }}" class="btn-reset">
          <i class="bi bi-arrow-clockwise"></i>
          Réinitialiser
        </a>
      </div>
    </form>
    
    <div class="export-section">
      <h6><i class="bi bi-download me-2"></i>Exporter les résultats</h6>
      <div class="export-buttons">
        <a href="{{ route('medecin.analyses.export.csv') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
           class="btn-export btn-export-csv">
          <i class="bi bi-file-earmark-spreadsheet"></i>
          Export CSV
        </a>
        <a href="{{ route('medecin.analyses.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
           class="btn-export btn-export-pdf">
          <i class="bi bi-file-earmark-pdf"></i>
          Export PDF
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Liste des analyses modernes --}}
<div class="analyses-container">
  <div class="analyses-header-section">
    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des analyses ({{ $analyses->total() }} résultats)</h5>
    <div class="analyses-count-info">
      <span class="count-badge">{{ $analyses->total() }}</span>
    </div>
  </div>
  
  @if($analyses->isEmpty())
    <div class="empty-state">
      <i class="bi bi-clipboard-x"></i>
      <h5>Aucune analyse pour le moment</h5>
      <p>Les analyses médicales que vous effectuerez apparaîtront ici.</p>
      <a href="{{ route('medecin.analyses.create') }}" class="btn-create-first">
        <i class="bi bi-plus-circle me-2"></i>
        Créer votre première analyse
      </a>
    </div>
  @else
    <div class="analyses-list">
      <div class="table-responsive">
        <table class="table analyses-table">
          <thead>
            <tr>
              <th><i class="bi bi-calendar3 me-1"></i>Date</th>
              <th><i class="bi bi-person me-1"></i>Patient</th>
              <th><i class="bi bi-clipboard-pulse me-1"></i>Type d'analyse</th>
              <th><i class="bi bi-hourglass me-1"></i>État</th>
              <th><i class="bi bi-file-earmark-medical me-1"></i>Résultats</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($analyses as $analyse)
              <tr data-analyse="{{ strtolower($analyse->type_analyse . ' ' . ($analyse->patient ? $analyse->patient->nom . ' ' . $analyse->patient->prenom : '')) }}">
                <td>
                  <div class="date-info">
                    <div class="date-main">{{ $analyse->date_analyse ? \Carbon\Carbon::parse($analyse->date_analyse)->format('d/m/Y') : '—' }}</div>
                    <small class="date-time">{{ $analyse->created_at->format('H:i') }}</small>
                  </div>
                </td>
                <td>
                  @if($analyse->patient)
                    <div class="patient-info-inline">
                      <div class="patient-avatar-small">
                        {{ strtoupper(substr($analyse->patient->nom ?? 'P', 0, 1)) }}
                      </div>
                      <div>
                        <div class="patient-name-inline">{{ $analyse->patient->nom }} {{ $analyse->patient->prenom }}</div>
                        @if($analyse->patient->telephone)
                          <small class="text-muted">{{ $analyse->patient->telephone }}</small>
                        @endif
                      </div>
                    </div>
                  @else
                    <span class="text-muted">Patient non trouvé</span>
                  @endif
                </td>
                <td>
                  <div class="analyse-type">
                    <i class="bi bi-activity me-2"></i>
                    <strong>{{ $analyse->type_analyse }}</strong>
                  </div>
                </td>
                <td>
                  @php
                    $etats = [
                      'programmee' => ['status-scheduled', '📅', 'Programmée'],
                      'en_cours' => ['status-progress', '⏳', 'En cours'],
                      'terminee' => ['status-completed', '✅', 'Terminée'],
                      'annulee' => ['status-cancelled', '❌', 'Annulée']
                    ];
                    $etatInfo = $etats[$analyse->etat] ?? ['status-default', '', $analyse->etat];
                  @endphp
                  <span class="status-badge {{ $etatInfo[0] }}">
                    <span class="status-icon">{{ $etatInfo[1] }}</span>
                    {{ $etatInfo[2] }}
                  </span>
                </td>
                <td>
                  <div class="resultats-preview">
                    @if($analyse->resultats)
                      <div class="resultats-text">{{ Str::limit($analyse->resultats, 50) }}</div>
                    @else
                      <span class="no-results">En attente des résultats</span>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="analyses-actions">
                    <a href="{{ route('medecin.analyses.show', $analyse->id) }}" 
                       class="btn-analyse-action btn-view" title="Voir détails">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('medecin.analyses.edit', $analyse->id) }}" 
                       class="btn-analyse-action btn-edit" title="Modifier">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn-analyse-action btn-delete" 
                            title="Supprimer" onclick="confirmDelete({{ $analyse->id }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      {{-- Pagination moderne --}}
      <div class="pagination-container">
        {{ $analyses->appends(request()->query())->links() }}
      </div>
    </div>
  @endif
</div>

{{-- Modal de confirmation de suppression moderne --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <div class="modal-header border-0">
        <div class="modal-icon-warning">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <h5 class="mb-3">Confirmer la suppression</h5>
        <p class="text-muted mb-4">Êtes-vous sûr de vouloir supprimer cette analyse ? Cette action est définitive et ne peut pas être annulée.</p>
        <div class="modal-actions">
          <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Annuler
          </button>
          <form id="deleteForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-modal-confirm">
              <i class="bi bi-trash me-2"></i>Supprimer
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Styles modernes complets pour la page analyses --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne analyses */
  .analyses-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
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
  
  .search-box {
    position: relative;
    min-width: 300px;
  }
  
  .search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    z-index: 2;
  }
  
  .search-box .form-control {
    padding-left: 3rem;
    border: none;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.9);
    color: #374151;
  }
  
  .search-box .form-control:focus {
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
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
    color: #10b981;
    transform: translateY(-2px);
  }
  
  /* Grille des statistiques */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(16, 185, 129, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(16, 185, 129, 0.15);
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
  
  .stat-total .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-month .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
  .stat-pending .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .stat-completed .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
  
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
  
  /* Conteneur filtres */
  .filters-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(16, 185, 129, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .filters-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .filters-body {
    padding: 2rem;
  }
  
  .filters-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  .form-group {
    margin-bottom: 0;
  }
  
  .form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
  }
  
  .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
  }
  
  .form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    background: white;
  }
  
  .filters-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .btn-filter {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  .btn-reset {
    background: white;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-reset:hover {
    border-color: #10b981;
    color: #10b981;
  }
  
  .export-section {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
  }
  
  .export-section h6 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  
  .export-buttons {
    display: flex;
    gap: 1rem;
  }
  
  .btn-export {
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 2px solid;
  }
  
  .btn-export-csv {
    background: white;
    color: #059669;
    border-color: #059669;
  }
  
  .btn-export-csv:hover {
    background: #059669;
    color: white;
    transform: translateY(-2px);
  }
  
  .btn-export-pdf {
    background: white;
    color: #dc2626;
    border-color: #dc2626;
  }
  
  .btn-export-pdf:hover {
    background: #dc2626;
    color: white;
    transform: translateY(-2px);
  }
  
  /* Conteneur analyses */
  .analyses-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(16, 185, 129, 0.1);
    overflow: hidden;
  }
  
  .analyses-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .analyses-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .count-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  /* Table analyses */
  .analyses-table {
    margin: 0;
  }
  
  .analyses-table th {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .analyses-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .analyses-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .date-main {
    font-weight: 600;
    color: #374151;
  }
  
  .date-time {
    color: #6b7280;
    font-size: 0.8rem;
  }
  
  .patient-info-inline {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .patient-avatar-small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    flex-shrink: 0;
  }
  
  .patient-name-inline {
    font-weight: 600;
    color: #1f2937;
  }
  
  .analyse-type {
    display: flex;
    align-items: center;
  }
  
  .analyse-type i {
    color: #10b981;
  }
  
  /* Badges de statut */
  .status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }
  
  .status-scheduled {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .status-progress {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }
  
  .status-completed {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }
  
  .status-cancelled {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
  }
  
  .status-default {
    background: #f3f4f6;
    color: #6b7280;
  }
  
  /* Aperçu des résultats */
  .resultats-preview {
    max-width: 200px;
  }
  
  .resultats-text {
    background: #f0fdf4;
    color: #166534;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    border-left: 3px solid #10b981;
  }
  
  .no-results {
    color: #6b7280;
    font-style: italic;
    font-size: 0.9rem;
  }
  
  /* Actions des analyses */
  .analyses-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-analyse-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.9rem;
  }
  
  .btn-view {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .btn-edit {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }
  
  .btn-delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
  }
  
  .btn-analyse-action:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    color: white;
  }
  
  /* État vide */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
  }
  
  .empty-state i {
    font-size: 4rem;
    color: #10b981;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }
  
  .empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  
  .btn-create-first {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.8rem 2rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    margin-top: 1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-create-first:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  /* Pagination */
  .pagination-container {
    padding: 2rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: center;
  }
  
  /* Modal moderne */
  .modern-modal {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  }
  
  .modal-icon-warning {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin: 0 auto 1rem;
  }
  
  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }
  
  .btn-modal-cancel {
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-modal-cancel:hover {
    background: #e5e7eb;
    color: #374151;
  }
  
  .btn-modal-confirm {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-modal-confirm:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    color: white;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .search-box {
      min-width: 250px;
    }
    
    .filters-row {
      grid-template-columns: 1fr;
    }
    
    .analyses-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .export-buttons {
      flex-direction: column;
    }
    
    .modal-actions {
      flex-direction: column;
    }
  }
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche d'analyses
    const searchInput = document.getElementById('analysesSearch');
    const analyseRows = document.querySelectorAll('.analyses-table tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            analyseRows.forEach(row => {
                const analyseName = row.dataset.analyse;
                row.style.display = analyseName.includes(query) ? '' : 'none';
            });
        });
    }
    
    // Fonction de confirmation de suppression
    window.confirmDelete = function(analyseId) {
        const form = document.getElementById('deleteForm');
        form.action = `/medecin/analyses/${analyseId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };
    
    // Animation des cartes statistiques au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${Array.from(entry.target.parentNode.children).indexOf(entry.target) * 100}ms`;
                entry.target.classList.add('animate-fadeInUp');
            }
        });
    }, observerOptions);
    
    // Observer les cartes de statistiques
    document.querySelectorAll('.stat-card').forEach(card => {
        observer.observe(card);
    });
    
    // Mémoriser les filtres dans le sessionStorage
    const filterForm = document.querySelector('.filters-form');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            // Restaurer les valeurs sauvegardées
            const savedValue = sessionStorage.getItem(`filter_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
            
            // Sauvegarder les changements
            input.addEventListener('change', function() {
                sessionStorage.setItem(`filter_${this.name}`, this.value);
            });
        });
    }
    
    // Tooltip pour les actions
    const tooltips = document.querySelectorAll('[title]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = this.getAttribute('title');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
            
            this.customTooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this.customTooltip) {
                this.customTooltip.remove();
                this.customTooltip = null;
            }
        });
    });
    
    // Auto-rafraîchissement des statistiques (optionnel)
    let autoRefreshEnabled = localStorage.getItem('autoRefresh') !== 'false';
    
    if (autoRefreshEnabled) {
        setInterval(() => {
            // Seulement rafraîchir les statistiques sans recharger la page
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Optionnel: mettre à jour seulement les chiffres des stats
                    console.log('Statistiques rafraîchies automatiquement');
                }
            }).catch(() => {
                // Ignorer les erreurs de réseau
            });
        }, 300000); // 5 minutes
    }
});

// Styles pour les animations et tooltips custom
const additionalStyles = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .custom-tooltip {
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        animation: fadeIn 0.2s ease forwards;
    }
    
    .custom-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #1f2937;
    }
    
    @keyframes fadeIn {
        to { opacity: 1; }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
</script>
@endsection

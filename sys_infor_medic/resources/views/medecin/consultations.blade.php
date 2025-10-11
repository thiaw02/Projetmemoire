@extends('layouts.app')

@section('content')
{{-- Header moderne pour consultations médicales --}}
<div class="consultations-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-clipboard-pulse"></i>
      <span>Consultations Médicales</span>
    </div>
    <div class="header-actions">
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="consultationsSearch" placeholder="Rechercher une consultation..." class="form-control">
      </div>
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

{{-- Statistiques rapides --}}
<div class="stats-grid mb-4">
  <div class="stat-card stat-total">
    <div class="stat-icon">
      <i class="bi bi-clipboard-pulse"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $consultations->count() }}</div>
      <div class="stat-label">Total consultations</div>
    </div>
  </div>
  
  <div class="stat-card stat-today">
    <div class="stat-icon">
      <i class="bi bi-calendar-day"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $consultations->where('date_consultation', '>=', now()->startOfDay())->where('date_consultation', '<=', now()->endOfDay())->count() }}</div>
      <div class="stat-label">Aujourd'hui</div>
    </div>
  </div>
  
  <div class="stat-card stat-month">
    <div class="stat-icon">
      <i class="bi bi-calendar-month"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $consultations->where('date_consultation', '>=', now()->startOfMonth())->count() }}</div>
      <div class="stat-label">Ce mois</div>
    </div>
  </div>
  
  <div class="stat-card stat-pending">
    <div class="stat-icon">
      <i class="bi bi-clock-history"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $consultations->where('statut', 'en_attente')->count() }}</div>
      <div class="stat-label">En attente</div>
    </div>
  </div>
</div>
{{-- Bouton pour nouvelle consultation --}}
<div class="consultation-actions mb-4">
  <button class="btn-new-consultation" type="button" data-bs-toggle="collapse" data-bs-target="#formConsultation" aria-expanded="false" aria-controls="formConsultation">
    <i class="bi bi-plus-circle"></i>
    Nouvelle Consultation
  </button>
</div>

{{-- Formulaire pour ajouter une consultation --}}
<div class="collapse {{ request('patient_id') ? 'show' : '' }}" id="formConsultation">
  <div class="form-container">
    <div class="form-header">
      <h5 class="mb-0"><i class="bi bi-clipboard-plus me-2"></i>Ajouter une consultation</h5>
    </div>
    <div class="form-body">
      <form action="{{ route('medecin.consultations.store') }}" method="POST" class="consultation-form">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label for="patient_id" class="form-label">
              <i class="bi bi-person me-2"></i>
              Patient
            </label>
            <select name="patient_id" id="patient_id" class="form-control" required>
              <option value="">-- Sélectionner un patient --</option>
              @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ (request('patient_id')==$patient->id) ? 'selected' : '' }}>
                  {{ $patient->nom }} {{ $patient->prenom }}
                </option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group">
            <label for="date_consultation" class="form-label">
              <i class="bi bi-calendar-event me-2"></i>
              Date & Heure
            </label>
            <input type="datetime-local" name="date_consultation" id="date_consultation" class="form-control" required value="{{ old('date_consultation', request('date_time')) }}">
          </div>
        </div>
        
        <div class="form-group">
          <label for="symptomes" class="form-label">
            <i class="bi bi-thermometer me-2"></i>
            Symptômes observés
          </label>
          <textarea name="symptomes" id="symptomes" class="form-control" rows="3" placeholder="Décrivez les symptômes du patient..."></textarea>
        </div>
        
        <div class="form-group">
          <label for="diagnostic" class="form-label">
            <i class="bi bi-clipboard-pulse me-2"></i>
            Diagnostic établi
          </label>
          <textarea name="diagnostic" id="diagnostic" class="form-control" rows="3" placeholder="Votre diagnostic médical..."></textarea>
        </div>
        
        <div class="form-group">
          <label for="traitement" class="form-label">
            <i class="bi bi-prescription2 me-2"></i>
            Traitement prescrit
          </label>
          <textarea name="traitement" id="traitement" class="form-control" rows="3" placeholder="Plan de traitement recommandé..."></textarea>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle me-2"></i>
            Enregistrer la consultation
          </button>
          <button type="button" class="btn-cancel" data-bs-toggle="collapse" data-bs-target="#formConsultation">
            <i class="bi bi-x-lg me-2"></i>
            Annuler
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Liste des consultations modernes --}}
<div class="consultations-container">
  <div class="consultations-header-section">
    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Mes consultations ({{ $consultations->count() }} consultations)</h5>
    <div class="consultations-count-info">
      <span class="count-badge">{{ $consultations->count() }}</span>
    </div>
  </div>
  
  @if($consultations->isEmpty())
    <div class="empty-state">
      <i class="bi bi-clipboard-x"></i>
      <h5>Aucune consultation pour le moment</h5>
      <p>Les consultations médicales que vous effectuerez apparaîtront ici.</p>
      <button type="button" class="btn-create-first" data-bs-toggle="collapse" data-bs-target="#formConsultation">
        <i class="bi bi-plus-circle me-2"></i>
        Créer votre première consultation
      </button>
    </div>
  @else
    <div class="consultations-list">
      <div class="table-responsive">
        <table class="table consultations-table" id="consultationsTable">
          <thead>
            <tr>
              <th><i class="bi bi-person me-1"></i>Patient</th>
              <th><i class="bi bi-calendar-event me-1"></i>Date & Heure</th>
              <th><i class="bi bi-thermometer me-1"></i>Symptômes</th>
              <th><i class="bi bi-clipboard-pulse me-1"></i>Diagnostic</th>
              <th><i class="bi bi-prescription2 me-1"></i>Traitement</th>
              <th><i class="bi bi-check-circle me-1"></i>Statut</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($consultations as $consultation)
              <tr data-consultation="{{ strtolower(($consultation->patient->nom ?? '') . ' ' . ($consultation->patient->prenom ?? '') . ' ' . $consultation->diagnostic . ' ' . $consultation->symptomes) }}">
                <td>
                  <div class="patient-info-inline">
                    <div class="patient-avatar-small">
                      {{ strtoupper(substr($consultation->patient->nom ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                      <div class="patient-name-inline">{{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}</div>
                      @if($consultation->patient->telephone)
                        <small class="text-muted">{{ $consultation->patient->telephone }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <div class="date-info">
                    <div class="date-main">{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') }}</div>
                    <small class="date-time">{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('H:i') }}</small>
                  </div>
                </td>
                <td>
                  <div class="consultation-preview">
                    @if($consultation->symptomes)
                      <div class="symptoms-text">{{ Str::limit($consultation->symptomes, 50) }}</div>
                    @else
                      <span class="no-data">Non renseigné</span>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="consultation-preview">
                    @if($consultation->diagnostic)
                      <div class="diagnostic-text">{{ Str::limit($consultation->diagnostic, 50) }}</div>
                    @else
                      <span class="no-data">En cours d'évaluation</span>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="consultation-preview">
                    @if($consultation->traitement)
                      <div class="treatment-text">{{ Str::limit($consultation->traitement, 50) }}</div>
                    @else
                      <span class="no-data">À déterminer</span>
                    @endif
                  </div>
                </td>
                <td>
                  @php
                    $statuts = [
                      'en_attente' => ['status-pending', '🕰️', 'En attente'],
                      'confirmee' => ['status-confirmed', '✅', 'Confirmée'],
                      'annulee' => ['status-cancelled', '❌', 'Annulée'],
                      'terminee' => ['status-completed', '🏁', 'Terminée']
                    ];
                    $statutInfo = $statuts[$consultation->statut ?? 'en_attente'] ?? ['status-default', '', $consultation->statut ?? 'En attente'];
                  @endphp
                  <span class="status-badge {{ $statutInfo[0] }}">
                    <span class="status-icon">{{ $statutInfo[1] }}</span>
                    {{ $statutInfo[2] }}
                  </span>
                </td>
                <td>
                  <div class="consultations-actions">
                    <a href="{{ route('medecin.consultations.edit', $consultation->id) }}" 
                       class="btn-consultation-action btn-edit" title="Modifier la consultation">
                      <i class="bi bi-pencil"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>

{{-- Styles modernes complets pour la page consultations --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne consultations */
  .consultations-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
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
    color: #667eea;
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
    border: 1px solid rgba(102, 126, 234, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.15);
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
  
  .stat-total .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
  .stat-today .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
  .stat-month .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-pending .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  
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
  
  /* Actions consultation */
  .consultation-actions {
    margin-bottom: 2rem;
  }
  
  .btn-new-consultation {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
  }
  
  .btn-new-consultation:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  /* Conteneur formulaire */
  .form-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(102, 126, 234, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .form-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .form-body {
    padding: 2rem;
  }
  
  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  .form-group {
    margin-bottom: 1.5rem;
  }
  
  .form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
  }
  
  .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
  }
  
  .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
  }
  
  .form-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .btn-submit {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  .btn-cancel {
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
  
  .btn-cancel:hover {
    background: #e5e7eb;
    color: #374151;
  }
  
  /* Conteneur consultations */
  .consultations-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(102, 126, 234, 0.1);
    overflow: hidden;
  }
  
  .consultations-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .consultations-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .count-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  /* Table consultations */
  .consultations-table {
    margin: 0;
  }
  
  .consultations-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .consultations-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .consultations-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .patient-info-inline {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .patient-avatar-small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea, #764ba2);
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
  
  .consultation-preview {
    max-width: 200px;
  }
  
  .symptoms-text {
    background: #fef3c7;
    color: #92400e;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    border-left: 3px solid #f59e0b;
  }
  
  .diagnostic-text {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    border-left: 3px solid #3b82f6;
  }
  
  .treatment-text {
    background: #f0fdf4;
    color: #166534;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    border-left: 3px solid #10b981;
  }
  
  .no-data {
    color: #6b7280;
    font-style: italic;
    font-size: 0.9rem;
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
  
  .status-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }
  
  .status-confirmed {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }
  
  .status-cancelled {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
  }
  
  .status-completed {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .status-default {
    background: #f3f4f6;
    color: #6b7280;
  }
  
  /* Actions des consultations */
  .consultations-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-consultation-action {
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
  
  .btn-edit {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .btn-consultation-action:hover {
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
    color: #667eea;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }
  
  .empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  
  .btn-create-first {
    background: linear-gradient(135deg, #667eea, #764ba2);
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
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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
    
    .form-row {
      grid-template-columns: 1fr;
    }
    
    .consultations-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .form-actions {
      flex-direction: column;
    }
  }
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche de consultations
    const searchInput = document.getElementById('consultationsSearch');
    const consultationRows = document.querySelectorAll('.consultations-table tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            consultationRows.forEach(row => {
                const consultationData = row.dataset.consultation;
                row.style.display = consultationData.includes(query) ? '' : 'none';
            });
        });
    }
    
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
});

// Styles pour les animations et tooltips
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

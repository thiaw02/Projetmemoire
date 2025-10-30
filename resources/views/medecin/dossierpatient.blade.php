@extends('layouts.app')

@section('content')
{{-- Header moderne pour dossiers patients --}}
<div class="patients-records-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-journal-medical"></i>
      <span>Dossiers Médicaux</span>
    </div>
    <div class="header-actions">
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="patientSearch" placeholder="Rechercher un patient..." class="form-control">
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

{{-- Statistiques rapides --}}
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-icon patients-icon">
      <i class="bi bi-people-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ method_exists($patients, 'total') ? $patients->total() : $patients->count() }}</div>
      <div class="stat-label">Patients Total</div>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon active-icon">
      <i class="bi bi-person-check-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $patients->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
      <div class="stat-label">Nouveaux ce mois</div>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon consultation-icon">
      <i class="bi bi-clipboard2-pulse-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $patients->where('updated_at', '>=', now()->subWeek())->count() }}</div>
      <div class="stat-label">Consultés récemment</div>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon urgent-icon">
      <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="stat-content">
      <div class="stat-number">{{ $patients->whereNotNull('antecedents')->count() }}</div>
      <div class="stat-label">Avec antécédents</div>
    </div>
  </div>
</div>

{{-- Liste des patients --}}
<div class="patients-container">
  <div class="patients-header-section">
    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des patients ({{ method_exists($patients, 'total') ? $patients->total() : $patients->count() }} patients)</h5>
    <div class="view-options">
      <button class="view-btn active" data-view="grid"><i class="bi bi-grid-3x3"></i></button>
      <button class="view-btn" data-view="list"><i class="bi bi-list"></i></button>
    </div>
  </div>
  
  @if($patients->isEmpty())
    <div class="empty-state">
      <i class="bi bi-person-x"></i>
      <h5>Aucun patient pour le moment</h5>
      <p>Les patients qui vous sont assignés apparaîtront ici.</p>
    </div>
  @else
    {{-- Vue en grille (par défaut) --}}
    <div id="gridView" class="patients-grid">
      @foreach($patients as $patient)
        <div class="patient-card" data-patient="{{ strtolower($patient->nom . ' ' . $patient->prenom) }}">
          <div class="patient-avatar-large">
            {{ strtoupper(substr($patient->nom ?? 'P', 0, 1)) }}
          </div>
          
          <div class="patient-info">
            <h6 class="patient-name">{{ $patient->nom }} {{ $patient->prenom }}</h6>
            
            <div class="patient-details">
              <div class="detail-item">
                <i class="bi bi-gender-ambiguous"></i>
                <span>{{ $patient->sexe ?? 'Non spécifié' }}</span>
              </div>
              
              <div class="detail-item">
                <i class="bi bi-calendar3"></i>
                <span>{{ $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'Non spécifié' }}</span>
              </div>
              
              @if($patient->telephone)
              <div class="detail-item">
                <i class="bi bi-telephone"></i>
                <span>{{ $patient->telephone }}</span>
              </div>
              @endif
              
              @if($patient->groupe_sanguin)
              <div class="detail-item blood-type">
                <i class="bi bi-droplet-fill"></i>
                <span>{{ $patient->groupe_sanguin }}</span>
              </div>
              @endif
            </div>
            
            @if($patient->antecedents)
            <div class="patient-warning">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <small>Antécédents médicaux</small>
            </div>
            @endif
          </div>
          
          <div class="patient-actions">
            <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn-open-dossier">
              <i class="bi bi-folder2-open"></i>
              Ouvrir le dossier
            </a>
          </div>
        </div>
      @endforeach
    </div>
    
    {{-- Vue en liste (cachée par défaut) --}}
    <div id="listView" class="patients-list" style="display: none;">
      <div class="table-responsive">
        <table class="table patients-table">
          <thead>
            <tr>
              <th><i class="bi bi-person me-1"></i>Patient</th>
              <th><i class="bi bi-gender-ambiguous me-1"></i>Sexe</th>
              <th><i class="bi bi-calendar3 me-1"></i>Date de naissance</th>
              <th><i class="bi bi-telephone me-1"></i>Téléphone</th>
              <th><i class="bi bi-droplet-fill me-1"></i>Groupe sanguin</th>
              <th><i class="bi bi-exclamation-triangle me-1"></i>Antécédents</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($patients as $patient)
              <tr data-patient="{{ strtolower($patient->nom . ' ' . $patient->prenom) }}">
                <td>
                  <div class="patient-info-inline">
                    <div class="patient-avatar-small">
                      {{ strtoupper(substr($patient->nom ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                      <div class="patient-name-inline">{{ $patient->nom }} {{ $patient->prenom }}</div>
                      @if($patient->email)
                        <small class="text-muted">{{ $patient->email }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>{{ $patient->sexe ?? '—' }}</td>
                <td>{{ $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') : '—' }}</td>
                <td>{{ $patient->telephone ?? '—' }}</td>
                <td>
                  @if($patient->groupe_sanguin)
                    <span class="blood-type-badge">{{ $patient->groupe_sanguin }}</span>
                  @else
                    —
                  @endif
                </td>
                <td>
                  @if($patient->antecedents)
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Oui</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-folder2-open me-1"></i>Ouvrir
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center my-3">
  {{ method_exists($patients, 'links') ? $patients->links() : '' }}
  {{-- Si vous avez un template personnalisé, utilisez: $patients->links('pagination.custom') --}}
</div>

{{-- Styles modernes pour la page dossiers patients --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne dossiers patients */
  .patients-records-header {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(30, 64, 175, 0.15);
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
    color: #1e40af;
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
    border: 1px solid rgba(30, 64, 175, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(30, 64, 175, 0.15);
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
  
  .patients-icon { background: linear-gradient(135deg, #1e40af, #1d4ed8); }
  .active-icon { background: linear-gradient(135deg, #10b981, #059669); }
  .consultation-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .urgent-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
  
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
  
  /* Conteneur patients */
  .patients-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(30, 64, 175, 0.1);
    overflow: hidden;
  }
  
  .patients-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .patients-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .view-options {
    display: flex;
    gap: 0.5rem;
  }
  
  .view-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #d1d5db;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .view-btn.active,
  .view-btn:hover {
    border-color: #1e40af;
    color: #1e40af;
    background: rgba(30, 64, 175, 0.1);
  }
  
  /* Vue en grille */
  .patients-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
  }
  
  .patient-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .patient-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #1e40af;
  }
  
  .patient-avatar-large {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
  }
  
  .patient-name {
    color: #1f2937;
    font-weight: 600;
    text-align: center;
    margin-bottom: 1rem;
    font-size: 1.1rem;
  }
  
  .patient-details {
    margin-bottom: 1rem;
  }
  
  .detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #6b7280;
  }
  
  .detail-item i {
    width: 16px;
    color: #1e40af;
  }
  
  .detail-item.blood-type {
    color: #dc2626;
    font-weight: 600;
  }
  
  .patient-warning {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #fef3c7;
    color: #92400e;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.8rem;
    margin-bottom: 1rem;
  }
  
  .btn-open-dossier {
    width: 100%;
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-open-dossier:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
    color: white;
  }
  
  /* Vue en liste */
  .patients-table {
    margin: 0;
  }
  
  .patients-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .patients-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .patients-table tbody tr:hover {
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
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
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
  
  .blood-type-badge {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  /* État vide */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
  }
  
  .empty-state i {
    font-size: 4rem;
    color: #1e40af;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }
  
  .empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-weight: 600;
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
    
    .patients-grid {
      grid-template-columns: 1fr;
      padding: 1.5rem;
    }
    
    .patients-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
  }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche de patients
    const searchInput = document.getElementById('patientSearch');
    const patientCards = document.querySelectorAll('.patient-card');
    const patientRows = document.querySelectorAll('.patients-table tbody tr');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        // Filtrer les cartes (vue grille)
        patientCards.forEach(card => {
            const patientName = card.dataset.patient;
            card.style.display = patientName.includes(query) ? 'block' : 'none';
        });
        
        // Filtrer les lignes (vue liste)
        patientRows.forEach(row => {
            const patientName = row.dataset.patient;
            row.style.display = patientName.includes(query) ? '' : 'none';
        });
    });
    
    // Basculer entre les vues
    const viewButtons = document.querySelectorAll('.view-btn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Mettre à jour les boutons actifs
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Afficher la bonne vue
            if (view === 'grid') {
                gridView.style.display = 'grid';
                listView.style.display = 'none';
            } else {
                gridView.style.display = 'none';
                listView.style.display = 'block';
            }
            
            // Sauvegarder la préférence
            localStorage.setItem('patientViewMode', view);
        });
    });
    
    // Restaurer la vue préférée
    const savedView = localStorage.getItem('patientViewMode');
    if (savedView && savedView === 'list') {
        document.querySelector('[data-view="list"]').click();
    }
});
</script>
@endsection

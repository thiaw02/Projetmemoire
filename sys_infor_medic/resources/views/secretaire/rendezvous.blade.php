@extends('layouts.app')

@section('content')
{{-- Header moderne pour rendez-vous --}}
<div class="appointments-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-calendar2-week"></i>
      <span>Gestion des Rendez-vous</span>
    </div>
    <div class="header-actions">
      <button class="btn btn-light btn-lg" id="toggleFormBtn">
        <i class="bi bi-calendar-plus me-2"></i>Planifier un rendez-vous
      </button>
      <a href="{{ route('secretaire.dashboard') }}" class="btn btn-outline-light">
        <i class="bi bi-arrow-left me-1"></i>Retour
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

{{-- Formulaire moderne de création --}}
<div class="appointment-form-container" id="rdvForm" style="display:none;">
  <div class="form-header">
    <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Nouveau rendez-vous</h5>
  </div>
  <div class="form-body">
    <form action="{{ route('secretaire.rendezvous.store') }}" method="POST" class="appointment-form">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label for="patient" class="form-label modern-label"><i class="bi bi-person me-1"></i>Patient</label>
          <select name="patient_id" id="patient" class="form-select modern-select" required>
            <option value="">— Sélectionnez un patient —</option>
            @foreach($patients as $patient)
              <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label for="medecin" class="form-label modern-label"><i class="bi bi-person-badge me-1"></i>Médecin</label>
          <select name="medecin_id" id="medecin" class="form-select modern-select" required>
            <option value="">— Sélectionnez un médecin —</option>
            @foreach($medecins as $medecin)
              <option value="{{ $medecin->id }}">{{ $medecin->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label for="date" class="form-label modern-label"><i class="bi bi-calendar me-1"></i>Date</label>
          <input type="date" name="date" id="date" class="form-control modern-input" required>
        </div>
        <div class="col-md-4">
          <label for="heure" class="form-label modern-label"><i class="bi bi-clock me-1"></i>Heure</label>
          <input type="time" name="heure" id="heure" class="form-control modern-input" required>
        </div>
        <div class="col-md-4">
          <div class="d-flex align-items-end h-100">
            <button type="submit" class="btn btn-primary btn-lg w-100">
              <i class="bi bi-calendar-check me-2"></i>Planifier
            </button>
          </div>
        </div>
        <div class="col-12">
          <label for="motif" class="form-label modern-label"><i class="bi bi-chat-text me-1"></i>Motif (optionnel)</label>
          <input type="text" name="motif" id="motif" class="form-control modern-input" placeholder="Ex: Consultation générale">
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Liste des rendez-vous moderne --}}
<div class="appointments-container">
  <div class="appointments-header-section">
    <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Rendez-vous existants</h5>
    <div class="search-container">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" id="searchRdv" class="form-control" placeholder="Rechercher un rendez-vous...">
      </div>
    </div>
  </div>
  
  <div class="table-responsive">
    <table class="table appointments-table" id="rdvTable">
      <thead>
        <tr>
          <th><i class="bi bi-person me-1"></i>Patient</th>
          <th><i class="bi bi-person-badge me-1"></i>Médecin</th>
          <th><i class="bi bi-calendar me-1"></i>Date</th>
          <th><i class="bi bi-clock me-1"></i>Heure</th>
          <th><i class="bi bi-check-circle me-1"></i>Statut</th>
          <th><i class="bi bi-gear me-1"></i>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rendezvous as $rdv)
          <tr>
            <td>
              <div class="patient-info">
                <strong>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</strong>
              </div>
            </td>
            <td>{{ $rdv->medecin->name }}</td>
            <td>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
            <td>{{ $rdv->heure }}</td>
            <td>
              @php $s = strtolower((string)$rdv->statut);
                $badge = in_array($s,['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array($s,['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark');
              @endphp
              <span class="badge {{ $badge }} status-badge">{{ str_replace('_',' ', $rdv->statut) }}</span>
            </td>
            <td>
              <div class="action-buttons">
                <a href="{{ route('secretaire.rendezvous.confirm', $rdv->id) }}" class="btn btn-outline-success btn-sm" title="Confirmer">
                  <i class="bi bi-check-lg me-1"></i>Confirmer
                </a>
                <a href="{{ route('secretaire.rendezvous.cancel', $rdv->id) }}" class="btn btn-outline-danger btn-sm" title="Annuler">
                  <i class="bi bi-x-lg me-1"></i>Annuler
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="bi bi-calendar-x display-6 text-muted mb-3"></i><br>
              Aucun rendez-vous programmé
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Styles modernes pour la page rendez-vous --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne rendez-vous */
  .appointments-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
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
    gap: 0.5rem;
    align-items: center;
  }
  
  .header-actions .btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .header-actions .btn-light {
    background: rgba(255, 255, 255, 0.9);
    color: #3b82f6;
    border: none;
  }
  
  .header-actions .btn-light:hover {
    background: white;
    transform: translateY(-2px);
  }
  
  /* Formulaire de création */
  .appointment-form-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(59, 130, 246, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .form-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .form-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .form-body {
    padding: 2rem;
  }
  
  .appointment-form .modern-label {
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .appointment-form .modern-input,
  .appointment-form .modern-select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
  }
  
  .appointment-form .modern-input:focus,
  .appointment-form .modern-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .appointment-form .btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .appointment-form .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  /* Conteneur rendez-vous */
  .appointments-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(59, 130, 246, 0.1);
    overflow: hidden;
  }
  
  .appointments-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .appointments-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .search-container {
    width: 300px;
  }
  
  .search-container .input-group-text {
    background: white;
    border-color: #d1d5db;
  }
  
  .search-container .form-control {
    border-color: #d1d5db;
  }
  
  .search-container .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .appointments-table {
    margin: 0;
  }
  
  .appointments-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .appointments-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .appointments-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .patient-info strong {
    color: #374151;
  }
  
  .status-badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 500;
  }
  
  .action-buttons {
    display: flex;
    gap: 0.5rem;
  }
  
  .action-buttons .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .action-buttons .btn:hover {
    transform: translateY(-1px);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .header-actions {
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .appointments-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .search-container {
      width: 100%;
    }
    
    .appointments-table th,
    .appointments-table td {
      padding: 0.75rem 0.5rem;
    }
    
    .action-buttons {
      flex-direction: column;
    }
  }
</style>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle form visibility
    const toggleBtn = document.getElementById('toggleFormBtn');
    const form = document.getElementById('rdvForm');
    
    toggleBtn.addEventListener('click', function() {
      if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
      } else {
        form.style.display = 'none';
      }
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchRdv');
    const tableRows = document.querySelectorAll('#rdvTable tbody tr');
    
    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      
      tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  });
</script>
@endsection

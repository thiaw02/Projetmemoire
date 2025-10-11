@extends('layouts.app')

@section('content')
{{-- Header moderne pour admissions --}}
<div class="admissions-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-hospital"></i>
      <span>Gestion des Admissions</span>
    </div>
    <div class="header-actions">
      <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#addAdmissionModal">
        <i class="bi bi-plus-circle me-2"></i>Nouvelle admission
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

<div class="admissions-container">
  <div class="admissions-header-section">
    <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Historique des admissions</h5>
    <div class="admissions-stats">
      <span class="badge bg-warning text-dark">{{ count($admissions) }} admission(s)</span>
    </div>
  </div>
  
  <div class="table-responsive">
    <table class="table admissions-table">
      <thead>
        <tr>
          <th><i class="bi bi-hash me-1"></i>#</th>
          <th><i class="bi bi-person me-1"></i>Patient</th>
          <th><i class="bi bi-calendar me-1"></i>Date d'admission</th>
          <th><i class="bi bi-chat-text me-1"></i>Motif</th>
          <th><i class="bi bi-gear me-1"></i>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($admissions as $index => $admission)
        <tr>
          <td><span class="admission-number">{{ $index + 1 }}</span></td>
          <td>
            <div class="patient-info">
              <strong>{{ $admission->patient->nom ?? '' }} {{ $admission->patient->prenom ?? '' }}</strong>
            </div>
          </td>
          <td>{{ \Carbon\Carbon::parse($admission->date_admission)->format('d/m/Y') }}</td>
          <td>
            <div class="motif-text">{{ $admission->motif }}</div>
          </td>
          <td>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAdmissionModal{{ $admission->id }}">
              <i class="bi bi-eye me-1"></i>Voir / Modifier
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-5">
            <i class="bi bi-hospital display-6 text-muted mb-3"></i><br>
            Aucune admission enregistrée
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Ajouter Admission --}}
<div class="modal fade" id="addAdmissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Ajouter une admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.storeAdmission') }}" method="POST">
                @csrf
                <div class="modal-body modern-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-person me-1"></i>Patient</label>
                            <select name="patient_id" class="form-select modern-select" required>
                                <option value="">— Sélectionner un patient —</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-calendar me-1"></i>Date d'admission</label>
                            <input type="date" name="date_admission" class="form-control modern-input" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label modern-label"><i class="bi bi-chat-text me-1"></i>Motif de l'admission</label>
                            <textarea name="motif" class="form-control modern-textarea" rows="3" placeholder="Ex : Surveillance post-opératoire, urgence cardiaque..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modals pour modifier chaque admission --}}
@foreach($admissions as $admission)
<div class="modal fade" id="editAdmissionModal{{ $admission->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Modifier l'admission de {{ $admission->patient->nom ?? '' }} {{ $admission->patient->prenom ?? '' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.updateAdmission', $admission->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $admission->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->nom }} {{ $patient->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'Admission</label>
                            <input type="date" name="date_admission" class="form-control" value="{{ $admission->date_admission }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Motif</label>
                            <textarea name="motif" class="form-control" rows="3" required>{{ $admission->motif }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Styles modernes pour la page admissions --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne admissions */
  .admissions-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15);
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
    color: #f59e0b;
    border: none;
  }
  
  .header-actions .btn-light:hover {
    background: white;
    transform: translateY(-2px);
  }
  
  /* Conteneur admissions */
  .admissions-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(245, 158, 11, 0.1);
    overflow: hidden;
  }
  
  .admissions-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .admissions-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .admissions-stats .badge {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
  }
  
  .admissions-table {
    margin: 0;
  }
  
  .admissions-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .admissions-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .admissions-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .admission-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.85rem;
  }
  
  .patient-info strong {
    color: #374151;
  }
  
  .motif-text {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #6b7280;
  }
  
  .admissions-table .btn-sm {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .admissions-table .btn-outline-primary:hover {
    transform: translateY(-1px);
  }
  
  /* Modals modernes */
  .modern-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }
  
  .modern-modal-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    color: white !important;
    padding: 1.5rem 2rem;
    border-bottom: none;
    border-radius: 16px 16px 0 0;
  }
  
  .modern-modal-header .modal-title {
    font-weight: 600;
    font-size: 1.1rem;
  }
  
  .modern-modal-body {
    padding: 2rem;
  }
  
  .modern-label {
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .modern-input,
  .modern-select,
  .modern-textarea {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
  }
  
  .modern-input:focus,
  .modern-select:focus,
  .modern-textarea:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
  }
  
  .modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e2e8f0;
  }
  
  .modal-footer .btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
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
    
    .admissions-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .admissions-table th,
    .admissions-table td {
      padding: 0.75rem 1rem;
    }
    
    .motif-text {
      max-width: 200px;
    }
  }
</style>
@endsection

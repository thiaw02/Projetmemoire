@extends('layouts.app')

@section('content')
{{-- Header moderne pour dossiers administratifs --}}
<div class="admin-files-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-folder2-open"></i>
      <span>Dossiers Administratifs</span>
    </div>
    <div class="header-actions">
      <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#addPatientModal">
        <i class="bi bi-person-plus me-2"></i>Ajouter un patient
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

<div class="patients-container">
  <div class="patients-header">
    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Liste des patients</h5>
    <div class="patients-stats">
      <span class="badge bg-success">{{ count($patients) }} patient(s)</span>
    </div>
  </div>
  
  <div class="table-responsive">
    <table class="table patients-table">
      <thead>
        <tr>
          <th><i class="bi bi-hash me-1"></i>#</th>
          <th><i class="bi bi-person me-1"></i>Nom & Prénom</th>
          <th><i class="bi bi-envelope me-1"></i>Email</th>
          <th><i class="bi bi-telephone me-1"></i>Téléphone</th>
          <th><i class="bi bi-gear me-1"></i>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($patients as $index => $patient)
        <tr>
          <td><span class="patient-number">{{ $index + 1 }}</span></td>
          <td>
            <div class="patient-name">
              <strong>{{ $patient->nom }} {{ $patient->prenom }}</strong>
              @if($patient->sexe)
                <small class="text-muted">({{ $patient->sexe }})</small>
              @endif
            </div>
          </td>
          <td>{{ $patient->email ?? '—' }}</td>
          <td>{{ $patient->telephone ?? '—' }}</td>
          <td>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewPatientModal{{ $patient->id }}">
              <i class="bi bi-eye me-1"></i>Voir / Modifier
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-5">
            <i class="bi bi-person-x display-6 text-muted mb-3"></i><br>
            Aucun patient enregistré
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>


{{-- Modals des patients --}}
@foreach($patients as $patient)
<div class="modal fade" id="viewPatientModal{{ $patient->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Patient : {{ $patient->nom }} {{ $patient->prenom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.updatePatient', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="{{ $patient->nom }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="{{ $patient->prenom }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $patient->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ $patient->telephone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sexe</label>
                            <select name="sexe" class="form-select">
                                <option value="Homme" {{ $patient->sexe == 'Homme' ? 'selected' : '' }}>Homme</option>
                                <option value="Femme" {{ $patient->sexe == 'Femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="{{ $patient->date_naissance }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secrétaire assigné(e)</label>
                            <select name="secretary_user_id" class="form-select">
                                <option value="">-- Aucune --</option>
                                @foreach(($secretaires ?? []) as $sec)
                                    <option value="{{ $sec->id }}" {{ ($patient->secretary_user_id ?? null) == $sec->id ? 'selected' : '' }}>{{ $sec->name }} ({{ $sec->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ $patient->adresse }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Groupe sanguin</label>
                            <input type="text" name="groupe_sanguin" class="form-control" value="{{ $patient->groupe_sanguin }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Antécédents</label>
                            <input type="text" name="antecedents" class="form-control" value="{{ $patient->antecedents }}">
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

{{-- Styles modernes pour la page dossiers administratifs --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne dossiers administratifs */
  .admin-files-header {
    background: linear-gradient(135deg, #27ae60 0%, #16a085 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(39, 174, 96, 0.15);
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
    color: #27ae60;
    border: none;
  }
  
  .header-actions .btn-light:hover {
    background: white;
    transform: translateY(-2px);
  }
  
  /* Conteneur patients */
  .patients-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(39, 174, 96, 0.1);
    overflow: hidden;
  }
  
  .patients-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .patients-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .patients-stats .badge {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
  }
  
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
  
  .patient-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #27ae60, #16a085);
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.85rem;
  }
  
  .patient-name strong {
    color: #374151;
  }
  
  .patients-table .btn-sm {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .patients-table .btn-outline-primary:hover {
    transform: translateY(-1px);
  }
  
  /* Modals modernes */
  .modern-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }
  
  .modern-modal-header {
    background: linear-gradient(135deg, #27ae60 0%, #16a085 100%) !important;
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
  .modern-select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
  }
  
  .modern-input:focus,
  .modern-select:focus {
    border-color: #27ae60;
    box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
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
    
    .patients-header {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .patients-table th,
    .patients-table td {
      padding: 0.75rem 1rem;
    }
  }
</style>

{{-- Modal Ajouter un patient --}}
<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Ajouter un patient</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.storePatient') }}" method="POST">
                @csrf
                <div class="modal-body modern-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-person me-1"></i>Nom</label>
                            <input type="text" name="nom" class="form-control modern-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-person me-1"></i>Prénom</label>
                            <input type="text" name="prenom" class="form-control modern-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-envelope me-1"></i>Email</label>
                            <input type="email" name="email" class="form-control modern-input">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-telephone me-1"></i>Téléphone</label>
                            <input type="text" name="telephone" class="form-control modern-input">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-gender-ambiguous me-1"></i>Sexe</label>
                            <select name="sexe" class="form-select modern-select">
                                <option value="Homme">Homme</option>
                                <option value="Femme">Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-calendar me-1"></i>Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control modern-input">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label modern-label"><i class="bi bi-geo-alt me-1"></i>Adresse</label>
                            <input type="text" name="adresse" class="form-control modern-input">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-droplet me-1"></i>Groupe sanguin</label>
                            <input type="text" name="groupe_sanguin" class="form-control modern-input">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label modern-label"><i class="bi bi-clipboard-pulse me-1"></i>Antécédents</label>
                            <input type="text" name="antecedents" class="form-control modern-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

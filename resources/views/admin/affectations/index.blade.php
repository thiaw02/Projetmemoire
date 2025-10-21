@extends('layouts.app')

@section('content')
<style>
  /* Styles admin affectations */
  body > .container { max-width: 1400px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  
  .admin-page-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 6px 20px rgba(5, 150, 105, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .page-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
  }
  
  .page-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
  }
  
  .back-btn-admin {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
  }
  
  .back-btn-admin:hover {
    background: white;
    color: #059669;
    transform: translateY(-1px);
  }
  
  .affectations-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: none;
  }
  
  .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
  }
  
  .form-select:focus {
    border-color: #059669;
    box-shadow: 0 0 0 0.2rem rgba(5, 150, 105, 0.15);
  }
  
  .nurses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
  }
  
  .nurse-checkbox {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 10px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .nurse-checkbox:hover {
    border-color: #d1fae5;
    background: #ecfdf5;
  }
  
  .nurse-checkbox.checked {
    border-color: #059669;
    background: #d1fae5;
  }
  
  .form-check-input:checked {
    background-color: #059669;
    border-color: #059669;
  }
  
  .btn-save-affectations {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    border: none;
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-save-affectations:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
    color: white;
  }
</style>

<div class="admin-page-header">
  <div class="header-content">
    <h4 class="page-title">
      <i class="bi bi-diagram-3"></i>
      Affectations Médecin-Infirmier
    </h4>
    <a href="{{ route('admin.dashboard') }}" class="back-btn-admin">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="affectations-card">
  <form method="GET" aria-label="Sélectionner un médecin">
    <div class="mb-4">
      <label for="doctor_id" class="form-label fw-semibold text-dark">
        <i class="bi bi-person-badge me-2"></i>Sélectionner un médecin
      </label>
      <select class="form-select" id="doctor_id" name="doctor_id" onchange="this.form.submit()">
        <option value="">— Choisir un médecin —</option>
        @foreach($doctors as $doc)
          <option value="{{ $doc->id }}" {{ $selectedDoctor && $selectedDoctor->id === $doc->id ? 'selected' : '' }}>
            {{ $doc->name }} @if($doc->specialite) ({{ $doc->specialite }}) @endif
          </option>
        @endforeach
      </select>
    </div>
  </form>

  @if($selectedDoctor)
    <div class="alert alert-info d-flex align-items-center mb-4">
      <i class="bi bi-info-circle me-2"></i>
      <div>
        <strong>Médecin sélectionné:</strong> {{ $selectedDoctor->name }}
        @if($selectedDoctor->specialite)
          <span class="text-muted"> • {{ $selectedDoctor->specialite }}</span>
        @endif
      </div>
    </div>

    <form method="POST" action="{{ route('admin.affectations.update', $selectedDoctor->id) }}" aria-label="Affecter des infirmiers">
      @csrf
      @method('PUT')

      <h6 class="fw-semibold text-dark mb-3">
        <i class="bi bi-people me-2"></i>Affecter des infirmiers
      </h6>

      @if($nurses->isEmpty())
        <div class="text-center py-4 text-muted">
          <i class="bi bi-person-x display-4 mb-2"></i>
          <p>Aucun infirmier disponible pour affectation.</p>
        </div>
      @else
        <div class="nurses-grid">
          @foreach($nurses as $n)
            <div class="nurse-checkbox {{ in_array($n->id, $assignedNurseIds) ? 'checked' : '' }}">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="nurses[]" id="nurse_{{ $n->id }}" 
                       value="{{ $n->id }}" {{ in_array($n->id, $assignedNurseIds) ? 'checked' : '' }}
                       onchange="this.closest('.nurse-checkbox').classList.toggle('checked', this.checked)">
                <label class="form-check-label fw-medium" for="nurse_{{ $n->id }}">
                  <div>{{ $n->name }}</div>
                  @if($n->pro_phone)
                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $n->pro_phone }}</small>
                  @endif
                </label>
              </div>
            </div>
          @endforeach
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn-save-affectations">
            <i class="bi bi-check2-circle"></i>
            Enregistrer les affectations
          </button>
        </div>
      @endif
    </form>
  @endif
</div>
@endsection

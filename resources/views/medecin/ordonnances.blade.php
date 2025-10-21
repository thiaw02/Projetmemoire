@extends('layouts.app')

@section('content')
<style>
  /* Styles modernes pour la page ordonnances */
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  
  .ordonnances-page-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
    position: relative;
    overflow: hidden;
  }
  
  .ordonnances-page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
      45deg,
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0.05) 1px,
      transparent 1px,
      transparent 10px
    );
    opacity: 0.3;
  }
  
  .ordonnances-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .ordonnances-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
  }
  
  .ordonnances-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.8rem;
    border-radius: 16px;
    font-size: 1.5rem;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .add-ordonnance-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.8rem 1.8rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
  }
  
  .add-ordonnance-btn:hover {
    background: white;
    color: #f59e0b;
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }
  
  .back-btn-ordonnances {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.7rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .back-btn-ordonnances:hover {
    background: white;
    color: #f59e0b;
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }
  
  .ordonnance-form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .ordonnance-form-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 1.5rem 2rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 1.1rem;
  }
  
  .ordonnance-form-body {
    padding: 2rem;
  }
  
  .form-group-ordonnance {
    margin-bottom: 1.5rem;
  }
  
  .form-label-ordonnance {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .form-control-ordonnance {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f9fafb;
  }
  
  .form-control-ordonnance:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25);
    background: white;
  }
  
  .btn-submit-ordonnance {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-submit-ordonnance:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  }
  
  .ordonnances-list-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
  }
  
  .ordonnances-list-header {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    padding: 1.5rem 2rem;
    border: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .ordonnances-list-body {
    padding: 2rem;
  }
  
  .modern-ordonnance-table {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
  }
  
  .modern-ordonnance-table th {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    font-weight: 600;
    padding: 1rem;
    border: none;
    font-size: 0.9rem;
  }
  
  .modern-ordonnance-table td {
    padding: 1rem;
    border: none;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
  }
  
  .modern-ordonnance-table tbody tr {
    transition: all 0.3s ease;
  }
  
  .modern-ordonnance-table tbody tr:hover {
    background: linear-gradient(135deg, #fef3c7 0%, #fef9e2 100%);
    transform: scale(1.01);
  }
  
  .medicament-list {
    background: #fef9e2;
    border-radius: 8px;
    padding: 0.8rem;
    margin-bottom: 0.5rem;
    border-left: 4px solid #f59e0b;
  }
  
  .medicament-list ul {
    margin: 0;
    padding-left: 1rem;
  }
  
  .medicament-list li {
    color: #92400e;
    font-weight: 500;
    margin-bottom: 0.25rem;
  }
  
  .dosage-info {
    background: #fef3c7;
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    color: #92400e;
    font-style: italic;
  }
  
  .patient-info-ordonnance {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .patient-avatar-ordonnance {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
  }
  
  .ordonnance-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-ordonnance-action {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.85rem;
  }
  
  .btn-edit-ordonnance {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
  }
  
  .btn-download-ordonnance {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
  }
  
  .btn-send-ordonnance {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
  }
  
  .btn-ordonnance-action:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    color: white;
  }
  
  .empty-state-ordonnances {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
  }
  
  .empty-state-ordonnances i {
    font-size: 4rem;
    color: #f59e0b;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }
  
  .empty-state-ordonnances h5 {
    color: #374151;
    margin-bottom: 1rem;
    font-weight: 600;
  }
</style>

<div class="ordonnances-page-header">
  <div class="ordonnances-header-content">
    <h2 class="ordonnances-title">
      <i class="bi bi-prescription2"></i>
      Gestion des Ordonnances
    </h2>
    <div class="header-actions">
      <button class="add-ordonnance-btn" type="button" data-bs-toggle="collapse" data-bs-target="#formOrdonnance" aria-expanded="false" aria-controls="formOrdonnance">
        <i class="bi bi-plus-circle"></i>
        Nouvelle Ordonnance
      </button>
      <a href="{{ route('medecin.dashboard') }}" class="back-btn-ordonnances">
        <i class="bi bi-arrow-left"></i>
        Retour au Dashboard
      </a>
    </div>
  </div>
</div>

<!-- Formulaire pour ajouter une ordonnance (collapse) -->
<div class="collapse {{ request('patient_id') ? 'show' : '' }}" id="formOrdonnance">
  <div class="ordonnance-form-card">
    <div class="ordonnance-form-header">
      <i class="bi bi-prescription2"></i>
      Rédiger une nouvelle ordonnance
    </div>
    <div class="ordonnance-form-body">
      <form action="{{ route('medecin.ordonnances.store') }}" method="POST">
        @csrf
        <div class="form-group-ordonnance">
          <label for="patient_id" class="form-label-ordonnance">
            <i class="bi bi-person"></i>
            Patient concerné
          </label>
          <select name="patient_id" id="patient_id" class="form-control form-control-ordonnance" required>
            <option value="">-- Sélectionner un patient --</option>
            @foreach($patients as $patient)
              <option value="{{ $patient->id }}" {{ (request('patient_id')==$patient->id) ? 'selected' : '' }}>
                {{ $patient->nom }} {{ $patient->prenom }}
              </option>
            @endforeach
          </select>
        </div>
        
        <div class="form-group-ordonnance">
          <label for="medicaments" class="form-label-ordonnance">
            <i class="bi bi-capsule"></i>
            Médicaments & Instructions
          </label>
          <textarea name="medicaments" id="medicaments" class="form-control form-control-ordonnance" rows="6" required placeholder="Exemple :\n• Paracétamol 500mg — 1 comprimé, 3 fois par jour\n• Ibuprofene 200mg — 1 comprimé, en cas de douleur\n• Doliprane sirop — 5ml, 2 fois par jour"></textarea>
          <div class="form-text text-muted mt-2">
            <i class="bi bi-info-circle me-1"></i>
            Chaque ligne sera affichée comme un médicament séparé dans l'ordonnance.
          </div>
        </div>
        
        <div class="form-group-ordonnance">
          <label for="dosage" class="form-label-ordonnance">
            <i class="bi bi-clock"></i>
            Instructions générales (optionnel)
          </label>
          <input type="text" id="dosage" name="dosage" class="form-control form-control-ordonnance" placeholder="Ex: Traitement à suivre pendant 7 jours, à prendre après les repas">
        </div>
        
        <div class="d-flex gap-2 align-items-center">
          <button type="submit" class="btn-submit-ordonnance">
            <i class="bi bi-check-circle"></i>
            Établir l'ordonnance
          </button>
          <button type="button" class="btn btn-light" data-bs-toggle="collapse" data-bs-target="#formOrdonnance">
            Annuler
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Liste des ordonnances -->
<div class="ordonnances-list-card">
  <div class="ordonnances-list-header">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-list-ul"></i>
      <span>Ordonnances établies</span>
    </div>
    <span class="badge bg-white text-dark">{{ $ordonnances->count() }} ordonnances</span>
  </div>
  <div class="ordonnances-list-body">
    @if($ordonnances->isEmpty())
      <div class="empty-state-ordonnances">
        <i class="bi bi-prescription2"></i>
        <h5>Aucune ordonnance établie</h5>
        <p>Commencez par rédiger votre première ordonnance pour un patient.</p>
      </div>
    @else
      <div class="table-responsive">
        <table class="table modern-ordonnance-table">
          <thead>
            <tr>
              <th><i class="bi bi-person me-1"></i>Patient</th>
              <th><i class="bi bi-capsule me-1"></i>Médicaments & Instructions</th>
              <th><i class="bi bi-calendar-date me-1"></i>Date d'établissement</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($ordonnances as $ordonnance)
              <tr>
                <td>
                  <div class="patient-info-ordonnance">
                    <div class="patient-avatar-ordonnance">
                      {{ strtoupper(substr($ordonnance->patient->nom ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                      <div class="fw-semibold">{{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  @php($text = $ordonnance->medicaments ?: $ordonnance->contenu)
                  @if(!empty($text))
                    @php($lines = preg_split("/(\\r\\n|\\r|\\n)/", $text))
                    <div class="medicament-list">
                      <ul>
                        @foreach($lines as $ln)
                          @if(trim($ln) !== '')
                            <li>{{ $ln }}</li>
                          @endif
                        @endforeach
                      </ul>
                    </div>
                  @else
                    <span class="text-muted">Aucun médicament spécifié</span>
                  @endif
                  @if(!empty($ordonnance->dosage))
                    <div class="dosage-info">
                      <i class="bi bi-info-circle me-1"></i>{{ $ordonnance->dosage }}
                    </div>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-calendar3 text-muted"></i>
                    {{ \Carbon\Carbon::parse($ordonnance->created_at)->format('d/m/Y H:i') }}
                  </div>
                </td>
                <td>
                  <div class="ordonnance-actions">
                    <a href="{{ route('medecin.ordonnances.edit', $ordonnance->id) }}" class="btn-ordonnance-action btn-edit-ordonnance" title="Modifier l'ordonnance">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="{{ route('medecin.ordonnances.download', $ordonnance->id) }}" class="btn-ordonnance-action btn-download-ordonnance" title="Télécharger PDF">
                      <i class="bi bi-download"></i>
                    </a>
                    <form method="POST" action="{{ route('medecin.ordonnances.resend', $ordonnance->id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn-ordonnance-action btn-send-ordonnance" title="Envoyer par e-mail">
                        <i class="bi bi-envelope-arrow-up"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection

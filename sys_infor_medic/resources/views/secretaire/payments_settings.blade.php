@extends('layouts.app')

@section('content')
{{-- Header moderne pour paramètres des tarifs --}}
<div class="settings-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-gear"></i>
      <span>Paramètres des Tarifs</span>
    </div>
    <div class="header-actions">
      <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-light">
        <i class="bi bi-arrow-left me-1"></i>Retour aux paiements
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

<div class="settings-container">
  <div class="settings-header-section">
    <h5 class="mb-0"><i class="bi bi-currency-exchange me-2"></i>Configuration des prix</h5>
    <div class="settings-info">
      <small class="text-muted">Définissez les tarifs pour chaque type de service</small>
    </div>
  </div>
  
  <div class="settings-body">
    <form method="POST" action="{{ route('secretaire.payments.settings.save') }}" class="settings-form">
      @csrf
      <div class="row g-4">
        <div class="col-md-6">
          <div class="price-card consultation-card">
            <div class="price-header">
              <i class="bi bi-person-check"></i>
              <span>Consultation</span>
            </div>
            <div class="price-body">
              <label class="form-label modern-label">Ticket consultation</label>
              <div class="input-group">
                <input type="number" name="price_consultation" min="100" step="100" value="{{ $defaults['consultation'] ?? 5000 }}" class="form-control modern-input" required>
                <span class="input-group-text">XOF</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="price-card analysis-card">
            <div class="price-header">
              <i class="bi bi-clipboard-pulse"></i>
              <span>Analyse</span>
            </div>
            <div class="price-body">
              <label class="form-label modern-label">Prix des analyses</label>
              <div class="input-group">
                <input type="number" name="price_analyse" min="100" step="100" value="{{ $defaults['analyse'] ?? 10000 }}" class="form-control modern-input" required>
                <span class="input-group-text">XOF</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="price-card procedure-card">
            <div class="price-header">
              <i class="bi bi-scissors"></i>
              <span>Acte médical</span>
            </div>
            <div class="price-body">
              <label class="form-label modern-label">Prix des actes</label>
              <div class="input-group">
                <input type="number" name="price_acte" min="100" step="100" value="{{ $defaults['acte'] ?? 7000 }}" class="form-control modern-input" required>
                <span class="input-group-text">XOF</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="price-card currency-card">
            <div class="price-header">
              <i class="bi bi-cash-coin"></i>
              <span>Devise</span>
            </div>
            <div class="price-body">
              <label class="form-label modern-label">Devise utilisée</label>
              <input type="text" name="currency" value="{{ $defaults['currency'] ?? 'XOF' }}" class="form-control modern-input" required>
            </div>
          </div>
        </div>
        
        <div class="col-12">
          <div class="action-section">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="bi bi-check2-circle me-2"></i>Enregistrer les paramètres
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Styles modernes pour la page paramètres --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1200px !important; }
  
  /* Header moderne paramètres */
  .settings-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.15);
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
  
  .header-actions .btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .header-actions .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.9);
    color: #8b5cf6;
    transform: translateY(-2px);
  }
  
  /* Conteneur paramètres */
  .settings-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(139, 92, 246, 0.1);
    overflow: hidden;
  }
  
  .settings-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
  }
  
  .settings-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
    margin-bottom: 0.5rem;
  }
  
  .settings-body {
    padding: 2rem;
  }
  
  /* Cartes de prix */
  .price-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
  }
  
  .price-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  .price-header {
    padding: 1rem 1.5rem;
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .price-header i {
    font-size: 1.2rem;
  }
  
  .consultation-card .price-header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .analysis-card .price-header { background: linear-gradient(135deg, #10b981, #059669); }
  .procedure-card .price-header { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .currency-card .price-header { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .consultation-card:hover { border-color: #3b82f6; }
  .analysis-card:hover { border-color: #10b981; }
  .procedure-card:hover { border-color: #f59e0b; }
  .currency-card:hover { border-color: #8b5cf6; }
  
  .price-body {
    padding: 1.5rem;
  }
  
  .modern-label {
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .modern-input {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
  }
  
  .modern-input:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
  }
  
  .input-group-text {
    background: #f8fafc;
    border-color: #d1d5db;
    color: #6b7280;
    font-weight: 500;
  }
  
  .action-section {
    text-align: center;
    padding: 1rem;
    margin-top: 1rem;
  }
  
  .action-section .btn-success {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border: none;
    border-radius: 10px;
    padding: 0.875rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    min-width: 250px;
  }
  
  .action-section .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .settings-body {
      padding: 1.5rem;
    }
    
    .price-card {
      margin-bottom: 1rem;
    }
    
    .action-section .btn-success {
      min-width: 100%;
    }
  }
</style>
@endsection

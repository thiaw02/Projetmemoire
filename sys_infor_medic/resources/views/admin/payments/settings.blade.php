@extends('layouts.app')

@section('content')
{{-- Header moderne pour configuration des paramètres de paiement --}}
<div class="payment-settings-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-gear-fill"></i>
      <span>Configuration des Paramètres de Paiement</span>
    </div>
    <div class="header-actions">
      <a href="{{ route('admin.payments.index') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour aux paiements
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-house-door"></i>
        Dashboard
      </a>
    </div>
  </div>
</div>

{{-- Messages de session --}}
@if(session('success'))
  <div class="alert alert-success alert-modern">
    <i class="bi bi-check-circle"></i>
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-modern">
    <i class="bi bi-exclamation-circle"></i>
    {{ session('error') }}
  </div>
@endif

{{-- Contenu principal divisé en sections --}}
<div class="settings-container">
  {{-- Section Tarifs --}}
  <div class="settings-section tarifs-section">
    <div class="section-header">
      <h3><i class="bi bi-currency-exchange"></i> Configuration des Tarifs</h3>
      <p>Définissez les prix pour les différents services médicaux</p>
    </div>
    
    <form id="tarifsForm" action="{{ route('admin.payments.settings.update') }}" method="POST" class="modern-form">
      @csrf
      @method('PUT')
      
      <div class="tarifs-grid">
        <div class="tarif-card consultation-card">
          <div class="tarif-icon">
            <i class="bi bi-person-video3"></i>
          </div>
          <div class="tarif-content">
            <label class="tarif-label">Consultation Médicale</label>
            <div class="input-group">
              <input type="number" 
                     name="consultation_price" 
                     value="{{ old('consultation_price', $settings['consultation_price'] ?? 5000) }}"
                     class="form-control tarif-input" 
                     min="0" 
                     step="500"
                     required>
              <span class="input-group-text">XOF</span>
            </div>
            <small class="text-muted">Prix d'une consultation standard</small>
          </div>
        </div>
        
        <div class="tarif-card analyse-card">
          <div class="tarif-icon">
            <i class="bi bi-clipboard-data"></i>
          </div>
          <div class="tarif-content">
            <label class="tarif-label">Analyse Médicale</label>
            <div class="input-group">
              <input type="number" 
                     name="analyse_price" 
                     value="{{ old('analyse_price', $settings['analyse_price'] ?? 10000) }}"
                     class="form-control tarif-input" 
                     min="0" 
                     step="500"
                     required>
              <span class="input-group-text">XOF</span>
            </div>
            <small class="text-muted">Prix d'une analyse de laboratoire</small>
          </div>
        </div>
        
        <div class="tarif-card urgence-card">
          <div class="tarif-icon">
            <i class="bi bi-heart-pulse"></i>
          </div>
          <div class="tarif-content">
            <label class="tarif-label">Consultation d'Urgence</label>
            <div class="input-group">
              <input type="number" 
                     name="urgence_price" 
                     value="{{ old('urgence_price', $settings['urgence_price'] ?? 8000) }}"
                     class="form-control tarif-input" 
                     min="0" 
                     step="500"
                     required>
              <span class="input-group-text">XOF</span>
            </div>
            <small class="text-muted">Tarif consultation urgente</small>
          </div>
        </div>
        
        <div class="tarif-card specialty-card">
          <div class="tarif-icon">
            <i class="bi bi-person-badge"></i>
          </div>
          <div class="tarif-content">
            <label class="tarif-label">Consultation Spécialisée</label>
            <div class="input-group">
              <input type="number" 
                     name="specialty_price" 
                     value="{{ old('specialty_price', $settings['specialty_price'] ?? 12000) }}"
                     class="form-control tarif-input" 
                     min="0" 
                     step="500"
                     required>
              <span class="input-group-text">XOF</span>
            </div>
            <small class="text-muted">Consultation avec spécialiste</small>
          </div>
        </div>
      </div>
      
      <div class="form-actions">
        <button type="submit" class="btn-save">
          <i class="bi bi-check-circle"></i>
          Sauvegarder les tarifs
        </button>
        <button type="button" class="btn-preview" onclick="previewChanges()">
          <i class="bi bi-eye"></i>
          Prévisualiser
        </button>
      </div>
    </form>
  </div>
  
  {{-- Section Paramètres généraux --}}
  <div class="settings-section general-section">
    <div class="section-header">
      <h3><i class="bi bi-sliders"></i> Paramètres Généraux</h3>
      <p>Configuration générale du système de paiement</p>
    </div>
    
    <form id="generalForm" action="{{ route('admin.payments.general.update') }}" method="POST" class="modern-form">
      @csrf
      @method('PUT')
      
      <div class="settings-row">
        <div class="setting-item">
          <label class="setting-label">
            <i class="bi bi-cash-coin"></i>
            Devise par défaut
          </label>
          <select name="default_currency" class="form-select modern-select">
            <option value="XOF" {{ ($settings['default_currency'] ?? 'XOF') == 'XOF' ? 'selected' : '' }}>
              XOF - Franc CFA
            </option>
            <option value="EUR" {{ ($settings['default_currency'] ?? 'XOF') == 'EUR' ? 'selected' : '' }}>
              EUR - Euro
            </option>
            <option value="USD" {{ ($settings['default_currency'] ?? 'XOF') == 'USD' ? 'selected' : '' }}>
              USD - Dollar US
            </option>
          </select>
        </div>
        
        <div class="setting-item">
          <label class="setting-label">
            <i class="bi bi-percent"></i>
            Taux de TVA (%)
          </label>
          <input type="number" 
                 name="tax_rate" 
                 value="{{ old('tax_rate', $settings['tax_rate'] ?? 18) }}"
                 class="form-control modern-input" 
                 min="0" 
                 max="100" 
                 step="0.1">
        </div>
      </div>
      
      <div class="settings-row">
        <div class="setting-item">
          <label class="setting-label">
            <i class="bi bi-credit-card"></i>
            Modes de paiement acceptés
          </label>
          <div class="checkbox-group">
            <label class="checkbox-item">
              <input type="checkbox" name="payment_methods[]" value="cash" 
                     {{ in_array('cash', $settings['payment_methods'] ?? ['cash']) ? 'checked' : '' }}>
              <span class="checkmark"></span>
              <i class="bi bi-cash-stack"></i>
              Espèces
            </label>
            <label class="checkbox-item">
              <input type="checkbox" name="payment_methods[]" value="card" 
                     {{ in_array('card', $settings['payment_methods'] ?? []) ? 'checked' : '' }}>
              <span class="checkmark"></span>
              <i class="bi bi-credit-card"></i>
              Carte bancaire
            </label>
            <label class="checkbox-item">
              <input type="checkbox" name="payment_methods[]" value="mobile" 
                     {{ in_array('mobile', $settings['payment_methods'] ?? []) ? 'checked' : '' }}>
              <span class="checkmark"></span>
              <i class="bi bi-phone"></i>
              Paiement mobile
            </label>
          </div>
        </div>
        
        <div class="setting-item">
          <label class="setting-label">
            <i class="bi bi-receipt"></i>
            Numérotation des factures
          </label>
          <div class="input-group">
            <input type="text" 
                   name="invoice_prefix" 
                   value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'FAC') }}"
                   class="form-control modern-input" 
                   placeholder="FAC"
                   maxlength="10">
            <input type="number" 
                   name="invoice_counter" 
                   value="{{ old('invoice_counter', $settings['invoice_counter'] ?? 1) }}"
                   class="form-control modern-input" 
                   min="1">
          </div>
          <small class="text-muted">Format: [Préfixe][Numéro] ex: FAC001</small>
        </div>
      </div>
      
      <div class="form-actions">
        <button type="submit" class="btn-save">
          <i class="bi bi-gear"></i>
          Appliquer les paramètres
        </button>
      </div>
    </form>
  </div>
  
  {{-- Section sécurité et audit --}}
  <div class="settings-section security-section">
    <div class="section-header">
      <h3><i class="bi bi-shield-lock"></i> Sécurité & Audit</h3>
      <p>Configuration de la sécurité et du suivi des transactions</p>
    </div>
    
    <div class="security-options">
      <div class="security-item">
        <div class="security-content">
          <h4><i class="bi bi-file-earmark-lock"></i> Logs des transactions</h4>
          <p>Toutes les transactions sont automatiquement enregistrées</p>
          <small class="text-success">✓ Activé par défaut</small>
        </div>
        <div class="security-action">
          <a href="{{ route('admin.audit.payments') }}" class="btn-audit">
            <i class="bi bi-eye"></i>
            Consulter les logs
          </a>
        </div>
      </div>
      
      <div class="security-item">
        <div class="security-content">
          <h4><i class="bi bi-person-check"></i> Validation des paiements</h4>
          <p>Exiger une validation d'administrateur pour certains montants</p>
        </div>
        <div class="security-action">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="requireValidation" 
                   {{ ($settings['require_validation'] ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="requireValidation">Activer</label>
          </div>
        </div>
      </div>
      
      <div class="security-item">
        <div class="security-content">
          <h4><i class="bi bi-clock-history"></i> Sauvegarde automatique</h4>
          <p>Dernière sauvegarde: {{ $last_backup ?? 'Jamais' }}</p>
        </div>
        <div class="security-action">
          <button class="btn-backup" onclick="createBackup()">
            <i class="bi bi-download"></i>
            Sauvegarder
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal de prévisualisation --}}
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye"></i> Prévisualisation des tarifs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="previewContent">
        <!-- Contenu généré dynamiquement -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

{{-- Styles pour la configuration des paiements --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1400px !important; }
  
  /* Header */
  .payment-settings-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.15);
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
    color: #059669;
    transform: translateY(-2px);
  }
  
  /* Alertes modernes */
  .alert-modern {
    border-radius: 12px;
    padding: 1rem 1.5rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
  }
  
  /* Container des sections */
  .settings-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  /* Sections de configuration */
  .settings-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 2px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .settings-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .tarifs-section:hover { border-color: #059669; }
  .general-section:hover { border-color: #3b82f6; }
  .security-section:hover { border-color: #f59e0b; }
  
  .section-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #f1f5f9;
  }
  
  .section-header h3 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .section-header p {
    margin: 0;
    color: #6b7280;
  }
  
  /* Formulaires modernes */
  .modern-form {
    padding: 2rem;
  }
  
  /* Grille des tarifs */
  .tarifs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .tarif-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .tarif-card:hover {
    border-color: #059669;
    background: white;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.1);
  }
  
  .tarif-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #059669, #047857);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    margin-bottom: 1rem;
  }
  
  .tarif-label {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    display: block;
  }
  
  .tarif-input {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    padding: 0.75rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.2s ease;
  }
  
  .tarif-input:focus {
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  }
  
  /* Paramètres généraux */
  .settings-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .setting-item {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .setting-label {
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .modern-input, .modern-select {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    padding: 0.75rem;
    transition: all 0.2s ease;
  }
  
  .modern-input:focus, .modern-select:focus {
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  }
  
  /* Checkboxes personnalisées */
  .checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
  }
  
  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
  }
  
  .checkbox-item:hover {
    background: #e2e8f0;
  }
  
  .checkbox-item input[type="checkbox"] {
    display: none;
  }
  
  .checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    transition: all 0.2s ease;
  }
  
  .checkbox-item input[type="checkbox"]:checked + .checkmark {
    background: #059669;
    border-color: #059669;
  }
  
  .checkbox-item input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    color: white;
    font-weight: bold;
    font-size: 12px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  
  /* Actions de formulaire */
  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
    flex-wrap: wrap;
  }
  
  .btn-save {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
  }
  
  .btn-preview {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  /* Section sécurité */
  .security-options {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .security-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .security-item:hover {
    border-color: #f59e0b;
    background: white;
  }
  
  .security-content h4 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .security-content p {
    margin: 0 0 0.25rem 0;
    color: #6b7280;
  }
  
  .btn-audit, .btn-backup {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
  }
  
  .btn-audit:hover, .btn-backup:hover {
    transform: translateY(-2px);
    color: white;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .tarifs-grid, .settings-row {
      grid-template-columns: 1fr;
    }
    
    .form-actions {
      justify-content: center;
    }
    
    .security-item {
      flex-direction: column;
      align-items: stretch;
      gap: 1rem;
    }
  }
</style>

{{-- Scripts pour interactions --}}
<script>
  // Prévisualisation des changements
  function previewChanges() {
    const consultation = document.querySelector('input[name="consultation_price"]').value;
    const analyse = document.querySelector('input[name="analyse_price"]').value;
    const urgence = document.querySelector('input[name="urgence_price"]').value;
    const specialty = document.querySelector('input[name="specialty_price"]').value;
    
    const previewContent = `
      <div class="preview-tarifs">
        <h6>Nouveaux tarifs:</h6>
        <div class="tarif-preview-item">
          <strong>Consultation:</strong> ${Number(consultation).toLocaleString()} XOF
        </div>
        <div class="tarif-preview-item">
          <strong>Analyse:</strong> ${Number(analyse).toLocaleString()} XOF
        </div>
        <div class="tarif-preview-item">
          <strong>Urgence:</strong> ${Number(urgence).toLocaleString()} XOF
        </div>
        <div class="tarif-preview-item">
          <strong>Spécialisée:</strong> ${Number(specialty).toLocaleString()} XOF
        </div>
      </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewContent;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
  }
  
  // Créer une sauvegarde
  function createBackup() {
    if(confirm('Voulez-vous créer une sauvegarde des paramètres actuels ?')) {
      // Appel AJAX pour créer la sauvegarde
      fetch('/admin/payments/backup', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          alert('Sauvegarde créée avec succès !');
        } else {
          alert('Erreur lors de la sauvegarde');
        }
      });
    }
  }
  
  // Gestion du switch de validation
  document.getElementById('requireValidation').addEventListener('change', function() {
    const isChecked = this.checked;
    
    fetch('/admin/payments/toggle-validation', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ require_validation: isChecked })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        console.log('Paramètre mis à jour');
      }
    });
  });
</script>
@endsection
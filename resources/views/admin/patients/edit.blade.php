@extends('layouts.app')

@section('title', 'Modifier Patient')

@push('styles')
<style>
  /* ============= STYLES MODERN POUR EDIT PATIENT ============= */
  
  :root {
    --edit-primary: #3b82f6;
    --edit-primary-dark: #2563eb;
    --edit-secondary: #10b981;
    --edit-danger: #ef4444;
    --edit-success: #10b981;
    --edit-warning: #f59e0b;
    --edit-info: #06b6d4;
    --edit-gray-50: #f9fafb;
    --edit-gray-100: #f3f4f6;
    --edit-gray-200: #e5e7eb;
    --edit-gray-300: #d1d5db;
    --edit-gray-400: #9ca3af;
    --edit-gray-500: #6b7280;
    --edit-gray-600: #4b5563;
    --edit-gray-700: #374151;
    --edit-gray-800: #1f2937;
    --edit-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --edit-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --edit-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --edit-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --edit-transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --edit-border-radius: 12px;
    --edit-border-radius-lg: 20px;
  }
  
  /* ============= LAYOUT PRINCIPAL ============= */
  
  .edit-patient-container {
    padding: 1.5rem;
    min-height: calc(100vh - 80px);
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  }
  
  /* ============= HEADER MODERNE INTEGRE ============= */
  
  .edit-patient-header {
    background: linear-gradient(135deg, var(--edit-primary) 0%, var(--edit-secondary) 100%);
    border-radius: var(--edit-border-radius-lg);
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--edit-shadow-xl);
    animation: fadeInDown 0.6s ease-out;
  }
  
  .edit-patient-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 15s infinite ease-in-out;
  }
  
  .edit-patient-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    animation: float 20s infinite ease-in-out reverse;
  }
  
  .header-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    animation: pulse 3s ease-in-out infinite;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
  }
  
  .header-text h1 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
  }
  
  .header-text p {
    opacity: 0.9;
    font-size: 1.1rem;
    font-weight: 500;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }
  
  .btn-header {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--edit-border-radius);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all var(--edit-transition);
    text-decoration: none;
    backdrop-filter: blur(10px);
  }
  
  .btn-header:hover {
    background: white;
    color: var(--edit-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }
  
  /* ============= CARTE PRINCIPALE ============= */
  
  .edit-patient-card {
    background: white;
    border-radius: var(--edit-border-radius-lg);
    box-shadow: var(--edit-shadow-xl);
    overflow: hidden;
    position: relative;
    animation: fadeInUp 0.8s ease-out;
  }
  
  .edit-patient-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--edit-primary) 0%, var(--edit-secondary) 50%, var(--edit-info) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
  }
  
  .card-header-modern {
    padding: 2rem;
    border-bottom: 2px solid var(--edit-gray-100);
    background: linear-gradient(135deg, var(--edit-gray-50) 0%, #ffffff 100%);
  }
  
  .card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--edit-gray-800);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .card-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--edit-primary) 0%, var(--edit-secondary) 100%);
    border-radius: var(--edit-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    box-shadow: var(--edit-shadow-md);
  }
  
  .card-subtitle {
    color: var(--edit-gray-600);
    font-size: 1rem;
    font-weight: 500;
  }
  
  .card-body-modern {
    padding: 2rem;
  }
  
  /* ============= ALERTES MODERNES ============= */
  
  .alert-modern {
    border: none;
    border-radius: var(--edit-border-radius);
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    animation: slideInDown 0.5s ease-out;
  }
  
  .alert-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--edit-danger);
    border-left: 4px solid var(--edit-danger);
  }
  
  .alert-modern .alert-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
  }
  
  .alert-modern ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .alert-modern li {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
    position: relative;
  }
  
  .alert-modern li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: var(--edit-danger);
    font-weight: bold;
  }
  
  /* ============= SECTIONS DE FORMULAIRE ============= */
  
  .form-section {
    margin-bottom: 2.5rem;
    animation: fadeInUp 0.6s ease-out;
  }
  
  .section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--edit-gray-800);
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid var(--edit-gray-200);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
  }
  
  .section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--edit-primary), var(--edit-secondary));
    border-radius: 2px;
  }
  
  .section-title i {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, var(--edit-primary), var(--edit-secondary));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  .form-group {
    position: relative;
    animation: fadeInUp 0.5s ease-out;
  }
  
  .form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--edit-gray-700);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
  }
  
  .form-label i {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--edit-primary), var(--edit-secondary));
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
  }
  
  .form-control-modern, .form-select-modern {
    background: var(--edit-gray-50);
    border: 2px solid transparent;
    border-radius: var(--edit-border-radius);
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all var(--edit-transition);
    width: 100%;
    font-weight: 500;
    color: var(--edit-gray-800);
  }
  
  .form-control-modern:focus, .form-select-modern:focus {
    outline: none;
    border-color: var(--edit-primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
  }
  
  .form-control-modern:hover, .form-select-modern:hover {
    border-color: var(--edit-gray-300);
    background: white;
  }
  
  .form-control-modern::placeholder {
    color: var(--edit-gray-400);
    font-weight: 400;
  }
  
  textarea.form-control-modern {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
  }
  
  /* ============= BOUTONS MODERNES ============= */
  
  .form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 2px solid var(--edit-gray-100);
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
  }
  
  .btn-modern {
    font-weight: 600;
    padding: 1rem 2rem;
    border-radius: var(--edit-border-radius);
    border: none;
    transition: all var(--edit-transition);
    position: relative;
    overflow: hidden;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    text-decoration: none;
    cursor: pointer;
    backdrop-filter: blur(10px);
    min-width: 160px;
  }
  
  .btn-modern::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.6s ease;
  }
  
  .btn-modern:hover::before {
    width: 300px;
    height: 300px;
  }
  
  .btn-modern-primary {
    background: linear-gradient(135deg, var(--edit-primary) 0%, var(--edit-secondary) 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
  }
  
  .btn-modern-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
    color: white;
  }
  
  .btn-modern-secondary {
    background: linear-gradient(135deg, var(--edit-gray-100), var(--edit-gray-200));
    color: var(--edit-gray-700);
    border: 2px solid var(--edit-gray-300);
  }
  
  .btn-modern-secondary:hover {
    background: linear-gradient(135deg, var(--edit-gray-200), var(--edit-gray-300));
    transform: translateY(-2px);
    color: var(--edit-gray-800);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }
  
  /* ============= ANIMATIONS ============= */
  
  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
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
  
  @keyframes slideInDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    25% { transform: translateY(-15px) translateX(10px); }
    50% { transform: translateY(0px) translateX(-10px); }
    75% { transform: translateY(15px) translateX(5px); }
  }
  
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  @keyframes shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
  }
  
  /* ============= RESPONSIVE ============= */
  
  @media (max-width: 768px) {
    .edit-patient-container {
      padding: 1rem;
    }
    
    .edit-patient-header {
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .header-text h1 {
      font-size: 1.8rem;
    }
    
    .card-header-modern,
    .card-body-modern {
      padding: 1.5rem;
    }
    
    .form-row {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    
    .form-actions {
      flex-direction: column;
      align-items: stretch;
    }
    
    .btn-modern {
      width: 100%;
      min-width: auto;
    }
  }
  
  /* Animations différées */
  .form-section:nth-child(1) { animation-delay: 0.1s; }
  .form-section:nth-child(2) { animation-delay: 0.2s; }
  .form-section:nth-child(3) { animation-delay: 0.3s; }
  .form-section:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="edit-patient-container">
  
  <!-- Header moderne -->
  <div class="edit-patient-header">
    <div class="header-content">
      <div class="header-title">
        <div class="header-icon">
          <i class="bi bi-person-gear"></i>
        </div>
        <div class="header-text">
          <h1>Modifier le Patient</h1>
          <p>Mettre à jour les informations du patient</p>
        </div>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-header">
          <i class="bi bi-arrow-left"></i>
          Retour Dashboard
        </a>
      </div>
    </div>
  </div>
  
  <!-- Carte principale -->
  <div class="edit-patient-card">
    <div class="card-header-modern">
      <h2 class="card-title">
        <div class="card-icon">
          <i class="bi bi-person-fill"></i>
        </div>
        Informations du Patient
      </h2>
      <p class="card-subtitle">Modifiez les données personnelles et médicales du patient</p>
    </div>
    
    <div class="card-body-modern">
      @if ($errors->any())
        <div class="alert-modern alert-danger">
          <div class="alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
          <div>
            <strong>Erreurs détectées !</strong>
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')
        @php $p = $patient->patient; @endphp
        
        <!-- Section Informations personnelles -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="bi bi-person-circle"></i>
            Informations personnelles
          </h3>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-person"></i>
                Nom *
              </label>
              <input type="text" 
                     name="nom" 
                     class="form-control-modern" 
                     placeholder="Nom de famille" 
                     required 
                     value="{{ old('nom', $p->nom ?? '') }}">
            </div>
            
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-person-check"></i>
                Prénom *
              </label>
              <input type="text" 
                     name="prenom" 
                     class="form-control-modern" 
                     placeholder="Prénom" 
                     required 
                     value="{{ old('prenom', $p->prenom ?? '') }}">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-envelope"></i>
                Adresse Email *
              </label>
              <input type="email" 
                     name="email" 
                     class="form-control-modern" 
                     placeholder="email@exemple.com" 
                     required 
                     value="{{ old('email', $patient->email) }}">
            </div>
            
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-telephone"></i>
                Téléphone
              </label>
              <input type="tel" 
                     name="telephone" 
                     class="form-control-modern" 
                     placeholder="+221 XX XXX XX XX" 
                     value="{{ old('telephone', $p->telephone ?? '') }}">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-gender-ambiguous"></i>
                Sexe *
              </label>
              <select name="sexe" class="form-select-modern" required>
                @php $sx = old('sexe', $p->sexe ?? ''); @endphp
                <option value="">-- Sélectionner --</option>
                <option value="Masculin" {{ $sx=='Masculin'?'selected':'' }}>Masculin</option>
                <option value="Féminin" {{ $sx=='Féminin'?'selected':'' }}>Féminin</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-calendar-date"></i>
                Date de naissance *
              </label>
              <input type="date" 
                     name="date_naissance" 
                     class="form-control-modern" 
                     required 
                     value="{{ old('date_naissance', ($p && $p->date_naissance) ? \Carbon\Carbon::parse($p->date_naissance)->format('Y-m-d') : '') }}">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-geo-alt"></i>
                Adresse
              </label>
              <input type="text" 
                     name="adresse" 
                     class="form-control-modern" 
                     placeholder="Adresse complète" 
                     value="{{ old('adresse', $p->adresse ?? '') }}">
            </div>
            
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-building"></i>
                Service assigné
              </label>
              @php $oldServices = collect(old('services', $p?->services?->pluck('id')->all() ?? [])); @endphp
              <select name="services[]" class="form-select-modern">
                <option value="">-- Sélectionner un service --</option>
                @foreach(($services ?? []) as $srv)
                  <option value="{{ $srv->id }}" {{ $oldServices->contains($srv->id) ? 'selected' : '' }}>{{ $srv->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        
        <!-- Section Informations médicales -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="bi bi-heart-pulse"></i>
            Informations médicales
          </h3>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-droplet"></i>
                Groupe sanguin
              </label>
              <select name="groupe_sanguin" class="form-select-modern">
                @php $gs = old('groupe_sanguin', $p->groupe_sanguin ?? ''); @endphp
                <option value="">-- Sélectionner --</option>
                <option value="A+" {{ $gs=='A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ $gs=='A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ $gs=='B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ $gs=='B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ $gs=='AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ $gs=='AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ $gs=='O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ $gs=='O-' ? 'selected' : '' }}>O-</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-clipboard-pulse"></i>
              Antécédents médicaux
            </label>
            <textarea name="antecedents" 
                      class="form-control-modern" 
                      rows="4" 
                      placeholder="Décrivez les antécédents médicaux du patient (optionnel)...">{{ old('antecedents', $p->antecedents ?? '') }}</textarea>
          </div>
        </div>
        
        <!-- Section Sécurité -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="bi bi-shield-lock"></i>
            Sécurité du compte
          </h3>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-key"></i>
                Nouveau mot de passe
              </label>
              <input type="password" 
                     name="password" 
                     class="form-control-modern" 
                     placeholder="Laisser vide pour ne pas changer">
              <small class="text-muted">Minimum 8 caractères avec majuscules, minuscules et chiffres</small>
            </div>
            
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-key-fill"></i>
                Confirmer le mot de passe
              </label>
              <input type="password" 
                     name="password_confirmation" 
                     class="form-control-modern" 
                     placeholder="Confirmer le nouveau mot de passe">
            </div>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
          <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-modern-secondary">
            <i class="bi bi-x-circle"></i>
            Annuler
          </a>
          <button type="submit" class="btn-modern btn-modern-primary">
            <i class="bi bi-check-circle"></i>
            Mettre à jour le patient
          </button>
        </div>
      </form>
    </div>
  </div>
  
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Animation des champs au focus
  const inputs = document.querySelectorAll('.form-control-modern, .form-select-modern');
  
  inputs.forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'translateY(-2px)';
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'translateY(0)';
    });
  });
  
  // Validation en temps réel de l'email
  const emailInput = document.querySelector('input[name="email"]');
  if (emailInput) {
    emailInput.addEventListener('input', function() {
      const email = this.value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (email && !emailRegex.test(email)) {
        this.style.borderColor = 'var(--edit-danger)';
        this.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
      } else {
        this.style.borderColor = 'transparent';
        this.style.boxShadow = '';
      }
    });
  }
  
  // Validation des mots de passe
  const passwordInput = document.querySelector('input[name="password"]');
  const confirmInput = document.querySelector('input[name="password_confirmation"]');
  
  function validatePasswords() {
    if (passwordInput.value || confirmInput.value) {
      if (passwordInput.value !== confirmInput.value) {
        confirmInput.style.borderColor = 'var(--edit-danger)';
        confirmInput.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
      } else {
        confirmInput.style.borderColor = 'var(--edit-success)';
        confirmInput.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
      }
    }
  }
  
  if (passwordInput && confirmInput) {
    passwordInput.addEventListener('input', validatePasswords);
    confirmInput.addEventListener('input', validatePasswords);
  }
  
  // Format automatique du téléphone
  const phoneInput = document.querySelector('input[name="telephone"]');
  if (phoneInput) {
    phoneInput.addEventListener('input', function() {
      let value = this.value.replace(/\D/g, '');
      if (value.length > 0) {
        if (value.length <= 9) {
          value = value.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        }
        this.value = value;
      }
    });
  }
  
  // Animation du bouton de soumission
  const submitBtn = document.querySelector('button[type="submit"]');
  if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
      const form = this.closest('form');
      
      // Vérifications des champs obligatoires
      const requiredFields = form.querySelectorAll('input[required], select[required]');
      let hasErrors = false;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.borderColor = 'var(--edit-danger)';
          field.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
          hasErrors = true;
        }
      });
      
      if (hasErrors) {
        e.preventDefault();
        // Créer une notification moderne
        const notification = document.createElement('div');
        notification.className = 'alert-modern alert-danger';
        notification.innerHTML = `
          <div class="alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
          <div>
            <strong>Attention !</strong>
            <p>Veuillez remplir tous les champs obligatoires marqués d'un astérisque (*).</p>
          </div>
        `;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        
        document.body.appendChild(notification);
        
        // Supprimer la notification après 5 secondes
        setTimeout(() => {
          if (notification.parentNode) {
            notification.remove();
          }
        }, 5000);
        
        return;
      }
      
      // Animation de chargement
      setTimeout(() => {
        const icon = this.querySelector('i');
        const text = this.querySelector('span') || this;
        
        if (icon) {
          icon.className = 'bi bi-hourglass-split';
        }
        if (this.childNodes.length > 1) {
          this.childNodes[this.childNodes.length - 1].textContent = 'Mise à jour...';
        } else {
          this.innerHTML = '<i class="bi bi-hourglass-split"></i> Mise à jour...';
        }
        this.disabled = true;
        this.style.opacity = '0.7';
      }, 100);
    });
  }
  
  // Effet de particules sur le header au clic
  const header = document.querySelector('.edit-patient-header');
  if (header) {
    header.addEventListener('click', function(e) {
      const rect = this.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const ripple = document.createElement('div');
      ripple.style.position = 'absolute';
      ripple.style.borderRadius = '50%';
      ripple.style.background = 'rgba(255, 255, 255, 0.3)';
      ripple.style.transform = 'scale(0)';
      ripple.style.animation = 'ripple 0.6s linear';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      ripple.style.width = '100px';
      ripple.style.height = '100px';
      ripple.style.marginLeft = '-50px';
      ripple.style.marginTop = '-50px';
      ripple.style.pointerEvents = 'none';
      
      this.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  }
});

// Animation ripple CSS
const style = document.createElement('style');
style.textContent = `
  @keyframes ripple {
    to {
      transform: scale(2);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);
</script>
@endpush
@extends('layouts.app')

@section('content')
{{-- Header moderne pour création d'utilisateur --}}
<div class="create-user-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-person-plus-fill"></i>
      <span>Créer un Utilisateur</span>
    </div>
    <div class="header-actions">
      <a href="{{ route('admin.users.index') }}" class="action-btn">
        <i class="bi bi-list"></i>
        Liste des utilisateurs
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour Dashboard
      </a>
    </div>
  </div>
</div>

{{-- Messages d'erreur modernes --}}
@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
    <div class="alert-icon">
      <i class="bi bi-exclamation-triangle"></i>
    </div>
    <div class="alert-content">
      <h6 class="mb-2">Erreurs de validation :</h6>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
    <div class="alert-icon">
      <i class="bi bi-check-circle"></i>
    </div>
    <div class="alert-content">
      {{ session('success') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

{{-- Formulaire de création moderne --}}
<div class="form-container">
  <div class="form-header">
    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Informations de l'utilisateur</h5>
    <div class="form-progress">
      <div class="progress-step active" data-step="1">1</div>
      <div class="progress-line"></div>
      <div class="progress-step" data-step="2">2</div>
      <div class="progress-line"></div>
      <div class="progress-step" data-step="3">3</div>
      <div class="progress-line"></div>
      <div class="progress-step" data-step="4">4</div>
    </div>
  </div>
  
  <div class="form-body">
    <form action="{{ route('admin.users.store') }}" method="POST" class="modern-form" id="createUserForm">
      @csrf
      
      {{-- Étape 1: Informations de base --}}
      <div class="form-step active" data-step="1">
        <div class="step-header">
          <h6><i class="bi bi-person-circle me-2"></i>Informations de base</h6>
          <p class="text-muted">Renseignez les informations principales de l'utilisateur</p>
        </div>
        
        <div class="form-grid">
          <div class="form-group">
            <label for="name" class="form-label required">
              <i class="bi bi-person me-2"></i>
              Nom complet
            </label>
            <input type="text" name="name" id="name" class="form-control" 
                   required value="{{ old('name') }}" 
                   placeholder="Prénom et nom de famille">
            <div class="form-feedback"></div>
          </div>
          
          <div class="form-group">
            <label for="email" class="form-label required">
              <i class="bi bi-envelope me-2"></i>
              Adresse email
            </label>
            <input type="email" name="email" id="email" class="form-control" 
                   required value="{{ old('email') }}" 
                   placeholder="utilisateur@exemple.com">
            <div class="form-feedback"></div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="role" class="form-label required">
            <i class="bi bi-person-badge me-2"></i>
            Rôle dans le système
          </label>
          <select name="role" id="role" class="form-control" required>
            <option value="">-- Choisir un rôle --</option>
            <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>
              <i class="bi bi-shield-check"></i> Administrateur
            </option>
            <option value="medecin" {{ old('role')=='medecin' ? 'selected' : '' }}>
              <i class="bi bi-heart-pulse"></i> Médecin
            </option>
            <option value="infirmier" {{ old('role')=='infirmier' ? 'selected' : '' }}>
              <i class="bi bi-bandaid"></i> Infirmier
            </option>
            <option value="secretaire" {{ old('role')=='secretaire' ? 'selected' : '' }}>
              <i class="bi bi-person-workspace"></i> Secrétaire
            </option>
          </select>
          <div class="form-feedback"></div>
        </div>
        
        <div class="form-group">
          <div class="form-switch-modern">
            <input type="checkbox" id="active" name="active" value="1" checked class="switch-input">
            <label for="active" class="switch-label">
              <span class="switch-slider"></span>
              <span class="switch-text">
                <i class="bi bi-person-check me-2"></i>
                Compte actif dès la création
              </span>
            </label>
          </div>
        </div>
      </div>
      
      {{-- Étape 2: Informations personnelles --}}
      <div class="form-step" data-step="2">
        <div class="step-header">
          <h6><i class="bi bi-person-lines-fill me-2"></i>Informations personnelles</h6>
          <p class="text-muted">Détails personnels et de contact</p>
        </div>
        
        <div class="form-grid">
          <div class="form-group">
            <label for="phone" class="form-label">
              <i class="bi bi-telephone me-2"></i>
              Téléphone personnel
            </label>
            <input type="tel" name="phone" id="phone" class="form-control" 
                   value="{{ old('phone') }}" 
                   placeholder="+221 77 000 00 00">
            <div class="form-feedback"></div>
          </div>
          
          <div class="form-group">
            <label for="gender" class="form-label">
              <i class="bi bi-gender-ambiguous me-2"></i>
              Genre
            </label>
            <select name="gender" id="gender" class="form-control">
              <option value="">-- Choisir --</option>
              <option value="Masculin" {{ old('gender')=='Masculin' ? 'selected' : '' }}>Masculin</option>
              <option value="Féminin" {{ old('gender')=='Féminin' ? 'selected' : '' }}>Féminin</option>
              <option value="Autre" {{ old('gender')=='Autre' ? 'selected' : '' }}>Autre</option>
            </select>
            <div class="form-feedback"></div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="date_of_birth" class="form-label">
            <i class="bi bi-calendar-date me-2"></i>
            Date de naissance
          </label>
          <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" 
                 value="{{ old('date_of_birth') }}">
          <div class="form-feedback"></div>
        </div>
        
        <div class="form-group">
          <label for="address" class="form-label">
            <i class="bi bi-geo-alt me-2"></i>
            Adresse
          </label>
          <textarea name="address" id="address" class="form-control" rows="3" 
                    placeholder="Adresse complète">{{ old('address') }}</textarea>
          <div class="form-feedback"></div>
        </div>
        
        <div class="form-grid">
          <div class="form-group">
            <label for="emergency_contact" class="form-label">
              <i class="bi bi-person-exclamation me-2"></i>
              Contact d'urgence
            </label>
            <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" 
                   value="{{ old('emergency_contact') }}" 
                   placeholder="Nom du contact d'urgence">
            <div class="form-feedback"></div>
          </div>
          
          <div class="form-group">
            <label for="emergency_phone" class="form-label">
              <i class="bi bi-telephone-fill me-2"></i>
              Téléphone d'urgence
            </label>
            <input type="tel" name="emergency_phone" id="emergency_phone" class="form-control" 
                   value="{{ old('emergency_phone') }}" 
                   placeholder="+221 77 000 00 00">
            <div class="form-feedback"></div>
          </div>
        </div>
      </div>
      
      {{-- Étape 3: Informations professionnelles --}}
      <div class="form-step" data-step="3">
        <div class="step-header">
          <h6><i class="bi bi-briefcase me-2"></i>Informations professionnelles</h6>
          <p class="text-muted">Détails professionnels et spécifiques au rôle</p>
        </div>
        
        <div class="form-grid">
          <div class="form-group">
            <label for="department" class="form-label">
              <i class="bi bi-building me-2"></i>
              Département/Service
            </label>
            <input type="text" name="department" id="department" class="form-control" 
                   value="{{ old('department') }}" 
                   placeholder="Ex: Ressources Humaines, IT...">
            <div class="form-feedback"></div>
          </div>
          
          <div class="form-group">
            <label for="hire_date" class="form-label">
              <i class="bi bi-calendar-check me-2"></i>
              Date d'embauche
            </label>
            <input type="date" name="hire_date" id="hire_date" class="form-control" 
                   value="{{ old('hire_date') }}">
            <div class="form-feedback"></div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="salary" class="form-label">
            <i class="bi bi-currency-dollar me-2"></i>
            Salaire (XOF)
          </label>
          <input type="number" name="salary" id="salary" class="form-control" 
                 value="{{ old('salary') }}" 
                 placeholder="0" min="0" step="100">
          <div class="form-feedback"></div>
        </div>
        
        <div class="form-group">
          <label for="notes" class="form-label">
            <i class="bi bi-sticky me-2"></i>
            Notes/Remarques
          </label>
          <textarea name="notes" id="notes" class="form-control" rows="3" 
                    placeholder="Informations supplémentaires...">{{ old('notes') }}</textarea>
          <div class="form-feedback"></div>
        </div>
        
        {{-- Champs pour médecin --}}
        <div class="role-fields role-medecin d-none">
          <div class="role-card">
            <div class="role-card-header">
              <i class="bi bi-heart-pulse"></i>
              <span>Informations Médecin</span>
            </div>
            <div class="role-card-body">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">
                    <i class="bi bi-medical-bag me-2"></i>
                    Spécialité
                  </label>
                  <input type="text" name="specialite" class="form-control" 
                         value="{{ old('specialite') }}" 
                         placeholder="Ex: Cardiologie, Neurologie...">
                </div>
                
                <div class="form-group">
                  <label class="form-label">
                    <i class="bi bi-card-text me-2"></i>
                    Matricule
                  </label>
                  <input type="text" name="matricule" class="form-control" 
                         value="{{ old('matricule') }}" 
                         placeholder="Numéro d'identification">
                </div>
              </div>
              
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-building me-2"></i>
                  Cabinet/Service
                </label>
                <input type="text" name="cabinet" class="form-control" 
                       value="{{ old('cabinet') }}" 
                       placeholder="Nom du cabinet ou service">
              </div>
              
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-clock me-2"></i>
                  Horaires de consultation
                </label>
                <textarea name="horaires" class="form-control" rows="3" 
                          placeholder="Ex: Lundi-Vendredi 9h-17h, Samedi 9h-13h">{{ old('horaires') }}</textarea>
              </div>
            </div>
          </div>
        </div>
        
        {{-- Champs pour personnel (secrétaire, infirmier, admin) --}}
        <div class="role-fields role-staff d-none">
          <div class="role-card">
            <div class="role-card-header">
              <i class="bi bi-person-workspace"></i>
              <span>Informations Professionnelles</span>
            </div>
            <div class="role-card-body">
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-telephone me-2"></i>
                  Téléphone professionnel
                </label>
                <input type="text" name="pro_phone" class="form-control" 
                       value="{{ old('pro_phone') }}" 
                       placeholder="Ex: +221 77 000 00 00">
              </div>
            </div>
          </div>
        </div>
        
        {{-- Message si aucun rôle sélectionné --}}
        <div class="no-role-selected">
          <div class="text-center py-4">
            <i class="bi bi-arrow-up-circle text-muted" style="font-size: 3rem;"></i>
            <h6 class="text-muted mt-3">Veuillez d'abord sélectionner un rôle</h6>
            <p class="text-muted">Revenez à l'étape précédente pour choisir le rôle de l'utilisateur</p>
          </div>
        </div>
      </div>
      
      {{-- Étape 4: Sécurité --}}
      <div class="form-step" data-step="4">
        <div class="step-header">
          <h6><i class="bi bi-shield-lock me-2"></i>Sécurité et accès</h6>
          <p class="text-muted">Définissez les paramètres de sécurité du compte</p>
        </div>
        
        <div class="security-card">
          <div class="form-group">
            <label for="password" class="form-label required">
              <i class="bi bi-key me-2"></i>
              Mot de passe
            </label>
            <div class="password-input">
              <input type="password" name="password" id="password" class="form-control" required>
              <button type="button" class="password-toggle" onclick="togglePassword('password')">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="password-strength">
              <div class="strength-bar">
                <div class="strength-fill"></div>
              </div>
              <small class="strength-text">Force du mot de passe</small>
            </div>
          </div>
          
          <div class="form-group">
            <label for="password_confirmation" class="form-label required">
              <i class="bi bi-shield-check me-2"></i>
              Confirmer le mot de passe
            </label>
            <div class="password-input">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
              <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="form-feedback"></div>
          </div>
        </div>
        
        <div class="security-tips">
          <h6><i class="bi bi-lightbulb me-2"></i>Conseils de sécurité</h6>
          <ul>
            <li>Le mot de passe doit contenir au moins 8 caractères</li>
            <li>Utilisez une combinaison de lettres, chiffres et symboles</li>
            <li>Evitez les mots de passe trop simples ou prévisibles</li>
          </ul>
        </div>
      </div>
      
      {{-- Navigation entre les étapes --}}
      <div class="form-navigation">
        <button type="button" class="btn-prev" onclick="previousStep()">
          <i class="bi bi-arrow-left me-2"></i>
          Précédent
        </button>
        
        <button type="button" class="btn-next" onclick="nextStep()">
          Suivant
          <i class="bi bi-arrow-right ms-2"></i>
        </button>
        
        <button type="submit" class="btn-submit d-none">
          <i class="bi bi-person-plus me-2"></i>
          Créer l'utilisateur
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Styles modernes complets pour la création d'utilisateur --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1200px !important; }
  
  /* Header moderne création utilisateur */
  .create-user-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
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
    color: #10b981;
    transform: translateY(-2px);
  }
  
  /* Alertes modernes */
  .modern-alert {
    border-radius: 12px;
    border: none;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
  }
  
  .alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
  }
  
  .alert-danger .alert-icon {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }
  
  .alert-success .alert-icon {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
  }
  
  .alert-content {
    flex: 1;
  }
  
  .alert-content h6 {
    color: inherit;
    font-weight: 600;
  }
  
  .alert-content ul {
    margin-left: 1rem;
  }
  
  /* Conteneur formulaire */
  .form-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(16, 185, 129, 0.1);
    overflow: hidden;
  }
  
  .form-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .form-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .progress-step {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .progress-step.active {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  }
  
  .progress-line {
    width: 60px;
    height: 2px;
    background: #e5e7eb;
    transition: all 0.3s ease;
  }
  
  .progress-line.active {
    background: linear-gradient(135deg, #10b981, #059669);
  }
  
  .form-body {
    padding: 2rem;
  }
  
  /* Étapes du formulaire
     Rendre toutes les étapes visibles pour éviter les soucis d'affichage
     si le JavaScript ne s'exécute pas */
  .form-step {
    display: block;
  }
  
  .form-step.active {
    display: block;
    animation: fadeInUp 0.5s ease;
  }
  
  .step-header {
    margin-bottom: 2rem;
    text-align: center;
  }
  
  .step-header h6 {
    color: #1f2937;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  
  .step-header p {
    color: #6b7280;
    margin: 0;
  }
  
  /* Grille de formulaire */
  .form-grid {
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
  
  .form-label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
  }
  
  .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
    font-size: 1rem;
  }
  
  .form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    background: white;
  }
  
  .form-control:invalid {
    border-color: #ef4444;
  }
  
  .form-feedback {
    margin-top: 0.25rem;
    font-size: 0.875rem;
    min-height: 1.25rem;
  }
  
  /* Switch moderne */
  .form-switch-modern {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
  }
  
  .switch-input {
    display: none;
  }
  
  .switch-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    margin: 0;
  }
  
  .switch-slider {
    width: 50px;
    height: 26px;
    background: #e5e7eb;
    border-radius: 26px;
    position: relative;
    transition: all 0.3s ease;
  }
  
  .switch-slider::before {
    content: '';
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .switch-input:checked + .switch-label .switch-slider {
    background: linear-gradient(135deg, #10b981, #059669);
  }
  
  .switch-input:checked + .switch-label .switch-slider::before {
    transform: translateX(24px);
  }
  
  .switch-text {
    font-weight: 500;
    color: #374151;
  }
  
  /* Cartes de rôle */
  .role-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  
  .role-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
  }
  
  .role-card-body {
    padding: 1.5rem;
  }
  
  /* Sécurité */
  .security-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
  }
  
  .password-input {
    position: relative;
  }
  
  .password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
  }
  
  .password-toggle:hover {
    color: #374151;
  }
  
  .password-strength {
    margin-top: 0.75rem;
  }
  
  .strength-bar {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 0.5rem;
  }
  
  .strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
  }
  
  .strength-text {
    color: #6b7280;
    font-size: 0.875rem;
  }
  
  .security-tips {
    background: #fffbeb;
    border: 1px solid #fed7aa;
    border-radius: 12px;
    padding: 1.5rem;
  }
  
  .security-tips h6 {
    color: #92400e;
    margin-bottom: 1rem;
  }
  
  .security-tips ul {
    margin: 0;
    padding-left: 1.5rem;
    color: #92400e;
  }
  
  .security-tips li {
    margin-bottom: 0.5rem;
  }
  
  /* Navigation */
  .form-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
    margin-top: 2rem;
  }
  
  .btn-prev,
  .btn-next,
  .btn-submit {
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-prev {
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
  }
  
  .btn-prev:hover:not(:disabled) {
    background: white;
    border-color: #10b981;
    color: #10b981;
  }
  
  .btn-prev:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .btn-next,
  .btn-submit {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
  }
  
  .btn-next:hover,
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  /* Message aucun rôle */
  .no-role-selected {
    display: block;
  }
  
  .role-fields:not(.d-none) ~ .no-role-selected {
    display: none;
  }
  
  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .form-header {
      flex-direction: column;
      gap: 1.5rem;
      text-align: center;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .form-navigation {
      flex-direction: column;
      gap: 1rem;
    }
  }
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
    
    // Fonction pour afficher/masquer les étapes
    function showStep(step) {
        // Ne pas masquer les étapes: elles restent toutes visibles pour fiabilité
        document.querySelectorAll('.form-step').forEach(s => s.classList.add('active'));
        updateProgress(step);
        updateNavigation(step);
    }
    
    // Fonction pour mettre à jour la barre de progression
    function updateProgress(step) {
        document.querySelectorAll('.progress-step').forEach((s, index) => {
            if (index + 1 <= step) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
        
        document.querySelectorAll('.progress-line').forEach((line, index) => {
            if (index + 1 < step) {
                line.classList.add('active');
            } else {
                line.classList.remove('active');
            }
        });
    }
    
    // Fonction pour mettre à jour la navigation
    function updateNavigation(step) {
        const prevBtn = document.querySelector('.btn-prev');
        const nextBtn = document.querySelector('.btn-next');
        const submitBtn = document.querySelector('.btn-submit');
        
        // Bouton précédent
        prevBtn.disabled = step === 1;
        
        // Toujours afficher le bouton Soumettre et masquer navigation si souhaité
        nextBtn.classList.add('d-none');
        submitBtn.classList.remove('d-none');
    }
    
    // Navigation vers l'étape suivante
    window.nextStep = function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    };
    
    // Navigation vers l'étape précédente
    window.previousStep = function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    };
    
    // Validation de l'étape courante
    function validateCurrentStep() {
        const currentStepEl = document.querySelector(`[data-step="${currentStep}"]`);
        const requiredFields = currentStepEl.querySelectorAll('input[required], select[required]');
        
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Ce champ est obligatoire');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                clearFieldError(field);
            }
        });
        
        // Validation spécifique pour l'email
        const emailField = currentStepEl.querySelector('#email');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                emailField.classList.add('is-invalid');
                showFieldError(emailField, 'Veuillez saisir une adresse email valide');
                isValid = false;
            }
        }
        
        // Validation spécifique pour les mots de passe
        const passwordField = currentStepEl.querySelector('#password');
        const confirmField = currentStepEl.querySelector('#password_confirmation');
        
        if (passwordField && passwordField.value) {
            if (passwordField.value.length < 8) {
                passwordField.classList.add('is-invalid');
                showFieldError(passwordField, 'Le mot de passe doit contenir au moins 8 caractères');
                isValid = false;
            }
        }
        
        if (confirmField && confirmField.value) {
            if (passwordField.value !== confirmField.value) {
                confirmField.classList.add('is-invalid');
                showFieldError(confirmField, 'Les mots de passe ne correspondent pas');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Afficher une erreur de champ
    function showFieldError(field, message) {
        const feedback = field.parentNode.querySelector('.form-feedback');
        if (feedback) {
            feedback.textContent = message;
            feedback.className = 'form-feedback text-danger';
        }
    }
    
    // Effacer l'erreur de champ
    function clearFieldError(field) {
        const feedback = field.parentNode.querySelector('.form-feedback');
        if (feedback) {
            feedback.textContent = '';
            feedback.className = 'form-feedback';
        }
    }
    
    // Gestion des champs selon le rôle
    const roleSelect = document.getElementById('role');
    function toggleRoleFields() {
        const selectedRole = roleSelect.value;
        
        // Masquer tous les champs de rôle
        document.querySelectorAll('.role-fields').forEach(el => {
            el.classList.add('d-none');
        });
        
        // Afficher les champs appropriés
        if (selectedRole === 'medecin') {
            document.querySelectorAll('.role-medecin').forEach(el => {
                el.classList.remove('d-none');
            });
        } else if (['secretaire', 'infirmier', 'admin'].includes(selectedRole)) {
            document.querySelectorAll('.role-staff').forEach(el => {
                el.classList.remove('d-none');
            });
        }
    }
    
    roleSelect.addEventListener('change', toggleRoleFields);
    toggleRoleFields();
    
    // Fonction pour basculer la visibilité du mot de passe
    window.togglePassword = function(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'bi bi-eye';
        }
    };
    
    // Indicateur de force du mot de passe
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.querySelector('.strength-fill');
            const strengthText = document.querySelector('.strength-text');
            
            let strength = 0;
            let strengthLabel = 'Très faible';
            let strengthColor = '#ef4444';
            
            // Calcul de la force
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 12.5;
            if (password.match(/[^\w\s]/)) strength += 12.5;
            
            // Déterminer le label et la couleur
            if (strength >= 80) {
                strengthLabel = 'Très fort';
                strengthColor = '#10b981';
            } else if (strength >= 60) {
                strengthLabel = 'Fort';
                strengthColor = '#059669';
            } else if (strength >= 40) {
                strengthLabel = 'Moyen';
                strengthColor = '#f59e0b';
            } else if (strength >= 20) {
                strengthLabel = 'Faible';
                strengthColor = '#f97316';
            }
            
            // Appliquer les styles
            strengthFill.style.width = strength + '%';
            strengthFill.style.backgroundColor = strengthColor;
            strengthText.textContent = `Force: ${strengthLabel}`;
            strengthText.style.color = strengthColor;
        });
    }
    
    // Validation en temps réel des champs
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
                showFieldError(this, 'Ce champ est obligatoire');
            } else {
                this.classList.remove('is-invalid');
                clearFieldError(this);
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                clearFieldError(this);
            }
        });
    });
    
    // Initialiser l'interface
    showStep(1);
});
</script>
@endsection

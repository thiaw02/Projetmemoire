@extends('layouts.app')

@section('content')
{{-- Header moderne pour modification d'utilisateur --}}
<div class="edit-user-header">
  <div class="header-content">
    <div class="header-title">
      <div class="title-icon">
        <i class="bi bi-person-gear"></i>
      </div>
      <div class="title-text">
        <h1>Modifier l'Utilisateur</h1>
        <div class="user-badge">
          <i class="bi bi-person-circle"></i>
          <span>{{ $user->name }}</span>
          <span class="role-indicator role-{{ $user->role }}">
            @switch($user->role)
              @case('admin') Administrateur @break
              @case('medecin') Médecin @break
              @case('infirmier') Infirmier @break
              @case('secretaire') Secrétaire @break
              @default Utilisateur
            @endswitch
          </span>
        </div>
      </div>
    </div>
    <div class="header-actions">
      <a href="{{ route('admin.users.index') }}" class="action-btn secondary">
        <i class="bi bi-list"></i>
        <span>Liste des utilisateurs</span>
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn primary">
        <i class="bi bi-arrow-left"></i>
        <span>Retour Dashboard</span>
      </a>
    </div>
  </div>
</div>

  {{-- Messages de feedback --}}
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show modern-alert animated slideInDown" role="alert">
      <div class="alert-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </div>
      <div class="alert-content">
        <h6 class="alert-title mb-2">Erreurs de validation détectées</h6>
        <ul class="error-list mb-0">
          @foreach($errors->all() as $error)
            <li><i class="bi bi-x-circle"></i> {{ $error }}</li>
          @endforeach
        </ul>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show modern-alert animated slideInDown" role="alert">
      <div class="alert-icon">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <div class="alert-content">
        <h6 class="alert-title mb-1">Succès</h6>
        <p class="mb-0">{{ session('success') }}</p>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
  @endif

{{-- Container principal du formulaire --}}
<div class="form-container">
  <div class="form-content">

          <form id="userEditForm" action="{{ route('admin.users.update', $user->id) }}" method="POST" class="modern-form">
            @csrf
            @method('PUT')
            
            {{-- Navigation par onglets --}}
            <div class="form-tabs">
              <button type="button" class="tab-btn active" onclick="switchFormTab('general')">
                <i class="bi bi-person"></i>
                <span>Informations générales</span>
              </button>
              <button type="button" class="tab-btn" onclick="switchFormTab('personal')">
                <i class="bi bi-person-lines-fill"></i>
                <span>Informations personnelles</span>
              </button>
              <button type="button" class="tab-btn" onclick="switchFormTab('professional')">
                <i class="bi bi-briefcase"></i>
                <span>Informations professionnelles</span>
              </button>
              <button type="button" class="tab-btn" onclick="switchFormTab('security')">
                <i class="bi bi-shield-lock"></i>
                <span>Sécurité</span>
              </button>
            </div>
            
            {{-- Onglet: Informations générales --}}
            <div class="tab-content active" id="general-tab">
              <div class="form-section">
                <div class="section-header">
                  <i class="bi bi-person-badge"></i>
                  <h3>Identité et Contact</h3>
                  <p>Informations de base de l'utilisateur</p>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="name" class="form-label required">
                      <i class="bi bi-person"></i>
                      Nom complet
                    </label>
                    <div class="input-wrapper">
                      <input type="text" 
                             name="name" 
                             id="name" 
                             class="form-control" 
                             required 
                             value="{{ old('name', $user->name) }}"
                             placeholder="Saisir le nom complet">
                      <div class="input-feedback"></div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="email" class="form-label required">
                      <i class="bi bi-envelope"></i>
                      Adresse email
                    </label>
                    <div class="input-wrapper">
                      <input type="email" 
                             name="email" 
                             id="email" 
                             class="form-control" 
                             required 
                             value="{{ old('email', $user->email) }}"
                             placeholder="email@exemple.com">
                      <div class="input-feedback"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-section">
                <div class="section-header">
                  <i class="bi bi-gear"></i>
                  <h3>Paramètres du Compte</h3>
                  <p>Rôle et statut d'activation</p>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="role" class="form-label required">
                      <i class="bi bi-person-badge"></i>
                      Rôle de l'utilisateur
                    </label>
                    <div class="select-wrapper">
                      <select name="role" id="role" class="form-select" required>
                        <option value="" disabled>Choisir un rôle...</option>
                        <option value="admin" 
                                {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}
                                data-icon="bi-shield-check"
                                data-color="#dc2626">
                          🔒 Administrateur
                        </option>
                        <option value="medecin" 
                                {{ (old('role', $user->role) == 'medecin') ? 'selected' : '' }}
                                data-icon="bi-heart-pulse"
                                data-color="#059669">
                          ❤️‍🩹 Médecin
                        </option>
                        <option value="infirmier" 
                                {{ (old('role', $user->role) == 'infirmier') ? 'selected' : '' }}
                                data-icon="bi-person-plus"
                                data-color="#3b82f6">
                          🚑 Infirmier
                        </option>
                        <option value="secretaire" 
                                {{ (old('role', $user->role) == 'secretaire') ? 'selected' : '' }}
                                data-icon="bi-pencil-square"
                                data-color="#8b5cf6">
                          📝 Secrétaire
                        </option>
                      </select>
                      <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="toggle-section">
                      <div class="toggle-header">
                        <div class="toggle-info">
                          <label class="form-label" for="active">
                            <i class="bi bi-power"></i>
                            Statut du compte
                          </label>
                          <p class="form-description">Activer ou désactiver l'accès utilisateur</p>
                        </div>
                        <div class="modern-toggle">
                          <input type="checkbox" 
                                 id="active" 
                                 name="active" 
                                 value="1" 
                                 {{ old('active', $user->active) ? 'checked' : '' }}>
                          <label for="active" class="toggle-label">
                            <span class="toggle-switch"></span>
                            <span class="toggle-text">Actif</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Onglet: Informations personnelles --}}
            <div class="tab-content" id="personal-tab">
              <div class="form-section">
                <div class="section-header">
                  <i class="bi bi-person-lines-fill"></i>
                  <h3>Informations Personnelles</h3>
                  <p>Détails personnels et de contact</p>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="phone" class="form-label">
                      <i class="bi bi-telephone"></i>
                      Téléphone personnel
                    </label>
                    <div class="input-wrapper">
                      <input type="tel" 
                             name="phone" 
                             id="phone" 
                             class="form-control" 
                             value="{{ old('phone', $user->phone) }}"
                             placeholder="+221 77 000 00 00">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="gender" class="form-label">
                      <i class="bi bi-gender-ambiguous"></i>
                      Genre
                    </label>
                    <div class="select-wrapper">
                      <select name="gender" id="gender" class="form-select">
                        <option value="">-- Choisir --</option>
                        <option value="Masculin" {{ (old('gender', $user->gender) == 'Masculin') ? 'selected' : '' }}>Masculin</option>
                        <option value="Féminin" {{ (old('gender', $user->gender) == 'Féminin') ? 'selected' : '' }}>Féminin</option>
                        <option value="Autre" {{ (old('gender', $user->gender) == 'Autre') ? 'selected' : '' }}>Autre</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="date_of_birth" class="form-label">
                      <i class="bi bi-calendar-date"></i>
                      Date de naissance
                    </label>
                    <div class="input-wrapper">
                      <input type="date" 
                             name="date_of_birth" 
                             id="date_of_birth" 
                             class="form-control" 
                             value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="hire_date" class="form-label">
                      <i class="bi bi-calendar-check"></i>
                      Date d'embauche
                    </label>
                    <div class="input-wrapper">
                      <input type="date" 
                             name="hire_date" 
                             id="hire_date" 
                             class="form-control" 
                             value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}">
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="address" class="form-label">
                    <i class="bi bi-geo-alt"></i>
                    Adresse
                  </label>
                  <div class="input-wrapper">
                    <textarea name="address" 
                              id="address" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Adresse complète">{{ old('address', $user->address) }}</textarea>
                  </div>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="emergency_contact" class="form-label">
                      <i class="bi bi-person-exclamation"></i>
                      Contact d'urgence
                    </label>
                    <div class="input-wrapper">
                      <input type="text" 
                             name="emergency_contact" 
                             id="emergency_contact" 
                             class="form-control" 
                             value="{{ old('emergency_contact', $user->emergency_contact) }}"
                             placeholder="Nom du contact d'urgence">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="emergency_phone" class="form-label">
                      <i class="bi bi-telephone-fill"></i>
                      Téléphone d'urgence
                    </label>
                    <div class="input-wrapper">
                      <input type="tel" 
                             name="emergency_phone" 
                             id="emergency_phone" 
                             class="form-control" 
                             value="{{ old('emergency_phone', $user->emergency_phone) }}"
                             placeholder="+221 77 000 00 00">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Onglet: Informations professionnelles --}}
            <div class="tab-content" id="professional-tab">
              <div class="form-section" id="roleFields">
                <div class="section-header">
                  <i class="bi bi-briefcase"></i>
                  <h3>Détails Professionnels</h3>
                  <p>Informations professionnelles et spécifiques au rôle</p>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="department" class="form-label">
                      <i class="bi bi-building"></i>
                      Département/Service
                    </label>
                    <div class="input-wrapper">
                      <input type="text" 
                             name="department" 
                             id="department" 
                             class="form-control" 
                             value="{{ old('department', $user->department) }}"
                             placeholder="Ex: Ressources Humaines, IT...">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="salary" class="form-label">
                      <i class="bi bi-currency-dollar"></i>
                      Salaire (XOF)
                    </label>
                    <div class="input-wrapper">
                      <input type="number" 
                             name="salary" 
                             id="salary" 
                             class="form-control" 
                             value="{{ old('salary', $user->salary) }}"
                             placeholder="0" min="0" step="100">
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="notes" class="form-label">
                    <i class="bi bi-sticky"></i>
                    Notes/Remarques
                  </label>
                  <div class="input-wrapper">
                    <textarea name="notes" 
                              id="notes" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Informations supplémentaires...">{{ old('notes', $user->notes) }}</textarea>
                  </div>
                </div>
                
                {{-- Champs spécifiques au médecin --}}
                <div class="role-specific role-medecin" style="display: none;">
                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label">
                        <i class="bi bi-heart-pulse"></i>
                        Spécialité médicale
                      </label>
                      <div class="input-wrapper">
                        <input type="text" 
                               name="specialite" 
                               class="form-control" 
                               value="{{ old('specialite', $user->specialite) }}" 
                               placeholder="Ex: Cardiologie, Pédiatrie...">
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label">
                        <i class="bi bi-card-text"></i>
                        Matricule professionnel
                      </label>
                      <div class="input-wrapper">
                        <input type="text" 
                               name="matricule" 
                               class="form-control" 
                               value="{{ old('matricule', $user->matricule) }}"
                               placeholder="Numéro de matricule">
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label">
                        <i class="bi bi-geo-alt"></i>
                        Cabinet / Service
                      </label>
                      <div class="input-wrapper">
                        <input type="text" 
                               name="cabinet" 
                               class="form-control" 
                               value="{{ old('cabinet', $user->cabinet) }}"
                               placeholder="Nom du cabinet ou service">
                      </div>
                    </div>
                    
                    <div class="form-group full-width">
                      <label class="form-label">
                        <i class="bi bi-clock"></i>
                        Horaires de consultation
                      </label>
                      <div class="input-wrapper">
                        <textarea name="horaires" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Ex: Lundi - Vendredi: 09:00 - 17:00&#10;Samedi: 09:00 - 12:00">{{ old('horaires', $user->horaires) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
                
                {{-- Champs pour le personnel (secrétaire, infirmier, admin) --}}
                <div class="role-specific role-staff" style="display: none;">
                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label">
                        <i class="bi bi-telephone"></i>
                        Téléphone professionnel
                      </label>
                      <div class="input-wrapper">
                        <input type="tel" 
                               name="pro_phone" 
                               class="form-control" 
                               value="{{ old('pro_phone', $user->pro_phone) }}" 
                               placeholder="+221 77 000 00 00">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Onglet: Sécurité --}}
            <div class="tab-content" id="security-tab">
              <div class="form-section">
                <div class="section-header">
                  <i class="bi bi-shield-lock"></i>
                  <h3>Sécurité et Mot de Passe</h3>
                  <p>Modifier les informations de connexion</p>
                </div>
                
                <div class="security-notice">
                  <i class="bi bi-info-circle"></i>
                  <div>
                    <strong>Note importante :</strong>
                    <p>Laissez les champs vides si vous ne souhaitez pas modifier le mot de passe actuel.</p>
                  </div>
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="password" class="form-label">
                      <i class="bi bi-key"></i>
                      Nouveau mot de passe
                    </label>
                    <div class="input-wrapper password-field">
                      <input type="password" 
                             name="password" 
                             id="password" 
                             class="form-control"
                             placeholder="Laisser vide pour conserver l'ancien">
                      <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password')">
                        <i class="bi bi-eye"></i>
                      </button>
                      <div class="password-strength">
                        <div class="strength-bar">
                          <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Force du mot de passe</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                      <i class="bi bi-check2-square"></i>
                      Confirmer le mot de passe
                    </label>
                    <div class="input-wrapper password-field">
                      <input type="password" 
                             name="password_confirmation" 
                             id="password_confirmation" 
                             class="form-control"
                             placeholder="Confirmer le nouveau mot de passe">
                      <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation')">
                        <i class="bi bi-eye"></i>
                      </button>
                      <div class="password-match">
                        <i class="bi bi-x-circle"></i>
                        <span>Les mots de passe doivent correspondre</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="password-requirements">
                  <h6><i class="bi bi-list-check"></i> Exigences du mot de passe :</h6>
                  <ul class="requirements-list">
                    <li class="requirement" data-rule="length">
                      <i class="bi bi-x-circle"></i>
                      <span>Au moins 8 caractères</span>
                    </li>
                    <li class="requirement" data-rule="uppercase">
                      <i class="bi bi-x-circle"></i>
                      <span>Une lettre majuscule</span>
                    </li>
                    <li class="requirement" data-rule="lowercase">
                      <i class="bi bi-x-circle"></i>
                      <span>Une lettre minuscule</span>
                    </li>
                    <li class="requirement" data-rule="number">
                      <i class="bi bi-x-circle"></i>
                      <span>Un chiffre</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            
            {{-- Actions du formulaire --}}
            <div class="form-actions">
              <div class="actions-content">
                <button type="button" 
                        class="btn-secondary" 
                        onclick="resetForm()">
                  <i class="bi bi-arrow-clockwise"></i>
                  <span>Réinitialiser</span>
                </button>
                
                <div class="primary-actions">
                  <button type="button" 
                          class="btn-outline" 
                          onclick="previewChanges()">
                    <i class="bi bi-eye"></i>
                    <span>Aperçu</span>
                  </button>
                  
                  <button type="submit" 
                          class="btn-primary" 
                          id="submitBtn">
                    <div class="btn-content">
                      <i class="bi bi-check-circle"></i>
                      <span>Mettre à jour l'utilisateur</span>
                    </div>
                    <div class="btn-loading" style="display: none;">
                      <div class="spinner"></div>
                      <span>Enregistrement...</span>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
</div>

<style>
  /* Variables CSS modernes */
  :root {
    --primary-color: #3b82f6;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    
    --admin-color: #dc2626;
    --medecin-color: #059669;
    --infirmier-color: #3b82f6;
    --secretaire-color: #8b5cf6;
    
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-tertiary: #f1f5f9;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    
    --transition-fast: 0.15s ease-in-out;
    --transition-normal: 0.3s ease-in-out;
    --transition-slow: 0.5s ease-in-out;
  }
  
  /* Header */
  .edit-user-header {
    background: var(--bg-primary);
    border-radius: var(--radius-2xl);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    position: relative;
  }
  
  .edit-user-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color), var(--warning-color), var(--danger-color));
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    gap: 2rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }
  
  .title-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: var(--shadow-lg);
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  .title-text h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    background: linear-gradient(135deg, var(--primary-color), var(--success-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .user-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--bg-secondary);
    padding: 0.75rem 1rem;
    border-radius: var(--radius-lg);
    border: 2px solid var(--border-color);
    margin-top: 0.5rem;
  }
  
  .user-badge i {
    font-size: 1.25rem;
    color: var(--primary-color);
  }
  
  .user-badge span:first-of-type {
    font-weight: 600;
    color: var(--text-primary);
  }
  
  .role-indicator {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .role-admin { background: rgba(220, 38, 38, 0.1); color: var(--admin-color); }
  .role-medecin { background: rgba(5, 150, 105, 0.1); color: var(--medecin-color); }
  .role-infirmier { background: rgba(59, 130, 246, 0.1); color: var(--infirmier-color); }
  .role-secretaire { background: rgba(139, 92, 246, 0.1); color: var(--secretaire-color); }
  
  .header-actions {
    display: flex;
    gap: 1rem;
  }
  
  .action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition-normal);
    border: 2px solid transparent;
  }
  
  .action-btn.secondary {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    border-color: var(--border-color);
  }
  
  .action-btn.secondary:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }
  
  .action-btn.primary {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
  }
  
  .action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    filter: brightness(1.05);
  }
  
  /* Messages d'alerte */
  .modern-alert {
    border: none;
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: var(--shadow-md);
  }
  
  .alert-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }
  
  .alert-danger .alert-icon {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
  }
  
  .alert-success .alert-icon {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
  }
  
  .alert-content {
    flex: 1;
  }
  
  .alert-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  
  .error-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .error-list li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
  }
  
  .error-list i {
    color: var(--danger-color);
  }
  
  .animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
  }
  
  .slideInDown {
    animation-name: slideInDown;
  }
  
  @keyframes slideInDown {
    from {
      transform: translate3d(0, -100%, 0);
      opacity: 0;
    }
    to {
      transform: translate3d(0, 0, 0);
      opacity: 1;
    }
  }
  
  /* Container du formulaire */
  .form-container {
    background: var(--bg-primary);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  /* Formulaire moderne */
  .form-content {
    padding: 2rem;
  }
  
  .modern-form {
    max-width: none;
  }
  
  /* Navigation par onglets */
  .form-tabs {
    display: flex;
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: 0.5rem;
    margin-bottom: 2rem;
    gap: 0.5rem;
  }
  
  .tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    background: none;
    border: none;
    border-radius: var(--radius-lg);
    color: var(--text-secondary);
    font-weight: 500;
    transition: var(--transition-normal);
    cursor: pointer;
  }
  
  .tab-btn:hover {
    color: var(--text-primary);
    background: var(--bg-primary);
  }
  
  .tab-btn.active {
    background: var(--bg-primary);
    color: var(--primary-color);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }
  
  .tab-btn i {
    font-size: 1.1rem;
  }
  
  /* Contenu des onglets */
  .tab-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
  }
  
  .tab-content.active {
    display: block;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  /* Sections du formulaire */
  .form-section {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
  }
  
  .form-section:hover {
    box-shadow: var(--shadow-md);
  }
  
  .section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
  }
  
  .section-header i {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }
  
  .section-header h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
  }
  
  .section-header p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
  }
  
  /* Grille de formulaire */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .form-group.full-width {
    grid-column: 1 / -1;
  }
  
  /* Labels et inputs */
  .form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  
  .form-label.required::after {
    content: '*';
    color: var(--danger-color);
    margin-left: 0.25rem;
  }
  
  .form-label i {
    color: var(--primary-color);
  }
  
  .input-wrapper {
    position: relative;
  }
  
  .form-control, .form-select {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: var(--transition-normal);
    outline: none;
  }
  
  .form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
  }
  
  .form-control:hover, .form-select:hover {
    border-color: var(--text-secondary);
  }
  
  .form-control::placeholder {
    color: var(--text-muted);
  }
  
  /* Select customisé */
  .select-wrapper {
    position: relative;
  }
  
  .select-arrow {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    pointer-events: none;
    transition: var(--transition-normal);
  }
  
  .form-select:focus + .select-arrow {
    color: var(--primary-color);
    transform: translateY(-50%) rotate(180deg);
  }
  
  /* Toggle moderne */
  .toggle-section {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--border-color);
  }
  
  .toggle-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }
  
  .toggle-info {
    flex: 1;
  }
  
  .form-description {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin: 0.25rem 0 0 0;
  }
  
  .modern-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .modern-toggle input[type="checkbox"] {
    display: none;
  }
  
  .toggle-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-primary);
  }
  
  .toggle-switch {
    width: 50px;
    height: 28px;
    background: var(--border-color);
    border-radius: 14px;
    position: relative;
    transition: var(--transition-normal);
  }
  
  .toggle-switch::before {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    top: 3px;
    left: 3px;
    transition: var(--transition-normal);
    box-shadow: var(--shadow-sm);
  }
  
  .modern-toggle input:checked + .toggle-label .toggle-switch {
    background: var(--success-color);
  }
  
  .modern-toggle input:checked + .toggle-label .toggle-switch::before {
    transform: translateX(22px);
  }
  
  /* Champs spécifiques par rôle */
  .role-specific {
    transition: var(--transition-normal);
  }
  
  .role-specific.hidden {
    display: none;
  }
  
  /* Champs de mot de passe */
  .password-field {
    position: relative;
  }
  
  .toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition-normal);
    z-index: 2;
  }
  
  .toggle-password:hover {
    color: var(--primary-color);
  }
  
  .password-strength {
    margin-top: 0.75rem;
  }
  
  .strength-bar {
    height: 4px;
    background: var(--bg-tertiary);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 0.5rem;
  }
  
  .strength-fill {
    height: 100%;
    background: var(--border-color);
    border-radius: 2px;
    transition: var(--transition-normal);
    width: 0%;
  }
  
  .strength-fill.weak { background: var(--danger-color); width: 25%; }
  .strength-fill.fair { background: var(--warning-color); width: 50%; }
  .strength-fill.good { background: var(--info-color); width: 75%; }
  .strength-fill.strong { background: var(--success-color); width: 100%; }
  
  .strength-text {
    font-size: 0.8rem;
    color: var(--text-muted);
  }
  
  .password-match {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--danger-color);
    opacity: 0;
    transition: var(--transition-normal);
  }
  
  .password-match.show {
    opacity: 1;
  }
  
  .password-match.success {
    color: var(--success-color);
  }
  
  .password-match.success i::before {
    content: "\f26b"; /* bi-check-circle */
  }
  
  /* Notice de sécurité */
  .security-notice {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: rgba(6, 182, 212, 0.05);
    border: 1px solid rgba(6, 182, 212, 0.2);
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
  }
  
  .security-notice i {
    color: var(--info-color);
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
  }
  
  .security-notice strong {
    color: var(--text-primary);
    display: block;
    margin-bottom: 0.5rem;
  }
  
  .security-notice p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
  }
  
  /* Exigences du mot de passe */
  .password-requirements {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-top: 1.5rem;
  }
  
  .password-requirements h6 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-weight: 600;
  }
  
  .requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
  }
  
  .requirement {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    transition: var(--transition-normal);
  }
  
  .requirement i {
    color: var(--text-muted);
    transition: var(--transition-normal);
  }
  
  .requirement.met i {
    color: var(--success-color);
  }
  
  .requirement.met i::before {
    content: "\f26b"; /* bi-check-circle */
  }
  
  .requirement.met span {
    color: var(--success-color);
  }
  
  /* Actions du formulaire */
  .form-actions {
    background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    padding: 2rem;
    margin-top: 2rem;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
  }
  
  .actions-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
  }
  
  .primary-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  /* Boutons d'action */
  .btn-secondary, .btn-outline, .btn-primary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: var(--radius-lg);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-normal);
    cursor: pointer;
    border: 2px solid transparent;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
  }
  
  .btn-secondary {
    background: var(--bg-primary);
    color: var(--text-secondary);
    border-color: var(--border-color);
  }
  
  .btn-secondary:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }
  
  .btn-outline {
    background: transparent;
    color: var(--primary-color);
    border-color: var(--primary-color);
  }
  
  .btn-outline:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  
  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    color: white;
    border-color: var(--primary-color);
    position: relative;
  }
  
  .btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
    filter: brightness(1.05);
  }
  
  .btn-primary:active {
    transform: translateY(-1px);
  }
  
  .btn-content, .btn-loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  
  .spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* Feedback des inputs */
  .input-feedback {
    font-size: 0.8rem;
    margin-top: 0.25rem;
    opacity: 0;
    transition: var(--transition-normal);
  }
  
  .input-feedback.show {
    opacity: 1;
  }
  
  .input-feedback.success {
    color: var(--success-color);
  }
  
  .input-feedback.error {
    color: var(--danger-color);
  }
  
  /* Responsive */
  @media (max-width: 1024px) {
    .form-container .row {
      margin: 0;
    }
    
    .form-container .col-lg-3,
    .form-container .col-lg-9 {
      padding: 0;
    }
    
    .info-sidebar {
      border-right: none;
      border-bottom: 1px solid var(--border-color);
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .header-content {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }
    
    .header-actions {
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .form-tabs {
      flex-direction: column;
      gap: 0.25rem;
    }
    
    .actions-content {
      flex-direction: column;
      align-items: stretch;
    }
    
    .primary-actions {
      justify-content: center;
    }
  }
  
  @media (max-width: 640px) {
    .modern-user-edit {
      padding: 1rem 0;
    }
    
    .header-content,
    .form-content,
    .info-sidebar {
      padding: 1rem;
    }
    
    .form-section {
      padding: 1rem;
    }
    
    .title-text h1 {
      font-size: 1.5rem;
    }
    
    .avatar-image {
      width: 60px;
      height: 60px;
      font-size: 2rem;
    }
    
    .title-icon {
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
    }
  }
</style>

@endsection

@section('scripts')
<script>
  // Variables globales
  let currentTab = 'general';
  let passwordStrength = 0;
  
  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    setupValidation();
    setupPasswordValidation();
    setupRoleFields();
  });
  
  // Initialisation du formulaire
  function initializeForm() {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
      toggleRoleFields(roleSelect.value);
      roleSelect.addEventListener('change', function() {
        toggleRoleFields(this.value);
      });
    }
    
    // Animation d'entrée
    setTimeout(() => {
      document.querySelector('.form-container').style.opacity = '1';
      document.querySelector('.form-container').style.transform = 'translateY(0)';
    }, 100);
  }
  
  // Gestion des onglets
  function switchFormTab(tabName) {
    // Masquer tous les contenus d'onglets
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
    });
    
    // Désactiver tous les boutons d'onglets
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('active');
    });
    
    // Activer l'onglet sélectionné
    document.getElementById(`${tabName}-tab`).classList.add('active');
    event.target.closest('.tab-btn').classList.add('active');
    
    currentTab = tabName;
    
    // Animation fluide
    const activeTab = document.getElementById(`${tabName}-tab`);
    activeTab.style.opacity = '0';
    activeTab.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      activeTab.style.opacity = '1';
      activeTab.style.transform = 'translateY(0)';
    }, 50);
  }
  
  // Gestion des champs spécifiques au rôle
  function toggleRoleFields(role) {
    const medecinFields = document.querySelector('.role-medecin');
    const staffFields = document.querySelector('.role-staff');
    
    if (medecinFields) {
      medecinFields.style.display = role === 'medecin' ? 'block' : 'none';
    }
    
    if (staffFields) {
      const showStaff = ['secretaire', 'infirmier', 'admin'].includes(role);
      staffFields.style.display = showStaff ? 'block' : 'none';
    }
    
    // Animation des champs
    setTimeout(() => {
      if (medecinFields && role === 'medecin') {
        medecinFields.style.opacity = '1';
        medecinFields.style.transform = 'translateY(0)';
      }
      if (staffFields && ['secretaire', 'infirmier', 'admin'].includes(role)) {
        staffFields.style.opacity = '1';
        staffFields.style.transform = 'translateY(0)';
      }
    }, 50);
  }
  
  // Configuration des champs de rôle
  function setupRoleFields() {
    const roleFields = document.querySelectorAll('.role-specific');
    roleFields.forEach(field => {
      field.style.opacity = '0';
      field.style.transform = 'translateY(20px)';
      field.style.transition = 'all 0.3s ease';
    });
  }
  
  // Validation en temps réel
  function setupValidation() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        validateField(this);
      });
      
      input.addEventListener('focus', function() {
        clearFieldValidation(this);
      });
    });
  }
  
  // Validation d'un champ
  function validateField(field) {
    const feedback = field.parentNode.querySelector('.input-feedback');
    let isValid = true;
    let message = '';
    
    // Validation du nom
    if (field.name === 'name') {
      if (!field.value.trim()) {
        isValid = false;
        message = 'Le nom est requis';
      } else if (field.value.trim().length < 2) {
        isValid = false;
        message = 'Le nom doit contenir au moins 2 caractères';
      }
    }
    
    // Validation de l'email
    if (field.name === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!field.value.trim()) {
        isValid = false;
        message = 'L\'adresse email est requise';
      } else if (!emailRegex.test(field.value)) {
        isValid = false;
        message = 'Format d\'email invalide';
      }
    }
    
    // Appliquer le style de validation
    if (feedback) {
      feedback.textContent = message;
      feedback.className = `input-feedback ${isValid ? 'success' : 'error'} show`;
    }
    
    field.style.borderColor = isValid ? 'var(--success-color)' : 'var(--danger-color)';
    
    return isValid;
  }
  
  // Effacer la validation d'un champ
  function clearFieldValidation(field) {
    const feedback = field.parentNode.querySelector('.input-feedback');
    if (feedback) {
      feedback.classList.remove('show');
    }
    field.style.borderColor = '';
  }
  
  // Validation des mots de passe
  function setupPasswordValidation() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (password) {
      password.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
      });
    }
    
    if (passwordConfirm) {
      passwordConfirm.addEventListener('input', checkPasswordMatch);
    }
  }
  
  // Vérification de la force du mot de passe
  function checkPasswordStrength(password) {
    const requirements = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /\d/.test(password)
    };
    
    let strength = 0;
    Object.values(requirements).forEach(met => {
      if (met) strength++;
    });
    
    // Mettre à jour les indicateurs visuels
    Object.keys(requirements).forEach(rule => {
      const element = document.querySelector(`[data-rule="${rule}"]`);
      if (element) {
        element.classList.toggle('met', requirements[rule]);
      }
    });
    
    // Mettre à jour la barre de force
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (strengthFill && strengthText) {
      strengthFill.className = 'strength-fill';
      
      if (password.length === 0) {
        strengthFill.style.width = '0%';
        strengthText.textContent = 'Force du mot de passe';
      } else if (strength === 1) {
        strengthFill.classList.add('weak');
        strengthText.textContent = 'Faible';
      } else if (strength === 2) {
        strengthFill.classList.add('fair');
        strengthText.textContent = 'Correct';
      } else if (strength === 3) {
        strengthFill.classList.add('good');
        strengthText.textContent = 'Bon';
      } else if (strength === 4) {
        strengthFill.classList.add('strong');
        strengthText.textContent = 'Fort';
      }
    }
    
    passwordStrength = strength;
  }
  
  // Vérification de la correspondance des mots de passe
  function checkPasswordMatch() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    const matchIndicator = document.querySelector('.password-match');
    
    if (password && passwordConfirm && matchIndicator) {
      const match = password.value === passwordConfirm.value;
      const bothFilled = password.value !== '' && passwordConfirm.value !== '';
      
      if (bothFilled) {
        matchIndicator.classList.add('show');
        matchIndicator.classList.toggle('success', match);
        matchIndicator.querySelector('span').textContent = 
          match ? 'Les mots de passe correspondent' : 'Les mots de passe ne correspondent pas';
      } else {
        matchIndicator.classList.remove('show');
      }
    }
  }
  
  // Affichage/masquage du mot de passe
  function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentNode.querySelector('.toggle-password');
    
    if (field.type === 'password') {
      field.type = 'text';
      button.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
      field.type = 'password';
      button.innerHTML = '<i class="bi bi-eye"></i>';
    }
  }
  
  
  // Aperçu des modifications
  function previewChanges() {
    const formData = new FormData(document.getElementById('userEditForm'));
    const changes = [];
    
    for (let [key, value] of formData.entries()) {
      if (value !== '' && key !== '_token' && key !== '_method') {
        changes.push(`${key}: ${value}`);
      }
    }
    
    const preview = changes.join('\n');
    alert(`Aperçu des modifications :\n\n${preview}`);
  }
  
  // Réinitialisation du formulaire
  function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les champs ?')) {
      document.getElementById('userEditForm').reset();
      
      // Réinitialiser les validations
      document.querySelectorAll('.input-feedback').forEach(feedback => {
        feedback.classList.remove('show');
      });
      
      document.querySelectorAll('.form-control, .form-select').forEach(field => {
        field.style.borderColor = '';
      });
      
      // Réinitialiser la force du mot de passe
      const strengthFill = document.querySelector('.strength-fill');
      const strengthText = document.querySelector('.strength-text');
      if (strengthFill && strengthText) {
        strengthFill.className = 'strength-fill';
        strengthFill.style.width = '0%';
        strengthText.textContent = 'Force du mot de passe';
      }
      
      showNotification('info', 'Formulaire réinitialisé.');
    }
  }
  
  // Validation du formulaire avant soumission
  document.getElementById('userEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const btnContent = submitBtn.querySelector('.btn-content');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Validation des champs obligatoires
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    let allValid = true;
    
    requiredFields.forEach(field => {
      if (!validateField(field)) {
        allValid = false;
      }
    });
    
    // Validation des mots de passe
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (password.value !== '' || passwordConfirm.value !== '') {
      if (password.value !== passwordConfirm.value) {
        allValid = false;
        showNotification('error', 'Les mots de passe ne correspondent pas.');
      } else if (password.value !== '' && passwordStrength < 3) {
        allValid = false;
        showNotification('error', 'Le mot de passe est trop faible.');
      }
    }
    
    if (!allValid) {
      showNotification('error', 'Veuillez corriger les erreurs avant de continuer.');
      return;
    }
    
    // Animation de chargement
    btnContent.style.display = 'none';
    btnLoading.style.display = 'flex';
    submitBtn.disabled = true;
    
    // Simulation d'envoi (remplacer par la soumission réelle)
    setTimeout(() => {
      this.submit();
    }, 1000);
  });
  
  // Fonction de notification
  function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show modern-alert animated slideInDown`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 350px;
      max-width: 500px;
    `;
    
    const iconMap = {
      success: 'check-circle-fill',
      error: 'exclamation-triangle-fill',
      warning: 'exclamation-triangle-fill',
      info: 'info-circle-fill'
    };
    
    notification.innerHTML = `
      <div class="alert-icon">
        <i class="bi bi-${iconMap[type]}"></i>
      </div>
      <div class="alert-content">
        <p class="mb-0">${message}</p>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
      if (notification.parentNode) {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
      }
    }, 5000);
  }
  
  // Styles d'animation pour les notifications
  const notificationStyles = document.createElement('style');
  notificationStyles.textContent = `
    @keyframes slideOutRight {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(100px);
      }
    }
  `;
  document.head.appendChild(notificationStyles);
  
  // Animation initiale du container
  document.querySelector('.form-container').style.cssText += `
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease;
  `;
</script>
@endsection

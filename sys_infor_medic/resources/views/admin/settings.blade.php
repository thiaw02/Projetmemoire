@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-4 mb-4">
    @include('layouts.partials.profile_sidebar')
  </div>
  <div class="col-lg-8">
<div class="settings-modern-container">
  {{-- Header principal modernisé --}}
  <div class="settings-hero">
    <div class="hero-content">
      <div class="hero-main">
        <div class="hero-icon">
          <i class="bi bi-shield-fill-exclamation"></i>
        </div>
        <div class="hero-text">
          <h1>Paramètres Administrateur</h1>
          <p>Gérez vos informations professionnelles et les paramètres système</p>
          <div class="hero-stats">
            <div class="stat-item">
              <i class="bi bi-shield-check"></i>
              <span>Accès complet</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-gear-fill"></i>
              <span>Contrôle système</span>
            </div>
          </div>
        </div>
      </div>
      <div class="hero-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-hero-back">
          <i class="bi bi-arrow-left"></i>
          <span>Retour</span>
        </a>
      </div>
    </div>
    <div class="hero-decoration">
      <div class="decoration-circle circle-1"></div>
      <div class="decoration-circle circle-2"></div>
      <div class="decoration-circle circle-3"></div>
    </div>
  </div>

  {{-- Navigation tabs modernisée --}}
  <div class="settings-nav">
    <div class="nav-container">
      <button class="nav-tab active" data-tab="personal">
        <i class="bi bi-person-fill"></i>
        <span>Personnel</span>
      </button>
      <button class="nav-tab" data-tab="professional">
        <i class="bi bi-building-gear"></i>
        <span>Professionnel</span>
      </button>
      <button class="nav-tab" data-tab="security">
        <i class="bi bi-shield-lock"></i>
        <span>Sécurité</span>
      </button>
      <button class="nav-tab" data-tab="system">
        <i class="bi bi-gear"></i>
        <span>Système</span>
      </button>
      <button class="nav-tab" data-tab="avatar">
        <i class="bi bi-camera"></i>
        <span>Photo</span>
      </button>
    </div>
  </div>

  {{-- Messages modernisés --}}
  <div class="messages-container">
    @if(session('success'))
      <div class="message-modern success">
        <div class="message-icon">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="message-content">
          <strong>Succès !</strong>
          <p>{{ session('success') }}</p>
        </div>
        <button class="message-close" onclick="this.parentElement.remove()">
          <i class="bi bi-x"></i>
        </button>
      </div>
    @endif
    @if($errors->any())
      <div class="message-modern error">
        <div class="message-icon">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="message-content">
          <strong>Erreurs détectées</strong>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        <button class="message-close" onclick="this.parentElement.remove()">
          <i class="bi bi-x"></i>
        </button>
      </div>
    @endif
  </div>

  {{-- Contenu principal avec tabs --}}
  <div class="settings-content">
    
    {{-- Tab Informations Personnelles --}}
    <div class="tab-content active" id="personal-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon">
            <i class="bi bi-person-fill"></i>
          </div>
          <div class="header-content">
            <h3>Informations Personnelles</h3>
            <p>Mettez à jour vos informations de base</p>
          </div>
        </div>
        <div class="card-body-modern">
          <form method="POST" action="{{ route('profile.update') }}" class="modern-form">
            @csrf
            @method('PATCH')
            
            <div class="form-grid">
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-person"></i>
                  <span>Nom complet</span>
                </label>
                <input type="text" name="name" class="input-modern" 
                       value="{{ old('name', auth()->user()->name) }}" 
                       placeholder="Votre nom complet" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-envelope"></i>
                  <span>Adresse email</span>
                </label>
                <input type="email" name="email" class="input-modern" 
                       value="{{ old('email', auth()->user()->email) }}" 
                       placeholder="votre@email.com" required>
              </div>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern btn-personal">
                <i class="bi bi-check2-circle"></i>
                <span>Sauvegarder les informations</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Tab Professionnel --}}
    <div class="tab-content" id="professional-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon professional">
            <i class="bi bi-building-gear"></i>
          </div>
          <div class="header-content">
            <h3>Informations Professionnelles</h3>
            <p>Détails de votre poste administratif</p>
          </div>
        </div>
        <div class="card-body-modern">
          <form method="POST" action="{{ route('profile.update') }}" class="modern-form">
            @csrf
            @method('PATCH')
            
            <div class="form-grid professional-grid">
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-telephone"></i>
                  <span>Téléphone professionnel</span>
                </label>
                <input type="tel" name="pro_phone" class="input-modern" 
                       value="{{ old('pro_phone', auth()->user()->pro_phone) }}" 
                       placeholder="+221 XX XXX XX XX">
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-card-text"></i>
                  <span>Matricule</span>
                </label>
                <input type="text" name="matricule" class="input-modern" 
                       value="{{ old('matricule', auth()->user()->matricule) }}" 
                       placeholder="Matricule administratif">
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-building"></i>
                  <span>Département</span>
                </label>
                <input type="text" name="specialite" class="input-modern" 
                       value="{{ old('specialite', auth()->user()->specialite) }}" 
                       placeholder="IT/Système, Administration, etc.">
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-geo-alt"></i>
                  <span>Bureau</span>
                </label>
                <input type="text" name="cabinet" class="input-modern" 
                       value="{{ old('cabinet', auth()->user()->cabinet) }}" 
                       placeholder="Administration, Bureau 101, etc.">
              </div>
              
              <div class="input-group-modern full-width">
                <label class="label-modern">
                  <i class="bi bi-clock"></i>
                  <span>Disponibilité</span>
                </label>
                <textarea name="horaires" class="input-modern" rows="2" 
                          placeholder="Ex: 24/7 Support, Lun-Ven 8h-18h">{{ old('horaires', auth()->user()->horaires) }}</textarea>
              </div>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern btn-admin">
                <i class="bi bi-shield-fill-check"></i>
                <span>Sauvegarder le profil admin</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Tab Sécurité --}}
    <div class="tab-content" id="security-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon security">
            <i class="bi bi-shield-lock"></i>
          </div>
          <div class="header-content">
            <h3>Sécurité du Compte</h3>
            <p>Changez votre mot de passe et sécurisez votre compte</p>
          </div>
        </div>
        <div class="card-body-modern">
          <form method="POST" action="{{ route('profile.password.update') }}" class="modern-form">
            @csrf
            @method('PUT')
            
            <div class="form-grid security-grid">
              <div class="input-group-modern full-width">
                <label class="label-modern">
                  <i class="bi bi-key"></i>
                  <span>Mot de passe actuel</span>
                </label>
                <input type="password" name="current_password" class="input-modern" 
                       placeholder="Votre mot de passe actuel" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-shield-plus"></i>
                  <span>Nouveau mot de passe</span>
                </label>
                <input type="password" name="password" class="input-modern" 
                       placeholder="Minimum 8 caractères" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-shield-check"></i>
                  <span>Confirmer le mot de passe</span>
                </label>
                <input type="password" name="password_confirmation" class="input-modern" 
                       placeholder="Confirmez le nouveau mot de passe" required>
              </div>
            </div>
            
            <div class="security-tips">
              <h6><i class="bi bi-info-circle me-2"></i>Conseils de sécurité administrateur</h6>
              <ul>
                <li>Utilisez un mot de passe très robuste (12+ caractères)</li>
                <li>Mélangez lettres majuscules/minuscules, chiffres et symboles</li>
                <li>Évitez les informations personnelles</li>
                <li>Changez régulièrement votre mot de passe</li>
                <li>Ne partagez jamais vos identifiants d'admin</li>
              </ul>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern btn-security">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Changer le mot de passe</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Tab Système --}}
    <div class="tab-content" id="system-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon system">
            <i class="bi bi-gear"></i>
          </div>
          <div class="header-content">
            <h3>Paramètres Système</h3>
            <p>Configuration avancée et préférences d'administration</p>
          </div>
        </div>
        <div class="card-body-modern">
          <div class="preferences-section">
            <h5 class="section-title">
              <i class="bi bi-toggles"></i>
              Options d'administration
            </h5>
            <div class="toggle-options-modern">
              <div class="toggle-item">
                <div class="toggle-info">
                  <strong>Mode maintenance</strong>
                  <small>Activer le mode maintenance pour le système</small>
                </div>
                <div class="toggle-switch-modern">
                  <input type="checkbox" name="maintenance_mode" id="maintenance">
                  <label for="maintenance" class="toggle-slider-modern"></label>
                </div>
              </div>
              
              <div class="toggle-item">
                <div class="toggle-info">
                  <strong>Logs détaillés</strong>
                  <small>Activer l'enregistrement détaillé des activités</small>
                </div>
                <div class="toggle-switch-modern">
                  <input type="checkbox" name="detailed_logs" id="logs" checked>
                  <label for="logs" class="toggle-slider-modern"></label>
                </div>
              </div>
              
              <div class="toggle-item">
                <div class="toggle-info">
                  <strong>Notifications système</strong>
                  <small>Recevoir les notifications critiques du système</small>
                </div>
                <div class="toggle-switch-modern">
                  <input type="checkbox" name="system_notifications" id="sysnotif" checked>
                  <label for="sysnotif" class="toggle-slider-modern"></label>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-actions-modern">
            <button type="button" class="btn-modern btn-system">
              <i class="bi bi-gear-fill"></i>
              <span>Sauvegarder les paramètres</span>
            </button>
            
            <a href="{{ route('admin.performance.index') }}" class="btn-modern btn-monitoring">
              <i class="bi bi-graph-up"></i>
              <span>Monitoring système</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Tab Photo de Profil --}}
    <div class="tab-content" id="avatar-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon avatar">
            <i class="bi bi-camera"></i>
          </div>
          <div class="header-content">
            <h3>Photo de Profil</h3>
            <p>Gérez votre photo de profil administrateur</p>
          </div>
        </div>
        <div class="card-body-modern">
          @php
            $user = auth()->user();
            $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
            $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
                      ? $rawAvatar
                      : asset(ltrim($rawAvatar, '/'));
          @endphp
          
          <div class="avatar-section">
            {{-- Affichage actuel --}}
            <div class="current-avatar">
              <h5 class="section-title">
                <i class="bi bi-person-circle"></i>
                Photo actuelle
              </h5>
              <div class="avatar-display-modern">
                <div class="avatar-container-modern">
                  <img src="{{ $avatar }}" alt="Photo actuelle" class="avatar-image" id="avatarPreview">
                  <div class="avatar-overlay-modern">
                    <i class="bi bi-camera-fill"></i>
                    <span>Changer</span>
                  </div>
                </div>
                <div class="avatar-details">
                  <h6>{{ $user->name }}</h6>
                  <p>{{ $user->avatar_url ? 'Image personnalisée' : 'Avatar généré automatiquement' }}</p>
                  @if($user->avatar_url)
                    <form method="POST" action="{{ route('profile.avatar.delete') }}" class="mt-2">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-modern danger-outline" 
                              onclick="return confirm('Voulez-vous supprimer votre photo de profil ?')">
                        <i class="bi bi-trash"></i>
                        <span>Supprimer la photo</span>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
            
            {{-- Zone d'upload --}}
            <div class="upload-section">
              <h5 class="section-title">
                <i class="bi bi-cloud-upload"></i>
                Nouvelle photo
              </h5>
              
              <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="avatar-upload-modern">
                @csrf
                
                <div class="upload-area-modern" id="uploadArea">
                  <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" 
                         id="avatarInput" class="upload-input-hidden" required>
                  
                  <div class="upload-content-modern">
                    <div class="upload-icon-modern">
                      <i class="bi bi-cloud-upload"></i>
                    </div>
                    <h6>Sélectionnez une image</h6>
                    <p>Glissez-déposez ou cliquez pour choisir</p>
                    <div class="upload-formats">
                      <span class="format-badge">JPG</span>
                      <span class="format-badge">PNG</span>
                      <span class="format-badge">WebP</span>
                      <span class="format-info">Max 2MB</span>
                    </div>
                  </div>
                </div>
                
                <div class="upload-preview-modern" id="uploadPreview" style="display: none;">
                  <img id="previewImage" alt="Prévisualisation">
                  <div class="preview-actions-modern">
                    <button type="button" class="btn-modern secondary" onclick="cancelUpload()">
                      <i class="bi bi-x"></i>
                      <span>Annuler</span>
                    </button>
                    <button type="submit" class="btn-modern btn-avatar">
                      <i class="bi bi-check-circle-fill"></i>
                      <span>Confirmer la photo</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div> {{-- Fin settings-content --}}
  
</div> {{-- Fin settings-modern-container --}}
  </div> {{-- Fin col-lg-9 --}}
</div> {{-- Fin row --}}

<style>
  /* Variables CSS pour admin */
  :root {
    --primary-color: #1e40af;
    --primary-light: #1e40af20;
    --primary-hover: #1e40af15;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    
    /* Couleurs de base */
    --bg-primary: #fafbfc;
    --bg-secondary: #ffffff;
    --bg-tertiary: #f8fafc;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --border-color: #e2e8f0;
    --border-light: #f1f5f9;
    
    /* Ombres */
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    
    /* Transitions */
    --transition-fast: 0.15s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;
  }
  
  /* ============= BOUTONS SPÉCIALISÉS ADMIN ============= */
  
  .btn-modern.btn-admin {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: white;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
    border: 2px solid transparent;
  }
  
  .btn-modern.btn-admin:hover {
    background: linear-gradient(135deg, #6d28d9, #5b21b6);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
    color: white;
  }
  
  .btn-modern.btn-system {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
    border: 2px solid transparent;
  }
  
  .btn-modern.btn-system:hover {
    background: linear-gradient(135deg, #047857, #065f46);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
    color: white;
  }
  
  .btn-modern.btn-monitoring {
    background: linear-gradient(135deg, #ea580c, #dc2626);
    color: white;
    box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
    border: 2px solid transparent;
  }
  
  .btn-modern.btn-monitoring:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(234, 88, 12, 0.4);
    color: white;
  }
  
  /* Include all styles from patient settings */
  @import url('{{ asset('css/patient-settings-styles.css') }}');
</style>

<script>
// Include all scripts from patient settings
document.addEventListener('DOMContentLoaded', function() {
  // Tab navigation
  function initTabNavigation() {
    const tabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const targetTab = tab.dataset.tab;
        
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        tab.classList.add('active');
        const targetContent = document.getElementById(targetTab + '-tab');
        if (targetContent) {
          targetContent.classList.add('active');
          
          targetContent.style.opacity = '0';
          targetContent.style.transform = 'translateY(20px)';
          
          requestAnimationFrame(() => {
            targetContent.style.transition = 'all 0.3s ease';
            targetContent.style.opacity = '1';
            targetContent.style.transform = 'translateY(0)';
          });
        }
        
        localStorage.setItem('activeAdminSettingsTab', targetTab);
      });
    });
    
    const lastActiveTab = localStorage.getItem('activeAdminSettingsTab');
    if (lastActiveTab) {
      const tabToActivate = document.querySelector(`[data-tab="${lastActiveTab}"]`);
      if (tabToActivate) {
        tabToActivate.click();
      }
    }
  }
  
  initTabNavigation();
  
  // Avatar upload functionality
  const uploadArea = document.getElementById('uploadArea');
  const avatarInput = document.getElementById('avatarInput');
  const uploadPreview = document.getElementById('uploadPreview');
  const previewImage = document.getElementById('previewImage');
  
  if (uploadArea && avatarInput) {
    uploadArea.addEventListener('click', () => avatarInput.click());
    
    avatarInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
      }
    });
  }
  
  function handleFileSelect(file) {
    if (!file.type.startsWith('image/')) {
      alert('Veuillez sélectionner un fichier image.');
      return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
      alert('La taille du fichier ne doit pas dépasser 2MB.');
      return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImage.src = e.target.result;
      uploadArea.style.display = 'none';
      uploadPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

function cancelUpload() {
  const uploadArea = document.getElementById('uploadArea');
  const uploadPreview = document.getElementById('uploadPreview');
  const avatarInput = document.getElementById('avatarInput');
  
  avatarInput.value = '';
  uploadArea.style.display = 'block';
  uploadPreview.style.display = 'none';
}
</script>
@endsection
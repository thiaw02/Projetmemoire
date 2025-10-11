@extends('layouts.app')

@section('content')
<div class="settings-modern-container">
  {{-- Header principal modernisé --}}
  <div class="settings-hero">
    <div class="hero-content">
      <div class="hero-main">
        <div class="hero-icon">
          <i class="bi bi-gear-fill"></i>
        </div>
        <div class="hero-text">
          <h1>Espace Personnel</h1>
          <p>Personnalisez votre expérience et gérez vos informations</p>
          <div class="hero-stats">
            <div class="stat-item">
              <i class="bi bi-person-check"></i>
              <span>Profil actif</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-shield-check"></i>
              <span>Sécurisé</span>
            </div>
          </div>
        </div>
      </div>
      <div class="hero-actions">
        <a href="{{ route('patient.dashboard') }}" class="btn-hero-back">
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
      <button class="nav-tab" data-tab="medical">
        <i class="bi bi-heart-pulse"></i>
        <span>Médical</span>
      </button>
      <button class="nav-tab" data-tab="security">
        <i class="bi bi-shield-lock"></i>
        <span>Sécurité</span>
      </button>
      <button class="nav-tab" data-tab="preferences">
        <i class="bi bi-palette"></i>
        <span>Interface</span>
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
              <button type="submit" class="btn-modern primary">
                <i class="bi bi-check2"></i>
                <span>Sauvegarder</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Tab Profil Médical --}}
    <div class="tab-content" id="medical-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon medical">
            <i class="bi bi-heart-pulse"></i>
          </div>
          <div class="header-content">
            <h3>Dossier Médical</h3>
            <p>Informations médicales et antécédents</p>
          </div>
        </div>
        <div class="card-body-modern">
          @php $patient = auth()->user()->patient; @endphp
          <form method="POST" action="{{ route('profile.patient.update') }}" class="modern-form">
            @csrf
            @method('PUT')
            
            <div class="form-grid medical-grid">
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-person-badge"></i>
                  <span>Nom de famille</span>
                </label>
                <input type="text" name="nom" class="input-modern" 
                       value="{{ old('nom', $patient->nom ?? '') }}" 
                       placeholder="Votre nom de famille" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-person-badge"></i>
                  <span>Prénom</span>
                </label>
                <input type="text" name="prenom" class="input-modern" 
                       value="{{ old('prenom', $patient->prenom ?? '') }}" 
                       placeholder="Votre prénom" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-telephone"></i>
                  <span>Téléphone</span>
                </label>
                <input type="tel" name="telephone" class="input-modern" 
                       value="{{ old('telephone', $patient->telephone ?? '') }}"
                       placeholder="+221 XX XXX XX XX">
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-gender-ambiguous"></i>
                  <span>Sexe</span>
                </label>
                @php $sexe = old('sexe', $patient->sexe ?? ''); @endphp
                <select name="sexe" class="input-modern" required>
                  <option value="">-- Choisir --</option>
                  <option value="Masculin" {{ $sexe == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                  <option value="Féminin" {{ $sexe == 'Féminin' ? 'selected' : '' }}>Féminin</option>
                </select>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-calendar-event"></i>
                  <span>Date de naissance</span>
                </label>
                <input type="date" name="date_naissance" class="input-modern" 
                       value="{{ old('date_naissance', $patient && $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('Y-m-d') : '') }}" required>
              </div>
              
              <div class="input-group-modern">
                <label class="label-modern">
                  <i class="bi bi-droplet"></i>
                  <span>Groupe sanguin</span>
                </label>
                <select name="groupe_sanguin" class="input-modern">
                  <option value="">-- Choisir --</option>
                  @php $groupe = old('groupe_sanguin', $patient->groupe_sanguin ?? ''); @endphp
                  @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                    <option value="{{ $group }}" {{ $groupe == $group ? 'selected' : '' }}>{{ $group }}</option>
                  @endforeach
                </select>
              </div>
              
              <div class="input-group-modern full-width">
                <label class="label-modern">
                  <i class="bi bi-geo-alt"></i>
                  <span>Adresse complète</span>
                </label>
                <textarea name="adresse" class="input-modern" rows="2" 
                          placeholder="Votre adresse complète">{{ old('adresse', $patient->adresse ?? '') }}</textarea>
              </div>
              
              <div class="input-group-modern full-width">
                <label class="label-modern">
                  <i class="bi bi-clipboard-pulse"></i>
                  <span>Antécédents médicaux</span>
                </label>
                <textarea name="antecedents" class="input-modern" rows="3" 
                          placeholder="Décrivez vos antécédents médicaux importants...">{{ old('antecedents', $patient->antecedents ?? '') }}</textarea>
              </div>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern primary">
                <i class="bi bi-heart"></i>
                <span>Sauvegarder le profil médical</span>
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
              <h6><i class="bi bi-info-circle me-2"></i>Conseils de sécurité</h6>
              <ul>
                <li>Utilisez au moins 8 caractères</li>
                <li>Mélangez lettres, chiffres et symboles</li>
                <li>Evitez les mots du dictionnaire</li>
                <li>Ne partagez jamais votre mot de passe</li>
              </ul>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern warning">
                <i class="bi bi-shield-fill-check"></i>
                <span>Changer le mot de passe</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Tab Interface/Préférences --}}
    <div class="tab-content" id="preferences-tab">
      <div class="modern-card">
        <div class="card-header-modern">
          <div class="header-icon preferences">
            <i class="bi bi-palette"></i>
          </div>
          <div class="header-content">
            <h3>Personnalisation Interface</h3>
            <p>Customisez l'apparence selon vos préférences</p>
          </div>
        </div>
        <div class="card-body-modern">
          <form method="POST" action="{{ route('patient.settings.update') }}" class="modern-form">
            @csrf
            
            {{-- Couleurs du thème --}}
            <div class="preferences-section">
              <h5 class="section-title">
                <i class="bi bi-palette2"></i>
                Couleur du thème
              </h5>
              <div class="color-grid-modern">
                @php
                  $colors = [
                    'blue' => ['name' => 'Bleu', 'color' => '#3b82f6', 'desc' => 'Calme et professionnel'],
                    'purple' => ['name' => 'Violet', 'color' => '#8b5cf6', 'desc' => 'Créatif et moderne'],
                    'green' => ['name' => 'Vert', 'color' => '#10b981', 'desc' => 'Nature et santé'],
                    'orange' => ['name' => 'Orange', 'color' => '#f59e0b', 'desc' => 'Dynamique et chaleureux'],
                    'red' => ['name' => 'Rouge', 'color' => '#ef4444', 'desc' => 'Puissant et énergique'],
                    'pink' => ['name' => 'Rose', 'color' => '#ec4899', 'desc' => 'Doux et élégant']
                  ];
                @endphp
                
                @foreach($colors as $key => $color)
                  <div class="color-choice-modern">
                    <input type="radio" name="theme_color" value="{{ $key }}" id="color-{{ $key }}" 
                           {{ $preferences['theme_color'] == $key ? 'checked' : '' }}>
                    <label for="color-{{ $key }}" class="color-option" 
                           style="--color: {{ $color['color'] }};">
                      <div class="color-preview" style="background: {{ $color['color'] }};"></div>
                      <div class="color-info">
                        <strong>{{ $color['name'] }}</strong>
                        <small>{{ $color['desc'] }}</small>
                      </div>
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
            
            {{-- Préférences d'affichage --}}
            <div class="preferences-section">
              <h5 class="section-title">
                <i class="bi bi-sliders"></i>
                Préférences d'affichage
              </h5>
              <div class="form-grid preferences-grid">
                <div class="input-group-modern">
                  <label class="label-modern">
                    <i class="bi bi-card-heading"></i>
                    <span>Style des cartes</span>
                  </label>
                  <select name="card_style" class="input-modern">
                    <option value="modern" {{ $preferences['card_style'] == 'modern' ? 'selected' : '' }}>🎨 Moderne</option>
                    <option value="classic" {{ $preferences['card_style'] == 'classic' ? 'selected' : '' }}>📋 Classique</option>
                    <option value="minimal" {{ $preferences['card_style'] == 'minimal' ? 'selected' : '' }}>⚡ Minimaliste</option>
                  </select>
                </div>
                
                <div class="input-group-modern">
                  <label class="label-modern">
                    <i class="bi bi-speedometer2"></i>
                    <span>Vitesse d'animation</span>
                  </label>
                  <select name="animation_speed" class="input-modern">
                    <option value="slow" {{ $preferences['animation_speed'] == 'slow' ? 'selected' : '' }}>🐌 Lente</option>
                    <option value="normal" {{ $preferences['animation_speed'] == 'normal' ? 'selected' : '' }}>⚡ Normale</option>
                    <option value="fast" {{ $preferences['animation_speed'] == 'fast' ? 'selected' : '' }}>🚀 Rapide</option>
                  </select>
                </div>
                
                <div class="input-group-modern full-width">
                  <label class="label-modern">
                    <i class="bi bi-house-door"></i>
                    <span>Onglet par défaut</span>
                  </label>
                  <select name="default_tab" class="input-modern">
                    <option value="rdv" {{ $preferences['default_tab'] == 'rdv' ? 'selected' : '' }}>📅 Nouveau RDV</option>
                    <option value="mesrdv" {{ $preferences['default_tab'] == 'mesrdv' ? 'selected' : '' }}>📋 Mes RDV</option>
                    <option value="dossier" {{ $preferences['default_tab'] == 'dossier' ? 'selected' : '' }}>📁 Dossier médical</option>
                    <option value="historique" {{ $preferences['default_tab'] == 'historique' ? 'selected' : '' }}>🕒 Historique</option>
                  </select>
                </div>
              </div>
            </div>
            
            {{-- Options avancées --}}
            <div class="preferences-section">
              <h5 class="section-title">
                <i class="bi bi-toggles"></i>
                Options avancées
              </h5>
              <div class="toggle-options-modern">
                <div class="toggle-item">
                  <div class="toggle-info">
                    <strong>Mode compact</strong>
                    <small>Interface plus condensée pour plus de contenu</small>
                  </div>
                  <div class="toggle-switch-modern">
                    <input type="checkbox" name="compact_mode" id="compact" {{ $preferences['compact_mode'] ? 'checked' : '' }}>
                    <label for="compact" class="toggle-slider-modern"></label>
                  </div>
                </div>
                
                <div class="toggle-item">
                  <div class="toggle-info">
                    <strong>Afficher les statistiques</strong>
                    <small>Cartes statistiques sur le tableau de bord</small>
                  </div>
                  <div class="toggle-switch-modern">
                    <input type="checkbox" name="show_statistics" id="stats" {{ $preferences['show_statistics'] ? 'checked' : '' }}>
                    <label for="stats" class="toggle-slider-modern"></label>
                  </div>
                </div>
                
                <div class="toggle-item">
                  <div class="toggle-info">
                    <strong>Score de santé</strong>
                    <small>Afficher le score de santé dans les statistiques</small>
                  </div>
                  <div class="toggle-switch-modern">
                    <input type="checkbox" name="show_health_score" id="health" {{ $preferences['show_health_score'] ? 'checked' : '' }}>
                    <label for="health" class="toggle-slider-modern"></label>
                  </div>
                </div>
                
                <div class="toggle-item">
                  <div class="toggle-info">
                    <strong>Notifications push</strong>
                    <small>Recevoir des notifications pour les RDV</small>
                  </div>
                  <div class="toggle-switch-modern">
                    <input type="checkbox" name="push_notifications" id="notifications" {{ $preferences['push_notifications'] ?? false ? 'checked' : '' }}>
                    <label for="notifications" class="toggle-slider-modern"></label>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-actions-modern">
              <button type="submit" class="btn-modern primary">
                <i class="bi bi-palette"></i>
                <span>Sauvegarder les préférences</span>
              </button>
              
              <a href="{{ route('patient.settings.reset') }}" class="btn-modern secondary" 
                 onclick="return confirm('Voulez-vous vraiment réinitialiser toutes vos préférences ?')">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Réinitialiser</span>
              </a>
            </div>
          </form>
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
            <p>Gérez votre photo de profil et votre avatar</p>
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
                    <button type="submit" class="btn-modern primary">
                      <i class="bi bi-check"></i>
                      <span>Confirmer</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
            
            {{-- Conseils --}}
            <div class="avatar-tips">
              <h6><i class="bi bi-info-circle me-2"></i>Conseils pour une belle photo</h6>
              <div class="tips-grid">
                <div class="tip-item">
                  <i class="bi bi-aspect-ratio"></i>
                  <span>Format carré recommandé</span>
                </div>
                <div class="tip-item">
                  <i class="bi bi-image"></i>
                  <span>200x200 pixels minimum</span>
                </div>
                <div class="tip-item">
                  <i class="bi bi-file-earmark"></i>
                  <span>Taille max : 2MB</span>
                </div>
                <div class="tip-item">
                  <i class="bi bi-brightness-high"></i>
                  <span>Image claire et nette</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div> {{-- Fin settings-content --}}
  
</div> {{-- Fin settings-modern-container --}}

<style>
  @php
    $currentColor = $preferences['theme_color'] ?? 'blue';
    $colorMap = [
      'blue' => '#3b82f6',
      'purple' => '#8b5cf6', 
      'green' => '#10b981',
      'orange' => '#f59e0b',
      'red' => '#ef4444',
      'pink' => '#ec4899'
    ];
    $primaryColor = $colorMap[$currentColor];
  @endphp
  
  /* Variables CSS modernes */
  :root {
    --primary-color: {{ $primaryColor }};
    --primary-light: {{ $primaryColor }}20;
    --primary-hover: {{ $primaryColor }}15;
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
  
  /* Reset et base */
  * {
    box-sizing: border-box;
  }
  
  body {
    background: var(--bg-primary) !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: var(--text-primary);
  }
  
  /* ============= LAYOUT PRINCIPAL ============= */
  
  .settings-modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
    min-height: 100vh;
  }
  
  /* ============= HERO SECTION ============= */
  
  .settings-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color)dd 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    margin: 2rem 0;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
  }
  
  .hero-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
  }
  
  .hero-main {
    display: flex;
    align-items: center;
    gap: 2rem;
  }
  
  .hero-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: float 3s ease-in-out infinite;
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
  
  .hero-text {
    color: white;
  }
  
  .hero-text h1 {
    font-size: 3rem;
    font-weight: 900;
    margin: 0 0 1rem 0;
    background: linear-gradient(135deg, #ffffff, #f0f9ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
  }
  
  .hero-text p {
    font-size: 1.25rem;
    opacity: 0.9;
    margin: 0 0 1.5rem 0;
  }
  
  .hero-stats {
    display: flex;
    gap: 2rem;
  }
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .stat-item i {
    font-size: 1.2rem;
  }
  
  .btn-hero-back {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 1rem 2rem;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 600;
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
  }
  
  .btn-hero-back:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  }
  
  .hero-decoration {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
  }
  
  .decoration-circle {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
  }
  
  .circle-1 {
    width: 200px;
    height: 200px;
    top: -50px;
    right: -50px;
    animation-delay: 0s;
  }
  
  .circle-2 {
    width: 150px;
    height: 150px;
    top: 50%;
    right: -75px;
    animation-delay: 1s;
  }
  
  .circle-3 {
    width: 100px;
    height: 100px;
    bottom: -25px;
    right: 20%;
    animation-delay: 2s;
  }
  
  @keyframes pulse {
    0%, 100% { 
      transform: scale(1);
      opacity: 0.1;
    }
    50% { 
      transform: scale(1.1);
      opacity: 0.2;
    }
  }
  }
  
  /* ============= NAVIGATION MODERNE ============= */
  
  .settings-nav {
    margin: 2rem 0;
    position: sticky;
    top: 20px;
    z-index: 100;
  }
  
  .nav-container {
    background: var(--bg-secondary);
    border-radius: 20px;
    padding: 0.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-light);
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
  }
  
  .nav-container::-webkit-scrollbar {
    display: none;
  }
  
  .nav-tab {
    flex: 1;
    min-width: 120px;
    background: transparent;
    border: none;
    padding: 1rem 1.5rem;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all var(--transition-normal);
    color: var(--text-secondary);
    font-weight: 500;
    position: relative;
  }
  
  .nav-tab i {
    font-size: 1.5rem;
    transition: all var(--transition-normal);
  }
  
  .nav-tab span {
    font-size: 0.875rem;
    transition: all var(--transition-normal);
  }
  
  .nav-tab:hover {
    background: var(--primary-hover);
    color: var(--primary-color);
    transform: translateY(-2px);
  }
  
  .nav-tab.active {
    background: var(--primary-color);
    color: white;
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
  }
  
  .nav-tab.active::before {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 6px;
    background: var(--primary-color);
    border-radius: 50%;
    box-shadow: 0 0 10px var(--primary-color);
  }
  
  /* ============= MESSAGES MODERNES ============= */
  
  .messages-container {
    margin: 2rem 0;
    position: relative;
    z-index: 50;
  }
  
  .message-modern {
    background: var(--bg-secondary);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: var(--shadow-md);
    border: 2px solid;
    animation: slideInDown 0.5s ease-out;
  }
  
  .message-modern.success {
    border-color: var(--success-color);
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
  }
  
  .message-modern.error {
    border-color: var(--danger-color);
    background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
  }
  
  .message-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
  }
  
  .message-modern.success .message-icon {
    background: var(--success-color);
    color: white;
  }
  
  .message-modern.error .message-icon {
    background: var(--danger-color);
    color: white;
  }
  
  .message-content {
    flex: 1;
  }
  
  .message-content strong {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  
  .message-modern.success .message-content strong {
    color: #065f46;
  }
  
  .message-modern.error .message-content strong {
    color: #991b1b;
  }
  
  .message-content p {
    margin: 0;
    opacity: 0.9;
  }
  
  .message-content ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.5rem;
    opacity: 0.9;
  }
  
  .message-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.6;
    transition: all var(--transition-fast);
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }
  
  .message-close:hover {
    opacity: 1;
    background: rgba(0,0,0,0.1);
  }
  
  @keyframes slideInDown {
    from {
      transform: translateY(-30px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  }
  
  /* ============= CONTENU & CARTES ============= */
  
  .settings-content {
    margin: 2rem 0;
  }
  
  .tab-content {
    display: none;
    animation: fadeIn 0.5s ease-out;
  }
  
  .tab-content.active {
    display: block;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .modern-card {
    background: var(--bg-secondary);
    border-radius: 24px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-light);
    overflow: hidden;
    transition: all var(--transition-normal);
    margin-bottom: 2rem;
  }
  
  .modern-card:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
  }
  
  .card-header-modern {
    background: linear-gradient(135deg, var(--bg-tertiary) 0%, #f1f5f9 100%);
    padding: 2rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }
  
  .header-icon {
    width: 64px;
    height: 64px;
    background: var(--primary-color);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
    box-shadow: var(--shadow-md);
    position: relative;
  }
  
  .header-icon.medical {
    background: linear-gradient(135deg, #ef4444, #dc2626);
  }
  
  .header-icon::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color)cc);
    border-radius: 18px;
    z-index: -1;
    opacity: 0;
    transition: opacity var(--transition-normal);
  }
  
  .modern-card:hover .header-icon::before {
    opacity: 1;
  }
  
  .header-content h3 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
  }
  
  .header-content p {
    color: var(--text-secondary);
    margin: 0;
    font-size: 1.1rem;
  }
  
  .card-body-modern {
    padding: 2.5rem;
  }
  
  /* ============= FORMULAIRES MODERNES ============= */
  
  .modern-form {
    position: relative;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .medical-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
  
  .input-group-modern {
    position: relative;
  }
  
  .label-modern {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    font-size: 1rem;
  }
  
  .label-modern i {
    width: 20px;
    height: 20px;
    background: var(--primary-light);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 0.875rem;
  }
  
  .input-modern {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    transition: all var(--transition-normal);
    background: var(--bg-secondary);
    color: var(--text-primary);
  }
  
  .input-modern:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px var(--primary-light);
    transform: translateY(-1px);
  }
  
  .input-modern::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
  }
  
  textarea.input-modern {
    min-height: 120px;
    resize: vertical;
  }
  
  .form-actions-modern {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 2rem;
    border-top: 1px solid var(--border-light);
  }
  
  .btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition-normal);
    text-decoration: none;
    position: relative;
    overflow: hidden;
  }
  
  .btn-modern::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.5s ease;
  }
  
  .btn-modern:hover::before {
    width: 300px;
    height: 300px;
  }
  
  .btn-modern.primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color)dd);
    color: white;
    box-shadow: var(--shadow-md);
  }
  
  .btn-modern.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  
  .btn-modern i {
    font-size: 1.1rem;
  }
  
  .btn-modern.secondary {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
  }
  
  .btn-modern.secondary:hover {
    background: var(--border-color);
    color: var(--text-primary);
    transform: translateY(-2px);
  }
  
  .btn-modern.warning {
    background: linear-gradient(135deg, var(--warning-color), #ea580c);
    color: white;
    box-shadow: var(--shadow-md);
  }
  
  .btn-modern.warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  
  .btn-modern.danger-outline {
    background: transparent;
    color: var(--danger-color);
    border: 2px solid var(--danger-color);
  }
  
  .btn-modern.danger-outline:hover {
    background: var(--danger-color);
    color: white;
  }
  
  /* ============= STYLES SPECIFIQUES PAR ONGLET ============= */
  
  /* Grid pour champs full-width */
  .input-group-modern.full-width {
    grid-column: 1 / -1;
  }
  
  .security-grid {
    grid-template-columns: 1fr;
  }
  
  .preferences-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
  
  /* Header icons par catégorie */
  .header-icon.security {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
  }
  
  .header-icon.preferences {
    background: linear-gradient(135deg, #a855f7, #9333ea);
  }
  
  .header-icon.avatar {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
  }
  
  /* Conseils de sécurité */
  .security-tips {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #fbbf24;
    border-radius: 16px;
    padding: 1.5rem;
    margin: 2rem 0;
  }
  
  .security-tips h6 {
    color: #92400e;
    font-weight: 700;
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
  
  /* Sections préférences */
  .preferences-section {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-light);
  }
  
  .preferences-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }
  
  .section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
  }
  
  .section-title i {
    width: 32px;
    height: 32px;
    background: var(--primary-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
  }
  
  /* Grille de couleurs moderne */
  .color-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }
  
  .color-choice-modern {
    position: relative;
  }
  
  .color-choice-modern input {
    display: none;
  }
  
  .color-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    cursor: pointer;
    transition: all var(--transition-normal);
    background: var(--bg-secondary);
  }
  
  .color-option:hover {
    border-color: var(--color);
    box-shadow: 0 0 0 4px rgba(var(--color), 0.1);
  }
  
  .color-choice-modern input:checked + .color-option {
    border-color: var(--color);
    background: rgba(var(--color), 0.05);
    box-shadow: 0 0 0 4px rgba(var(--color), 0.1);
  }
  
  .color-preview {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    box-shadow: var(--shadow-md);
    flex-shrink: 0;
  }
  
  .color-info strong {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
  }
  
  .color-info small {
    color: var(--text-muted);
    font-size: 0.875rem;
  }
  
  /* Toggles modernes */
  .toggle-options-modern {
    display: grid;
    gap: 1rem;
  }
  
  .toggle-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--bg-tertiary);
    border-radius: 16px;
    border: 2px solid transparent;
    transition: all var(--transition-normal);
  }
  
  .toggle-item:hover {
    border-color: var(--primary-color);
    background: var(--primary-hover);
  }
  
  .toggle-info strong {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
  }
  
  .toggle-info small {
    color: var(--text-muted);
    font-size: 0.875rem;
  }
  
  .toggle-switch-modern {
    position: relative;
  }
  
  .toggle-switch-modern input {
    display: none;
  }
  
  .toggle-slider-modern {
    width: 60px;
    height: 32px;
    background: var(--border-color);
    border-radius: 32px;
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    display: block;
  }
  
  .toggle-slider-modern::after {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-sm);
  }
  
  .toggle-switch-modern input:checked + .toggle-slider-modern {
    background: var(--primary-color);
  }
  
  .toggle-switch-modern input:checked + .toggle-slider-modern::after {
    transform: translateX(28px);
  }
  
  .btn-back-main {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
  }
  
  .btn-back-main:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-2px);
  }
  
  /* Cards principales */
  .settings-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.3s;
  }
  
  .settings-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    transform: translateY(-2px);
  }
  
  .card-header {
    background: #f8fafc;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 700;
    font-size: 1.2rem;
    color: #1f2937;
    border-radius: 16px 16px 0 0;
  }
  
  .card-body {
    padding: 2rem;
  }
  
  /* Formulaires modernisés */
  .modern-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
  }
  
  .modern-input {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s;
    font-size: 1rem;
  }
  
  .modern-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
  }
  
  /* Grille de couleurs */
  .color-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }
  
  .color-choice input {
    display: none;
  }
  
  .color-label {
    aspect-ratio: 1;
    border-radius: 12px;
    display: flex;
    align-items: end;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
  }
  
  .color-choice input:checked + .color-label {
    border-color: #1f2937;
    transform: scale(1.05);
  }
  
  .color-choice input:checked + .color-label::after {
    content: "✓";
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0,0,0,0.8);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
  }
  
  .color-name {
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 0.5rem;
    border-radius: 8px 8px 0 0;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  /* Preview */
  .preview-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }
  
  .preview-item {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    transition: all 0.3s;
  }
  
  .preview-item:hover {
    transform: translateY(-2px);
  }
  
  .preview-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
  }
  
  .preview-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
  }
  
  .preview-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
  }
  
  /* Options */
  .options-grid {
    display: grid;
    gap: 1rem;
  }
  
  .option-item {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s;
  }
  
  .option-item:hover {
    background: #f1f5f9;
  }
  
  .option-info strong {
    display: block;
    color: #1f2937;
    font-weight: 600;
  }
  
  .option-info small {
    color: #6b7280;
    font-size: 0.85rem;
  }
  
  /* Toggle switches */
  .toggle-switch {
    position: relative;
  }
  
  .toggle-switch input {
    display: none;
  }
  
  .toggle-slider {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
    background: #d1d5db;
    border-radius: 28px;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .toggle-slider::after {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s;
  }
  
  .toggle-switch input:checked + .toggle-slider {
    background: var(--primary-color);
  }
  
  .toggle-switch input:checked + .toggle-slider::after {
    transform: translateX(22px);
  }
  
  /* Avatar Management */
  .avatar-management {
    display: grid;
    gap: 2rem;
  }
  
  .avatar-display {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 16px;
    border: 2px solid #e5e7eb;
  }
  
  .avatar-container {
    position: relative;
    display: inline-block;
  }
  
  .avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transition: all 0.3s;
  }
  
  .avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s;
    color: white;
    font-size: 1.5rem;
  }
  
  .avatar-container:hover .avatar-overlay {
    opacity: 1;
  }
  
  .avatar-container:hover .avatar-preview {
    transform: scale(1.05);
  }
  
  .avatar-info h6 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  /* Upload Area */
  .avatar-upload-form {
    border: 2px dashed #d1d5db;
    border-radius: 16px;
    transition: all 0.3s;
    overflow: hidden;
  }
  
  .upload-area {
    position: relative;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .upload-area:hover {
    background: rgba(59,130,246,0.05);
    border-color: var(--primary-color);
  }
  
  .upload-area.dragover {
    background: rgba(59,130,246,0.1);
    border-color: var(--primary-color);
    transform: scale(1.02);
  }
  
  .upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }
  
  .upload-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), rgba(59,130,246,0.8));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 2rem;
    transition: all 0.3s;
  }
  
  .upload-area:hover .upload-icon {
    transform: scale(1.1);
  }
  
  .upload-content h6 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  .upload-content p {
    margin: 0 0 1rem 0;
    color: #6b7280;
  }
  
  /* Upload Preview */
  .upload-preview {
    padding: 2rem;
    background: #f8fafc;
    text-align: center;
    border-top: 1px solid #e5e7eb;
  }
  
  .upload-preview img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    margin-bottom: 1rem;
  }
  
  .preview-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }
  
  /* Upload Tips */
  .upload-tips {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 1.5rem;
  }
  
  .upload-tips h6 {
    margin: 0 0 1rem 0;
    color: #856404;
    font-weight: 600;
  }
  
  .upload-tips ul {
    margin: 0;
    padding-left: 1.2rem;
    color: #856404;
  }
  
  .upload-tips li {
    margin-bottom: 0.5rem;
  }
  
  .upload-tips li:last-child {
    margin-bottom: 0;
  }
  
  /* Actions */
  .form-actions {
    border-top: 1px solid #e5e7eb;
    padding-top: 1.5rem;
  }
  
  /* ============= STYLES AVATAR MODERNE ============= */
  
  .avatar-section {
    display: grid;
    gap: 2rem;
  }
  
  .avatar-display-modern {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: var(--bg-tertiary);
    border-radius: 20px;
    border: 2px solid var(--border-light);
  }
  
  .avatar-container-modern {
    position: relative;
    flex-shrink: 0;
  }
  
  .avatar-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-lg);
    transition: all var(--transition-normal);
  }
  
  .avatar-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: all var(--transition-normal);
    color: white;
    cursor: pointer;
  }
  
  .avatar-container-modern:hover .avatar-overlay-modern {
    opacity: 1;
  }
  
  .avatar-container-modern:hover .avatar-image {
    transform: scale(1.05);
  }
  
  .avatar-details h6 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
  }
  
  .avatar-details p {
    margin: 0;
    color: var(--text-secondary);
  }
  
  .upload-area-modern {
    border: 3px dashed var(--border-color);
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
  }
  
  .upload-area-modern:hover,
  .upload-area-modern.dragover {
    border-color: var(--primary-color);
    background: var(--primary-hover);
    transform: scale(1.02);
  }
  
  .upload-input-hidden {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }
  
  .upload-icon-modern {
    width: 80px;
    height: 80px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 2rem;
    transition: all var(--transition-normal);
  }
  
  .upload-area-modern:hover .upload-icon-modern {
    transform: scale(1.1) rotate(5deg);
  }
  
  .upload-content-modern h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
  }
  
  .upload-content-modern p {
    color: var(--text-secondary);
    margin: 0 0 1.5rem 0;
  }
  
  .upload-formats {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    align-items: center;
  }
  
  .format-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  
  .format-info {
    color: var(--text-muted);
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .upload-preview-modern {
    padding: 2rem;
    text-align: center;
    background: var(--bg-tertiary);
    border-radius: 16px;
  }
  
  .upload-preview-modern img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-lg);
    margin-bottom: 1.5rem;
  }
  
  .preview-actions-modern {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }
  
  .avatar-tips {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #0ea5e9;
    border-radius: 16px;
    padding: 1.5rem;
  }
  
  .avatar-tips h6 {
    color: #0369a1;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  
  .tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
  }
  
  .tip-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #0369a1;
    font-size: 0.875rem;
  }
  
  .tip-item i {
    width: 20px;
    height: 20px;
    background: #0ea5e9;
    color: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
  }
  
  }
  
  /* ============= RESPONSIVE DESIGN ============= */
  
  @media (max-width: 1200px) {
    .settings-modern-container {
      padding: 0 0.75rem;
    }
    
    .hero-main {
      flex-direction: column;
      text-align: center;
      gap: 1.5rem;
    }
    
    .hero-text h1 {
      font-size: 2.5rem;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
  }
  
  @media (max-width: 768px) {
    .settings-modern-container {
      padding: 0 0.5rem;
    }
    
    .settings-hero {
      padding: 2rem 1.5rem;
      margin: 1rem 0;
    }
    
    .hero-content {
      flex-direction: column;
      gap: 2rem;
      text-align: center;
    }
    
    .hero-text h1 {
      font-size: 2rem;
    }
    
    .hero-stats {
      justify-content: center;
    }
    
    .nav-container {
      padding: 0.25rem;
      gap: 0.125rem;
    }
    
    .nav-tab {
      min-width: 80px;
      padding: 0.75rem 0.5rem;
    }
    
    .nav-tab span {
      font-size: 0.75rem;
    }
    
    .nav-tab i {
      font-size: 1.25rem;
    }
    
    .card-header-modern {
      flex-direction: column;
      text-align: center;
      padding: 1.5rem;
      gap: 1rem;
    }
    
    .header-icon {
      width: 56px;
      height: 56px;
      font-size: 1.5rem;
    }
    
    .header-content h3 {
      font-size: 1.5rem;
    }
    
    .card-body-modern {
      padding: 1.5rem;
    }
    
    .form-actions-modern {
      justify-content: center;
    }
    
    .btn-modern {
      width: 100%;
      justify-content: center;
    }
  }
  
  @media (max-width: 480px) {
    .settings-hero {
      padding: 1.5rem 1rem;
    }
    
    .hero-icon {
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
    }
    
    .hero-text h1 {
      font-size: 1.75rem;
    }
    
    .hero-text p {
      font-size: 1rem;
    }
    
    .hero-stats {
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
    }
    
    .nav-tab span {
      display: none;
    }
    
    .nav-tab {
      min-width: 60px;
    }
    
    .input-modern {
      padding: 0.875rem 1rem;
    }
    
    .form-grid {
      gap: 1.5rem;
    }
  }
  
  /* ============= ANIMATIONS SUPPLEMENTAIRES ============= */
  
  @media (prefers-reduced-motion: no-preference) {
    .modern-card {
      animation: slideUp 0.6s ease-out;
    }
    
    .modern-card:nth-child(2) {
      animation-delay: 0.1s;
    }
    
    .modern-card:nth-child(3) {
      animation-delay: 0.2s;
    }
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* ============= UTILITAIRES ============= */
  
  .text-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color)cc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // ============= GESTION DES TABS MODERNES =============
  
  function initTabNavigation() {
    const tabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const targetTab = tab.dataset.tab;
        
        // Retirer les classes actives
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Ajouter les classes actives
        tab.classList.add('active');
        const targetContent = document.getElementById(targetTab + '-tab');
        if (targetContent) {
          targetContent.classList.add('active');
          
          // Animation d'entrée
          targetContent.style.opacity = '0';
          targetContent.style.transform = 'translateY(20px)';
          
          requestAnimationFrame(() => {
            targetContent.style.transition = 'all 0.3s ease';
            targetContent.style.opacity = '1';
            targetContent.style.transform = 'translateY(0)';
          });
        }
        
        // Vibration tactile pour mobile
        if (navigator.vibrate) {
          navigator.vibrate(50);
        }
        
        // Sauvegarder l'onglet actuel
        localStorage.setItem('activeSettingsTab', targetTab);
      });
    });
    
    // Restaurer le dernier onglet actif
    const lastActiveTab = localStorage.getItem('activeSettingsTab');
    if (lastActiveTab) {
      const tabToActivate = document.querySelector(`[data-tab="${lastActiveTab}"]`);
      if (tabToActivate) {
        tabToActivate.click();
      }
    }
  }
  
  // ============= COULEURS ET PREVIEW =============
  
  const colorMap = {
    'blue': '#3b82f6',
    'purple': '#8b5cf6',
    'green': '#10b981', 
    'orange': '#f59e0b',
    'red': '#ef4444',
    'pink': '#ec4899'
  };
  
  // ============= PREVIEW COULEURS DYNAMIQUE =============
  
  function updateColorPreview() {
    const selectedColor = document.querySelector('input[name="theme_color"]:checked')?.value;
    if (selectedColor && colorMap[selectedColor]) {
      const color = colorMap[selectedColor];
      
      // Mettre à jour les variables CSS en temps réel
      document.documentElement.style.setProperty('--primary-color', color);
      document.documentElement.style.setProperty('--primary-light', color + '20');
      document.documentElement.style.setProperty('--primary-hover', color + '15');
      
      // Animation de transition
      document.body.style.transition = 'all 0.3s ease';
      
      // Mettre à jour le header hero
      const hero = document.querySelector('.settings-hero');
      if (hero) {
        hero.style.background = `linear-gradient(135deg, ${color}, ${color}dd)`;
      }
      
      // Effet de pulsation pour indiquer le changement
      const colorOptions = document.querySelectorAll('.color-label');
      colorOptions.forEach(option => {
        option.style.transform = 'scale(0.95)';
        setTimeout(() => {
          option.style.transform = 'scale(1)';
        }, 150);
      });
    }
  }
  
  // ============= GESTION AVANCEE DES FORMULAIRES =============
  
  function initAdvancedForms() {
    // Animation des inputs au focus
    const inputs = document.querySelectorAll('.input-modern');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
        if (this.value.trim() !== '') {
          this.parentElement.classList.add('filled');
        } else {
          this.parentElement.classList.remove('filled');
        }
      });
      
      // Vérifier si déjà rempli au chargement
      if (input.value.trim() !== '') {
        input.parentElement.classList.add('filled');
      }
    });
  }
  
  // ============= MESSAGES INTERACTIFS =============
  
  function initInteractiveMessages() {
    const messages = document.querySelectorAll('.message-modern');
    messages.forEach((message, index) => {
      // Animation d'entrée échelonnée
      message.style.animationDelay = `${index * 0.1}s`;
      
      // Auto-dismiss pour les messages de succès
      if (message.classList.contains('success')) {
        setTimeout(() => {
          message.style.transform = 'translateX(100%)';
          message.style.opacity = '0';
          setTimeout(() => {
            message.remove();
          }, 300);
        }, 5000);
      }
    });
  }
  
  // ============= DETECTION CHANGEMENTS =============
  
  function initChangeDetection() {
    let hasUnsavedChanges = false;
    
    const trackableInputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
    trackableInputs.forEach(input => {
      const originalValue = input.value;
      
      input.addEventListener('input', () => {
        hasUnsavedChanges = input.value !== originalValue;
        updateUnsavedIndicator(hasUnsavedChanges);
      });
    });
    
    // Avertissement avant fermeture
    window.addEventListener('beforeunload', (e) => {
      if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'Vous avez des modifications non sauvegardées.';
      }
    });
    
    // Réinitialiser après soumission
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', () => {
        hasUnsavedChanges = false;
      });
    });
  }
  
  function updateUnsavedIndicator(hasChanges) {
    // Optionnel: ajouter un indicateur visuel des modifications
    const indicator = document.querySelector('.unsaved-indicator');
    if (indicator) {
      indicator.style.display = hasChanges ? 'block' : 'none';
    }
  }
  
  // ============= INITIALISATION PRINCIPALE =============
  
  // Initialiser tous les modules
  initTabNavigation();
  initAdvancedForms();
  initInteractiveMessages();
  initChangeDetection();
  
  // Écouter les changements de couleur
  document.querySelectorAll('input[name="theme_color"]').forEach(radio => {
    radio.addEventListener('change', updateColorPreview);
  });
  
  // Initialiser la prévisualisation
  updateColorPreview();
  
  // Gestion de l'upload d'avatar (garder la fonction existante)
  initAvatarUpload();
  
  // ============= AMELIORATIONS UX =============
  
  // Smooth scroll pour les liens d'ancrage
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
  
  // Indicateur de chargement pour les formulaires
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Traitement...</span>';
        submitBtn.disabled = true;
        
        // Réactiver si le formulaire ne se soumet pas (erreur)
        setTimeout(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }, 5000);
      }
    });
  });
  
  // Raccourcis clavier pour navigation rapide
  document.addEventListener('keydown', function(e) {
    // Alt + 1-5 pour changer d'onglet
    if (e.altKey && e.key >= '1' && e.key <= '5') {
      e.preventDefault();
      const tabIndex = parseInt(e.key) - 1;
      const tabs = document.querySelectorAll('.nav-tab');
      if (tabs[tabIndex]) {
        tabs[tabIndex].click();
      }
    }
    
    // Echap pour fermer les messages
    if (e.key === 'Escape') {
      document.querySelectorAll('.message-modern').forEach(message => {
        message.remove();
      });
    }
  });
  
  // Animation au scroll
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animationPlayState = 'running';
      }
    });
  }, observerOptions);
  
  document.querySelectorAll('.modern-card').forEach(card => {
    observer.observe(card);
  });

});

// Fonction pour gérer l'upload d'avatar
function initAvatarUpload() {
  const uploadArea = document.getElementById('uploadArea');
  const avatarInput = document.getElementById('avatarInput');
  const uploadPreview = document.getElementById('uploadPreview');
  const previewImage = document.getElementById('previewImage');
  
  if (!uploadArea || !avatarInput) return;
  
  // Drag & Drop
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
  });
  
  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }
  
  ['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
  });
  
  uploadArea.addEventListener('drop', handleDrop, false);
  
  function handleDrop(e) {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      handleFileSelect(files[0]);
    }
  }
  
  // Click to select
  uploadArea.addEventListener('click', () => avatarInput.click());
  
  // File input change
  avatarInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
      handleFileSelect(e.target.files[0]);
    }
  });
  
  function handleFileSelect(file) {
    // Vérification du type de fichier
    if (!file.type.startsWith('image/')) {
      alert('Veuillez sélectionner un fichier image.');
      return;
    }
    
    // Vérification de la taille (2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('La taille du fichier ne doit pas dépasser 2MB.');
      return;
    }
    
    // Prévisualisation
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImage.src = e.target.result;
      uploadArea.style.display = 'none';
      uploadPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

// Fonction pour annuler l'upload
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

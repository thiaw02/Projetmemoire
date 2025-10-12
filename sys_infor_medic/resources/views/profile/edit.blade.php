@extends('layouts.app')

@section('content')
<div class="container-lg">
  <div class="row g-4">
    <div class="col-lg-3 mb-4">
      <div class="sidebar-standardized">
        @include('layouts.partials.profile_sidebar')
      </div>
    </div>
    
    <div class="col-lg-9">
      {{-- Header paramètres adaptatif selon le rôle --}}
      <div class="settings-header role-{{ $user->role ?? 'user' }}">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1>
              <i class="bi bi-gear-fill me-2"></i>
              @switch($user->role)
                @case('admin') Paramètres Administrateur @break
                @case('secretaire') Paramètres Secrétaire @break
                @case('medecin') Paramètres Médecin @break
                @case('infirmier') Paramètres Infirmier @break
                @default Paramètres du Compte
              @endswitch
            </h1>
            <p class="mb-0">Gérez vos informations personnelles et professionnelles</p>
          </div>
          <a href="@php
            echo match($user->role ?? 'user') {
              'admin' => route('admin.dashboard'),
              'secretaire' => route('secretaire.dashboard'),
              'medecin' => route('medecin.dashboard'),
              'infirmier' => route('infirmier.dashboard'),
              'patient' => route('patient.dashboard'),
              default => route('login')
            };
          @endphp" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-md-inline">Retour</span>
          </a>
        </div>
      </div>

      {{-- Messages --}}
      @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-3">
          <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start mb-3">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <div>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      {{-- Informations personnelles --}}
      <div class="settings-card">
        <div class="card-header">
          <i class="bi bi-person me-2"></i>Informations Personnelles
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.update') }}" class="modern-form">
            @csrf
            @method('PATCH')
            
            <div class="row g-4">
              <div class="col-md-6">
                <label class="setting-label">
                  <i class="bi bi-person-fill me-2"></i>Nom complet
                </label>
                <input type="text" name="name" class="form-control setting-input" 
                       required value="{{ old('name', $user->name) }}" 
                       placeholder="Prénom et nom de famille">
              </div>
              
              <div class="col-md-6">
                <label class="setting-label">
                  <i class="bi bi-envelope-fill me-2"></i>Adresse email
                </label>
                <input type="email" name="email" class="form-control setting-input" 
                       required value="{{ old('email', $user->email) }}" 
                       placeholder="votre.email@exemple.com">
              </div>
            </div>
            
            {{-- Champs spécifiques selon le rôle --}}
            @if($user->role === 'medecin')
              <div class="role-specific-section">
                <h6 class="role-subtitle">
                  <i class="bi bi-briefcase me-2"></i>Informations Médicales
                </h6>
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="setting-label">
                      <i class="bi bi-heart-pulse me-2"></i>Spécialité
                    </label>
                    <input type="text" name="specialite" class="form-control setting-input" 
                           value="{{ old('specialite', $user->specialite) }}" 
                           placeholder="Ex: Cardiologie, Neurologie...">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="setting-label">
                      <i class="bi bi-card-text me-2"></i>Matricule professionnel
                    </label>
                    <input type="text" name="matricule" class="form-control setting-input" 
                           value="{{ old('matricule', $user->matricule) }}" 
                           placeholder="Numéro d'identification">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="setting-label">
                      <i class="bi bi-building me-2"></i>Cabinet/Service
                    </label>
                    <input type="text" name="cabinet" class="form-control setting-input" 
                           value="{{ old('cabinet', $user->cabinet) }}" 
                           placeholder="Cabinet Médical X">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="setting-label">
                      <i class="bi bi-clock me-2"></i>Horaires de consultation
                    </label>
                    <textarea name="horaires" class="form-control setting-input" rows="2" 
                              placeholder="Ex: Lun-Ven 9:00-17:00, Sam 9:00-13:00">{{ old('horaires', $user->horaires) }}</textarea>
                  </div>
                </div>
              </div>
            @endif
            
            @if(in_array($user->role, ['secretaire', 'infirmier', 'admin']))
              <div class="role-specific-section">
                <h6 class="role-subtitle">
                  <i class="bi bi-building-gear me-2"></i>Informations Professionnelles
                </h6>
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="setting-label">
                      <i class="bi bi-telephone me-2"></i>Téléphone professionnel
                    </label>
                    <input type="text" name="pro_phone" class="form-control setting-input" 
                           value="{{ old('pro_phone', $user->pro_phone) }}" 
                           placeholder="+221 77 000 00 00">
                  </div>
                </div>
              </div>
            @endif
            
            <div class="form-actions">
              <button type="submit" class="btn-save">
                <i class="bi bi-check2-circle me-2"></i>Enregistrer les modifications
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Mot de passe --}}
      <div class="settings-card">
        <div class="card-header">
          <i class="bi bi-shield-lock me-2"></i>Sécurité du Compte
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.password.update') }}" class="modern-form">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
              <div class="col-md-4">
                <label class="setting-label">
                  <i class="bi bi-key me-2"></i>Mot de passe actuel
                </label>
                <input type="password" name="current_password" class="form-control setting-input" 
                       required placeholder="••••••••">
              </div>
              
              <div class="col-md-4">
                <label class="setting-label">
                  <i class="bi bi-shield-plus me-2"></i>Nouveau mot de passe
                </label>
                <input type="password" name="password" class="form-control setting-input" 
                       required placeholder="••••••••">
              </div>
              
              <div class="col-md-4">
                <label class="setting-label">
                  <i class="bi bi-shield-check me-2"></i>Confirmer le mot de passe
                </label>
                <input type="password" name="password_confirmation" class="form-control setting-input" 
                       required placeholder="••••••••">
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="btn-save security">
                <i class="bi bi-shield-fill-check me-2"></i>Mettre à jour le mot de passe
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Photo de profil --}}
      <div class="settings-card">
        <div class="card-header">
          <i class="bi bi-person-circle me-2"></i>Photo de Profil
        </div>
        <div class="card-body">
          <div class="avatar-section">
            @php
              $user = auth()->user();
              $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
              $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
                        ? $rawAvatar
                        : asset(ltrim($rawAvatar, '/'));
            @endphp
            
            <div class="current-avatar">
              <img src="{{ $avatar }}" alt="Photo actuelle" class="avatar-preview">
              <div class="avatar-info">
                <h6>Photo actuelle</h6>
                <small class="text-muted">Image {{ $user->avatar_url ? 'personnalisée' : 'générée automatiquement' }}</small>
              </div>
            </div>
            
            <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="avatar-form">
              @csrf
              <div class="upload-zone">
                <input type="file" name="avatar" accept="image/*" class="form-control" required id="avatarInput">
                <label for="avatarInput" class="upload-label">
                  <i class="bi bi-cloud-upload me-2"></i>
                  Choisir une nouvelle photo
                </label>
              </div>
              <button type="submit" class="btn-upload">
                <i class="bi bi-upload me-2"></i>Téléverser
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- Section spéciale pour les patients --}}
      @if($user->role === 'patient')
        <div class="settings-card">
          <div class="card-header">
            <i class="bi bi-heart-pulse me-2"></i>Profil Médical
          </div>
          <div class="card-body">
            @php $p = $user->patient; @endphp
            <form method="POST" action="{{ route('profile.patient.update') }}" class="modern-form">
              @csrf
              @method('PUT')
              
              <div class="row g-4">
                <div class="col-md-6">
                  <label class="setting-label">Nom</label>
                  <input type="text" name="nom" class="form-control setting-input" required value="{{ old('nom', $p->nom ?? '') }}">
                </div>
                <div class="col-md-6">
                  <label class="setting-label">Prénom</label>
                  <input type="text" name="prenom" class="form-control setting-input" required value="{{ old('prenom', $p->prenom ?? '') }}">
                </div>
                <div class="col-md-6">
                  <label class="setting-label">Téléphone</label>
                  <input type="text" name="telephone" class="form-control setting-input" value="{{ old('telephone', $p->telephone ?? '') }}">
                </div>
                <div class="col-md-6">
                  <label class="setting-label">Sexe</label>
                  @php $sx = old('sexe', $p->sexe ?? ''); @endphp
                  <select name="sexe" class="form-select setting-input" required>
                    <option value="">-- Choisir --</option>
                    <option value="Masculin" {{ $sx=='Masculin'?'selected':'' }}>Masculin</option>
                    <option value="Féminin" {{ $sx=='Féminin'?'selected':'' }}>Féminin</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="setting-label">Date de naissance</label>
                  <input type="date" name="date_naissance" class="form-control setting-input" required value="{{ old('date_naissance', ($p && $p->date_naissance) ? \Carbon\Carbon::parse($p->date_naissance)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                  <label class="setting-label">Groupe sanguin</label>
                  <input type="text" name="groupe_sanguin" class="form-control setting-input" value="{{ old('groupe_sanguin', $p->groupe_sanguin ?? '') }}">
                </div>
                <div class="col-12">
                  <label class="setting-label">Adresse</label>
                  <input type="text" name="adresse" class="form-control setting-input" value="{{ old('adresse', $p->adresse ?? '') }}">
                </div>
                <div class="col-12">
                  <label class="setting-label">Antécédents médicaux</label>
                  <textarea name="antecedents" class="form-control setting-input" rows="3">{{ old('antecedents', $p->antecedents ?? '') }}</textarea>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn-save">
                  <i class="bi bi-heart-fill me-2"></i>Enregistrer le profil médical
                </button>
              </div>
            </form>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<style>
  @php
    $roleColors = [
      'admin' => ['primary' => '#dc2626', 'secondary' => '#b91c1c'],
      'secretaire' => ['primary' => '#059669', 'secondary' => '#047857'],
      'medecin' => ['primary' => '#3b82f6', 'secondary' => '#1e40af'],
      'infirmier' => ['primary' => '#8b5cf6', 'secondary' => '#7c3aed'],
      'patient' => ['primary' => '#f59e0b', 'secondary' => '#d97706']
    ];
    $currentRole = $user->role ?? 'user';
    $colors = $roleColors[$currentRole] ?? $roleColors['admin'];
  @endphp
  
  :root {
    --role-primary: {{ $colors['primary'] }};
    --role-secondary: {{ $colors['secondary'] }};
    --settings-success: #059669;
    --settings-danger: #dc2626;
  }
  
  /* Header adaptatif selon le rôle */
  .settings-header {
    background: linear-gradient(135deg, var(--role-primary), var(--role-secondary));
    color: white;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  }
  
  .settings-header h1 { 
    font-size: 1.5rem; 
    font-weight: 700; 
    margin: 0; 
    display: flex; 
    align-items: center; 
  }
  
  .btn-back {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .btn-back:hover { 
    background: white; 
    color: var(--role-primary); 
    transform: translateY(-1px); 
  }
  
  /* Cards modernes */
  .settings-card {
    background: white;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.2s ease;
  }
  
  .settings-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    transform: translateY(-1px);
  }
  
  .card-header {
    background: #f8fafc;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    color: #1f2937;
    border-bottom: 1px solid #e5e7eb;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
  }
  
  .card-body { 
    padding: 1.25rem 1.5rem; 
  }
  
  /* Formulaires modernisés */
  .setting-label {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
  }
  
  .setting-input {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s;
    font-size: 0.95rem;
  }
  
  .setting-input:focus {
    border-color: var(--role-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    outline: none;
  }
  
  /* Sections spécifiques aux rôles */
  .role-specific-section {
    margin-top: 1.75rem;
    padding-top: 1.75rem;
    border-top: 2px solid #f3f4f6;
  }
  
  .role-subtitle {
    color: var(--role-primary);
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    font-size: 1rem;
  }
  
  /* Actions des formulaires */
  .form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    text-align: right;
  }
  
  .btn-save {
    background: linear-gradient(135deg, var(--role-primary), var(--role-secondary));
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-save:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
  }
  
  .btn-save.security {
    background: linear-gradient(135deg, var(--settings-success), #047857);
  }
  
  /* Section avatar */
  .avatar-section {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
  }
  
  .current-avatar {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .avatar-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
  }
  
  .avatar-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
  }
  
  .upload-zone {
    position: relative;
  }
  
  .upload-zone input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
  }
  
  .upload-label {
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    margin: 0;
    color: #6b7280;
  }
  
  .upload-label:hover {
    border-color: var(--role-primary);
    background: rgba(59,130,246,0.05);
    color: var(--role-primary);
  }
  
  .btn-upload {
    background: var(--role-primary);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
    display: flex;
    align-items: center;
  }
  
  .btn-upload:hover {
    background: var(--role-secondary);
    transform: translateY(-1px);
  }
  
  /* Sidebar optimisée */
  .sidebar-compact {
    position: sticky;
    top: 1rem;
  }
  
  .container-lg {
    max-width: 1400px;
  }
  
  /* Optimisations de la sidebar moderne */
  .modern-sidebar {
    margin-bottom: 0;
    border-radius: 16px;
  }
  
  .sidebar-body {
    padding: 1.5rem 1.25rem;
  }
  
  .profile-avatar img {
    width: 70px;
    height: 70px;
  }
  
  .profile-name {
    font-size: 1.1rem;
  }
  
  .profile-role {
    font-size: 0.75rem;
    padding: 0.3rem 0.8rem;
  }
  
  .profile-settings-btn {
    padding: 0.5rem 1.2rem;
    font-size: 0.8rem;
  }
  
  .profile-info-item {
    padding: 0.6rem 0.8rem;
    margin-bottom: 0.5rem;
  }
  
  .info-label {
    font-size: 0.75rem;
  }
  
  .info-value {
    font-size: 0.75rem;
  }
  
  .sidebar-icon {
    width: 14px;
    height: 14px;
  }
  
  /* Responsive optimisé */
  @media (min-width: 1400px) {
    .sidebar-compact {
      max-width: 280px;
    }
  }
  
  @media (min-width: 1200px) and (max-width: 1399px) {
    .sidebar-compact {
      max-width: 320px;
    }
    
    .profile-avatar img {
      width: 75px;
      height: 75px;
    }
    
    .sidebar-body {
      padding: 1.75rem 1.5rem;
    }
  }
  
  @media (max-width: 991px) {
    .sidebar-compact {
      position: static;
      margin-bottom: 1.5rem;
    }
    
    .profile-avatar {
      text-align: center;
    }
    
    .profile-avatar img {
      width: 80px;
      height: 80px;
    }
  }
  
  @media (max-width: 767px) {
    .settings-header .d-flex { 
      flex-direction: column; 
      gap: 1rem; 
      text-align: center; 
    }
    
    .avatar-section, .avatar-form { 
      flex-direction: column; 
      align-items: flex-start; 
    }
    
    .form-actions { 
      text-align: center; 
    }
    
    .sidebar-compact {
      position: static;
    }
    
    .container-lg {
      padding-left: 1rem;
      padding-right: 1rem;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Indicateur de modifications non sauvegardées
  let hasChanges = false;
  
  document.querySelectorAll('input, select, textarea').forEach(input => {
    const originalValue = input.value;
    
    input.addEventListener('change', () => {
      if (!hasChanges && input.value !== originalValue) {
        hasChanges = true;
        // Ajouter un indicateur visuel sur les boutons de sauvegarde
        document.querySelectorAll('.btn-save').forEach(btn => {
          if (!btn.classList.contains('modified')) {
            btn.classList.add('modified');
            btn.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
            const icon = btn.querySelector('i');
            if (icon) {
              icon.className = 'bi bi-exclamation-circle me-2';
            }
          }
        });
      }
    });
  });
  
  // Confirmation avant de quitter si il y a des modifications
  window.addEventListener('beforeunload', function(e) {
    if (hasChanges) {
      e.preventDefault();
      e.returnValue = 'Vous avez des modifications non sauvegardées. Voulez-vous vraiment quitter ?';
    }
  });
  
  // Réinitialiser l'indicateur après soumission
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      hasChanges = false;
      const submitBtn = form.querySelector('.btn-save');
      if (submitBtn) {
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enregistrement...';
        submitBtn.disabled = true;
      }
    });
  });
});
</script>
@endsection
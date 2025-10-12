@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-4 mb-4">
    @include('layouts.partials.profile_sidebar')
  </div>
  <div class="col-lg-8">
    <div class="settings-modern-container">
      
      {{-- Header infirmier --}}
      <div class="settings-hero">
        <div class="hero-content">
          <div class="hero-main">
            <div class="hero-icon">
              <i class="bi bi-heart-pulse"></i>
            </div>
            <div class="hero-text">
              <h1>Paramètres Infirmier</h1>
              <p>Gérez vos informations de soins et services</p>
              <div class="hero-stats">
                <div class="stat-item">
                  <i class="bi bi-bandaid"></i>
                  <span>Soins paramédicaux</span>
                </div>
                <div class="stat-item">
                  <i class="bi bi-clipboard2-pulse"></i>
                  <span>Suivi patient</span>
                </div>
              </div>
            </div>
          </div>
          <div class="hero-actions">
            <a href="{{ route('infirmier.dashboard') }}" class="btn-hero-back">
              <i class="bi bi-arrow-left"></i>
              <span>Retour</span>
            </a>
          </div>
        </div>
      </div>

      {{-- Navigation --}}
      <div class="settings-nav">
        <div class="nav-container">
          <button class="nav-tab active" data-tab="personal">
            <i class="bi bi-person-fill"></i>
            <span>Personnel</span>
          </button>
          <button class="nav-tab" data-tab="professional">
            <i class="bi bi-heart-pulse"></i>
            <span>Professionnel</span>
          </button>
          <button class="nav-tab" data-tab="security">
            <i class="bi bi-shield-lock"></i>
            <span>Sécurité</span>
          </button>
          <button class="nav-tab" data-tab="avatar">
            <i class="bi bi-camera"></i>
            <span>Photo</span>
          </button>
        </div>
      </div>

      {{-- Messages --}}
      @if(session('success'))
        <div class="message-modern success">
          <div class="message-icon"><i class="bi bi-check-circle-fill"></i></div>
          <div class="message-content">
            <strong>Succès !</strong>
            <p>{{ session('success') }}</p>
          </div>
        </div>
      @endif

      {{-- Contenu tabs --}}
      <div class="settings-content">
        
        {{-- Tab Personnel --}}
        <div class="tab-content active" id="personal-tab">
          <div class="modern-card">
            <div class="card-header-modern">
              <div class="header-icon">
                <i class="bi bi-person-fill"></i>
              </div>
              <div class="header-content">
                <h3>Informations Personnelles</h3>
                <p>Vos données personnelles de base</p>
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
                           placeholder="Nom Prénom" required>
                  </div>
                  
                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-envelope"></i>
                      <span>Email professionnel</span>
                    </label>
                    <input type="email" name="email" class="input-modern" 
                           value="{{ old('email', auth()->user()->email) }}" 
                           placeholder="nom@hopital.com" required>
                  </div>

                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-telephone"></i>
                      <span>Téléphone</span>
                    </label>
                    <input type="tel" name="phone" class="input-modern" 
                           value="{{ old('phone', auth()->user()->phone) }}" 
                           placeholder="+221 XX XXX XX XX">
                  </div>

                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-geo-alt"></i>
                      <span>Adresse</span>
                    </label>
                    <input type="text" name="adresse" class="input-modern" 
                           value="{{ old('adresse', auth()->user()->adresse) }}" 
                           placeholder="Adresse complète">
                  </div>
                </div>
                
                <div class="form-actions-modern">
                  <button type="submit" class="btn-modern btn-nurse-personal">
                    <i class="bi bi-person-check-fill"></i>
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
                <i class="bi bi-heart-pulse"></i>
              </div>
              <div class="header-content">
                <h3>Informations Professionnelles</h3>
                <p>Détails de votre pratique infirmière</p>
              </div>
            </div>
            <div class="card-body-modern">
              <form method="POST" action="{{ route('profile.update') }}" class="modern-form">
                @csrf
                @method('PATCH')
                
                <div class="form-grid professional-grid">
                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-bandaid"></i>
                      <span>Service/Spécialité</span>
                    </label>
                    <input type="text" name="service" class="input-modern" 
                           value="{{ old('service', auth()->user()->service) }}" 
                           placeholder="Médecine générale, Urgences, etc.">
                  </div>
                  
                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-card-text"></i>
                      <span>Numéro d'ordre infirmier</span>
                    </label>
                    <input type="text" name="matricule" class="input-modern" 
                           value="{{ old('matricule', auth()->user()->matricule) }}" 
                           placeholder="Numéro d'ordre professionnel">
                  </div>
                  
                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-hospital"></i>
                      <span>Établissement</span>
                    </label>
                    <input type="text" name="etablissement" class="input-modern" 
                           value="{{ old('etablissement', auth()->user()->etablissement) }}" 
                           placeholder="Hôpital ou clinique">
                  </div>
                  
                  <div class="input-group-modern">
                    <label class="label-modern">
                      <i class="bi bi-calendar-check"></i>
                      <span>Années d'expérience</span>
                    </label>
                    <input type="number" name="experience" class="input-modern" 
                           value="{{ old('experience', auth()->user()->experience) }}" 
                           placeholder="Ex: 5" min="0">
                  </div>
                  
                  <div class="input-group-modern full-width">
                    <label class="label-modern">
                      <i class="bi bi-list-stars"></i>
                      <span>Compétences spécialisées</span>
                    </label>
                    <textarea name="competences" class="input-modern" rows="3" 
                              placeholder="Ex: Soins d'urgence, Pédiatrie, Gériatrie...">{{ old('competences', auth()->user()->competences) }}</textarea>
                  </div>
                  
                  <div class="input-group-modern full-width">
                    <label class="label-modern">
                      <i class="bi bi-clock"></i>
                      <span>Horaires de service</span>
                    </label>
                    <textarea name="horaires" class="input-modern" rows="3" 
                              placeholder="Ex: Équipe de jour 7h-19h, Garde de nuit disponible">{{ old('horaires', auth()->user()->horaires) }}</textarea>
                  </div>
                </div>
                
                <div class="form-actions-modern">
                  <button type="submit" class="btn-modern btn-nurse-professional">
                    <i class="bi bi-heart-pulse-fill"></i>
                    <span>Sauvegarder le profil professionnel</span>
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
                <p>Protection des données de soins</p>
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
                  <h6><i class="bi bi-info-circle me-2"></i>Sécurité des soins infirmiers</h6>
                  <ul>
                    <li>Protégez l'accès aux données patients sensibles</li>
                    <li>Changez votre mot de passe régulièrement</li>
                    <li>Respectez la confidentialité médicale</li>
                    <li>Déconnectez-vous lors des changements d'équipe</li>
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

        {{-- Tab Avatar --}}
        <div class="tab-content" id="avatar-tab">
          <div class="modern-card">
            <div class="card-header-modern">
              <div class="header-icon avatar">
                <i class="bi bi-camera"></i>
              </div>
              <div class="header-content">
                <h3>Photo de Profil</h3>
                <p>Photo professionnelle de service</p>
              </div>
            </div>
            <div class="card-body-modern">
              @php
                $user = auth()->user();
                $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
                $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
                          ? $rawAvatar : asset(ltrim($rawAvatar, '/'));
              @endphp
              
              <div class="avatar-section">
                <div class="current-avatar">
                  <div class="avatar-display-modern">
                    <div class="avatar-container-modern">
                      <img src="{{ $avatar }}" alt="Photo actuelle" class="avatar-image">
                      <div class="avatar-overlay-modern">
                        <i class="bi bi-camera-fill"></i>
                        <span>Changer</span>
                      </div>
                    </div>
                    <div class="avatar-details">
                      <h6>{{ $user->name }}</h6>
                      <p>Photo visible sur votre badge professionnel</p>
                    </div>
                  </div>
                </div>
                
                <div class="form-actions-modern">
                  <button type="button" class="btn-modern btn-avatar">
                    <i class="bi bi-camera-fill"></i>
                    <span>Gérer la photo</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      
    </div>
  </div>
</div>

<style>
  /* Variables CSS pour infirmier */
  :root {
    --primary-color: #16a34a;
    --primary-light: #16a34a20;
    --primary-hover: #16a34a15;
  }
  
  /* Boutons spécialisés infirmier */
  .btn-modern.btn-nurse-personal {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: white;
    box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
  }
  
  .btn-modern.btn-nurse-personal:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    transform: translateY(-3px);
    color: white;
  }
  
  .btn-modern.btn-nurse-professional {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: white;
    box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
  }
  
  .btn-modern.btn-nurse-professional:hover {
    background: linear-gradient(135deg, #15803d, #166534);
    transform: translateY(-3px);
    color: white;
  }
  
  /* Import styles from patient settings */
  .settings-modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
  }
  
  .settings-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color)dd 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    margin: 2rem 0;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
  }
  
  .hero-text {
    color: white;
  }
  
  .hero-text h1 {
    font-size: 3rem;
    font-weight: 900;
    margin: 0 0 1rem 0;
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
  }
  
  .btn-hero-back {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 1rem 2rem;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .btn-hero-back:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-3px);
  }
  
  /* Navigation moderne */
  .settings-nav {
    margin: 2rem 0;
    position: sticky;
    top: 20px;
    z-index: 100;
  }
  
  .nav-container {
    background: white;
    border-radius: 20px;
    padding: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border: 1px solid #f1f5f9;
    display: flex;
    gap: 0.25rem;
  }
  
  .nav-tab {
    flex: 1;
    background: transparent;
    border: none;
    padding: 1rem 1.5rem;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s;
    color: #4a5568;
    font-weight: 500;
  }
  
  .nav-tab:hover {
    background: #f8fafc;
    color: var(--primary-color);
  }
  
  .nav-tab.active {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }
  
  /* Cards modernes */
  .modern-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .card-header-modern {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 2rem;
    border-bottom: 1px solid #f1f5f9;
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
  }
  
  .header-content h3 {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
  }
  
  .card-body-modern {
    padding: 2.5rem;
  }
  
  /* Formulaires */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .input-group-modern.full-width {
    grid-column: 1 / -1;
  }
  
  .label-modern {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
  }
  
  .input-modern {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s;
    background: white;
  }
  
  .input-modern:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px var(--primary-light);
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
    border-top: 1px solid #f1f5f9;
  }
  
  .btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
  }
  
  .tab-content {
    display: none;
  }
  
  .tab-content.active {
    display: block;
  }
  
  /* Messages */
  .message-modern {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 2px solid #10b981;
  }
  
  .message-icon {
    width: 48px;
    height: 48px;
    background: #10b981;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
  }
  
  /* Avatar */
  .avatar-display-modern {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 20px;
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
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
    transition: all 0.3s;
    color: white;
    cursor: pointer;
  }
  
  .avatar-container-modern:hover .avatar-overlay-modern {
    opacity: 1;
  }
  
  /* Security tips */
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
  
  @media (max-width: 768px) {
    .hero-content {
      flex-direction: column;
      gap: 2rem;
      text-align: center;
    }
    
    .hero-text h1 {
      font-size: 2rem;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
        }
      });
    });
  }
  
  initTabNavigation();
});
</script>
@endsection
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SMART-HEALTH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<style>
  /* ============= VARIABLES DYNAMIQUES ============= */
  :root {
    --primary: #10b981;
    --primary-dark: #059669;
    --primary-light: #34d399;
    --secondary: #3b82f6;
    --accent: #f59e0b;
    --danger: #ef4444;
    --dark: #1f2937;
    --light: #f8fafc;
    --white: #ffffff;
    --border: #e5e7eb;
    
    /* Gradients */
    --gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-secondary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    --gradient-card: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.8) 100%);
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.3);
    
    /* Transitions */
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--gradient-hero);
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
  }

  /* ============= ARRIÈRE-PLAN ANIMÉ ============= */
  
  .animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
  }
  
  .bg-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
  }
  
  .shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    animation: float 20s infinite linear;
    opacity: 0.1;
  }
  
  .shape:nth-child(1) {
    width: 300px;
    height: 300px;
    background: var(--gradient-primary);
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }
  
  .shape:nth-child(2) {
    width: 200px;
    height: 200px;
    background: var(--gradient-secondary);
    top: 60%;
    right: 10%;
    animation-delay: -5s;
  }
  
  .shape:nth-child(3) {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    bottom: 10%;
    left: 20%;
    animation-delay: -10s;
  }
  
  .shape:nth-child(4) {
    width: 180px;
    height: 180px;
    background: linear-gradient(135deg, #ec4899, #be185d);
    top: 20%;
    right: 30%;
    animation-delay: -15s;
  }
  
  @keyframes float {
    0%, 100% {
      transform: translate(0, 0) rotate(0deg);
    }
    25% {
      transform: translate(-20px, -20px) rotate(90deg);
    }
    50% {
      transform: translate(20px, -10px) rotate(180deg);
    }
    75% {
      transform: translate(-10px, 20px) rotate(270deg);
    }
  }
  
  /* ============= PARTICULES FLOTTANTES ============= */
  
  .particles {
    position: absolute;
    width: 100%;
    height: 100%;
    pointer-events: none;
  }
  
  .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    animation: particle-float 15s infinite linear;
  }
  
  @keyframes particle-float {
    0% {
      opacity: 0;
      transform: translateY(100vh) scale(0);
    }
    10% {
      opacity: 1;
    }
    90% {
      opacity: 1;
    }
    100% {
      opacity: 0;
      transform: translateY(-100px) scale(1);
    }
  }
  
  /* ============= LAYOUT PRINCIPAL ============= */
  
  .auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    position: relative;
    z-index: 1;
  }
  
  .auth-card {
    background: var(--gradient-card);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 32px;
    padding: 3rem;
    width: 100%;
    max-width: 480px;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
  }
  
  .auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
  }
  
  .auth-card::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(16,185,129,0.03), transparent, rgba(59,130,246,0.03), transparent);
    animation: rotate 20s linear infinite;
    z-index: -1;
  }
  
  @keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* ============= LOGO DYNAMIQUE ============= */
  
  .logo-container {
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
  }
  
  .logo-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    position: relative;
    display: inline-block;
  }
  
  .logo-bg {
    width: 120px;
    height: 120px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-glow);
    animation: pulse-logo 3s ease-in-out infinite;
    position: relative;
  }
  
  .logo-bg::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #10b981, #3b82f6, #f59e0b, #ec4899);
    border-radius: 50%;
    z-index: -1;
    animation: rainbow-border 4s ease-in-out infinite;
  }
  
  .logo-bg img {
    width: 65px;
    height: 65px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    transition: opacity 0.3s ease;
    max-width: 100%;
    max-height: 100%;
  }
  
  /* S'assurer que l'image est visible */
  .logo-bg img:not([src=""]):not([src="null"]) {
    opacity: 1;
  }
  
  .logo-bg img[src=""],
  .logo-bg img[src="null"] {
    opacity: 0;
  }
  
  /* Fallback au cas où l'image ne charge pas */
  .logo-bg .fallback-icon {
    font-size: 2rem;
    color: white;
    display: none;
    position: relative;
    z-index: 2;
  }
  
  /* Logo SVG personnalisé comme dernier recours */
  .logo-bg .custom-logo {
    width: 65px;
    height: 65px;
    display: none;
  }
  
  .logo-bg .custom-logo svg {
    width: 100%;
    height: 100%;
    fill: white;
  }
  
  .logo-bg img[src=""],
  .logo-bg img:not([src]),
  .logo-bg img[src="null"] {
    display: none;
  }
  
  .logo-bg img[src=""]+.fallback-icon,
  .logo-bg img:not([src])+.fallback-icon,
  .logo-bg img[src="null"]+.fallback-icon {
    display: block;
  }
  
  .pulse-indicator {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    background: var(--danger);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    animation: pulse-indicator 2s ease-in-out infinite;
    box-shadow: var(--shadow-md);
  }
  
  @keyframes pulse-logo {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  @keyframes rainbow-border {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(180deg); }
  }
  
  @keyframes pulse-indicator {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    50% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
  }
  
  .brand-title {
    font-size: 2rem;
    font-weight: 800;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
    animation: shimmer 3s ease-in-out infinite;
  }
  
  @keyframes shimmer {
    0%, 100% { filter: brightness(1); }
    50% { filter: brightness(1.2); }
  }
  
  .brand-subtitle {
    color: #64748b;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0;
  }
  
  /* ============= FORMULAIRES MODERNES ============= */
  
  .form-group {
    margin-bottom: 1.5rem;
    position: relative;
  }
  
  .form-label {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .form-control {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid transparent;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all var(--transition-normal);
    backdrop-filter: blur(10px);
    position: relative;
  }
  
  .form-control:focus {
    background: rgba(255, 255, 255, 0.95);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), var(--shadow-glow);
    outline: none;
    transform: translateY(-1px);
  }
  
  .form-control::placeholder {
    color: #94a3b8;
    opacity: 1;
  }
  
  .input-group {
    position: relative;
  }
  
  .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
  }
  
  .input-group-text {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid transparent;
    border-left: none;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px;
    cursor: pointer;
    transition: all var(--transition-normal);
    backdrop-filter: blur(10px);
  }
  
  .input-group:focus-within .input-group-text {
    background: rgba(255, 255, 255, 0.95);
    border-color: var(--primary);
    color: var(--primary);
  }
  
  /* ============= BOUTONS MODERNES ============= */
  
  .btn {
    border: none;
    border-radius: 16px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
  }
  
  .btn::before {
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
  
  .btn:hover::before {
    width: 300px;
    height: 300px;
  }
  
  .btn-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow-glow);
  }
  
  .btn-primary:hover {
    background: var(--gradient-primary);
    transform: translateY(-2px);
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
    color: white;
  }
  
  .btn-primary:active {
    transform: translateY(0);
  }
  
  /* ============= MESSAGES ============= */
  
  .alert {
    border: none;
    border-radius: 16px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    animation: slideDown 0.5s ease-out;
  }
  
  .alert-success {
    background: rgba(16, 185, 129, 0.1);
    border-left: 4px solid var(--primary);
    color: #065f46;
  }
  
  .alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border-left: 4px solid var(--danger);
    color: #991b1b;
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* ============= CHECKBOX MODERNE ============= */
  
  .form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .form-check-input {
    width: 1.2rem;
    height: 1.2rem;
    border-radius: 6px;
    border: 2px solid #d1d5db;
    transition: all var(--transition-normal);
  }
  
  .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
  }
  
  .form-check-label {
    font-size: 0.875rem;
    color: #64748b;
    cursor: pointer;
  }
  
  /* ============= LIENS ============= */
  
  a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-fast);
  }
  
  a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
  }
  
  /* ============= RESPONSIVE ============= */
  
  @media (max-width: 768px) {
    .auth-card {
      padding: 2rem 1.5rem;
      margin: 1rem;
    }
    
    .logo-icon {
      width: 100px;
      height: 100px;
    }
    
    .logo-bg {
      width: 100px;
      height: 100px;
    }
    
    .logo-bg img {
      width: 55px;
      height: 55px;
    }
    
    .logo-bg .custom-logo {
      width: 55px;
      height: 55px;
    }
    
    .brand-title {
      font-size: 1.5rem;
    }
    
    .form-control {
      padding: 0.875rem 1rem;
    }
  }
  
  /* ============= ANIMATIONS D'APPARITION ============= */
  
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
  
  .fade-in-up {
    animation: fadeInUp 0.6s ease-out;
  }
  
  .fade-in-up.delay-1 {
    animation-delay: 0.1s;
    animation-fill-mode: both;
  }
  
  .fade-in-up.delay-2 {
    animation-delay: 0.2s;
    animation-fill-mode: both;
  }
  
  .fade-in-up.delay-3 {
    animation-delay: 0.3s;
    animation-fill-mode: both;
  }
</style>

<!-- Arrière-plan animé -->
<div class="animated-background">
  <div class="bg-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <div class="particles" id="particles"></div>
</div>

<!-- Contenu principal -->
<div class="auth-container">
  <div class="auth-card fade-in-up">
    <!-- Logo dynamique -->
    <div class="logo-container fade-in-up delay-1">
      <div class="logo-icon">
        <div class="logo-bg">
          <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="SMART-HEALTH" onerror="this.onerror=null; this.src='{{ asset('images/LOGO.png') }}'; if(this.naturalHeight===0) { this.style.display='none'; this.nextElementSibling.style.display='block'; }">
          <div class="custom-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" opacity="0.8"/>
              <path d="M12 6C15.31 6 18 8.69 18 12S15.31 18 12 18 6 15.31 6 12 8.69 6 12 6M12 8C9.79 8 8 9.79 8 12S9.79 16 12 16 16 14.21 16 12 14.21 8 12 8M12 10C13.1 10 14 10.9 14 12S13.1 14 12 14 10 13.1 10 12 10.9 10 12 10Z"/>
              <circle cx="12" cy="12" r="2" opacity="0.6"/>
            </svg>
          </div>
          <i class="bi bi-hospital fallback-icon"></i>
        </div>
        <div class="pulse-indicator">
          <i class="bi bi-heart-pulse-fill"></i>
        </div>
      </div>
      <h1 class="brand-title">SMART-HEALTH</h1>
      <p class="brand-subtitle">Plateforme e-santé moderne</p>
    </div>
    
    <!-- Messages d'alerte -->
    @if(session('success'))
      <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif
    
    @if($errors->any())
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        @foreach($errors->all() as $error)
          {{ $error }}
        @endforeach
      </div>
    @endif
    
    <!-- Formulaire de connexion -->
    <form method="POST" action="{{ url('/login') }}" class="fade-in-up delay-2">
      @csrf
      
      <div class="form-group">
        <label for="email" class="form-label">
          <i class="bi bi-envelope"></i>
          Adresse Email
        </label>
        <input type="email" 
               name="email" 
               id="email" 
               class="form-control" 
               value="{{ old('email') }}" 
               placeholder="votre@email.com" 
               required 
               autocomplete="email">
      </div>
      
      <div class="form-group">
        <label for="password" class="form-label">
          <i class="bi bi-lock"></i>
          Mot de passe
        </label>
        <div class="input-group">
          <input type="password" 
                 name="password" 
                 id="password" 
                 class="form-control" 
                 placeholder="••••••••" 
                 required>
          <span class="input-group-text" id="togglePassword">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </span>
        </div>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" 
                 type="checkbox" 
                 value="1" 
                 id="remember" 
                 name="remember">
          <label class="form-check-label" for="remember">
            Se souvenir de moi
          </label>
        </div>
        <a href="{{ route('password.request') }}">
          Mot de passe oublié ?
        </a>
      </div>
      
      <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Se connecter
      </button>
      
      <div class="text-center">
        <span class="text-muted">Nouveau patient ? </span>
        <a href="{{ route('register') }}">
          Créer un compte
        </a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ============= GESTION DU LOGO ============= //
  
  const logoImg = document.querySelector('.logo-bg img');
  const customLogo = document.querySelector('.custom-logo');
  const fallbackIcon = document.querySelector('.fallback-icon');
  
  function showFallback() {
    if (logoImg) logoImg.style.display = 'none';
    if (customLogo) {
      customLogo.style.display = 'block';
      console.log('Utilisation du logo SVG personnalisé');
    } else if (fallbackIcon) {
      fallbackIcon.style.display = 'block';
      console.log('Utilisation de l\'icône de fallback');
    }
  }
  
  function showMainLogo() {
    if (logoImg) logoImg.style.display = 'block';
    if (customLogo) customLogo.style.display = 'none';
    if (fallbackIcon) fallbackIcon.style.display = 'none';
    console.log('Logo principal chargé avec succès');
  }
  
  if (logoImg) {
    // Gestion du chargement réussi
    logoImg.addEventListener('load', function() {
      if (this.naturalWidth > 0 && this.naturalHeight > 0) {
        showMainLogo();
      } else {
        showFallback();
      }
    });
    
    // Gestion des erreurs de chargement
    logoImg.addEventListener('error', function() {
      console.warn('Erreur de chargement du logo');
      showFallback();
    });
    
    // Vérification initiale après un court délai
    setTimeout(() => {
      if (logoImg.complete) {
        if (logoImg.naturalWidth > 0 && logoImg.naturalHeight > 0) {
          showMainLogo();
        } else {
          showFallback();
        }
      }
    }, 100);
    
    // Vérification de sécurité après 2 secondes
    setTimeout(() => {
      if (logoImg.style.display === 'none' || 
          (logoImg.naturalWidth === 0 && logoImg.naturalHeight === 0)) {
        showFallback();
      }
    }, 2000);
  } else {
    // Si pas d'image du tout, montrer directement le fallback
    showFallback();
  }
  
  // ============= GESTION DU MOT DE PASSE ============= //
  
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');
  
  if (togglePassword && passwordInput && eyeIcon) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Animation de l'icône
      eyeIcon.style.transform = 'scale(0.8)';
      setTimeout(() => {
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
        eyeIcon.style.transform = 'scale(1)';
      }, 100);
    });
  }
  
  // ============= PARTICULES FLOTTANTES ============= //
  
  function createParticle() {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
    particle.style.opacity = Math.random() * 0.5 + 0.2;
    
    const particlesContainer = document.getElementById('particles');
    if (particlesContainer) {
      particlesContainer.appendChild(particle);
      
      // Supprimer la particule après l'animation
      setTimeout(() => {
        if (particle.parentNode) {
          particle.parentNode.removeChild(particle);
        }
      }, 15000);
    }
  }
  
  // Créer des particules à intervalles réguliers
  setInterval(createParticle, 500);
  
  // ============= ANIMATIONS DES INPUTS ============= //
  
  const inputs = document.querySelectorAll('.form-control');
  
  inputs.forEach(input => {
    // Animation au focus
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1), 0 0 20px rgba(16, 185, 129, 0.3)';
    });
    
    // Animation au blur
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'translateY(0)';
      if (!this.matches(':focus')) {
        this.style.boxShadow = '';
      }
    });
    
    // Animation de frappe
    input.addEventListener('input', function() {
      const label = this.parentElement.querySelector('.form-label');
      if (label) {
        label.style.color = this.value ? 'var(--primary)' : 'var(--dark)';
      }
    });
  });
  
  // ============= ANIMATION DU BOUTON ============= //
  
  const submitBtn = document.querySelector('button[type="submit"]');
  
  if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
      // Créer l'effet ripple
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      ripple.classList.add('ripple');
      
      this.appendChild(ripple);
      
      // Supprimer l'effet après l'animation
      setTimeout(() => {
        ripple.remove();
      }, 600);
      
      // Animation de chargement
      setTimeout(() => {
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Connexion...';
        this.disabled = true;
      }, 100);
    });
  }
  
  // ============= EFFET RIPPLE CSS ============= //
  
  const style = document.createElement('style');
  style.textContent = `
    .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.4);
      transform: scale(0);
      animation: ripple-effect 0.6s linear;
      pointer-events: none;
    }
    
    @keyframes ripple-effect {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
  
  // ============= ANIMATIONS AU SCROLL ============= //
  
  window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.5;
    
    const shapes = document.querySelectorAll('.shape');
    shapes.forEach((shape, index) => {
      const speed = (index + 1) * 0.1;
      shape.style.transform = `translate3d(0, ${rate * speed}px, 0)`;
    });
  });
  
  // ============= VALIDATION EN TEMPS RÉEL ============= //
  
  const emailInput = document.getElementById('email');
  
  if (emailInput) {
    emailInput.addEventListener('input', function() {
      const email = this.value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (email && !emailRegex.test(email)) {
        this.style.borderColor = 'var(--danger)';
        this.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
      } else {
        this.style.borderColor = 'transparent';
        this.style.boxShadow = '';
      }
    });
  }
  
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      
      if (password && password.length < 6) {
        this.style.borderColor = 'var(--accent)';
        this.style.boxShadow = '0 0 0 4px rgba(245, 158, 11, 0.1)';
      } else {
        this.style.borderColor = 'transparent';
        this.style.boxShadow = '';
      }
    });
  }
  
  // ============= GESTION DES RACCOURCIS CLAVIER ============= //
  
  document.addEventListener('keydown', function(e) {
    // Enter pour soumettre
    if (e.key === 'Enter' && (e.target.tagName !== 'BUTTON')) {
      const form = document.querySelector('form');
      if (form) {
        form.requestSubmit();
      }
    }
    
    // Escape pour vider les champs
    if (e.key === 'Escape') {
      inputs.forEach(input => input.value = '');
    }
  });
  
  // ============= FOCUS AUTOMATIQUE ============= //
  
  // Focus sur le premier champ au chargement
  setTimeout(() => {
    if (emailInput) {
      emailInput.focus();
    }
  }, 1000);
  
  // ============= GESTION D'ÉTAT DE CONNEXION ============= //
  
  // Sauvegarder l'état de "se souvenir de moi"
  const rememberCheckbox = document.getElementById('remember');
  if (rememberCheckbox) {
    const savedEmail = localStorage.getItem('rememberedEmail');
    if (savedEmail && emailInput) {
      emailInput.value = savedEmail;
      rememberCheckbox.checked = true;
    }
    
    rememberCheckbox.addEventListener('change', function() {
      if (this.checked && emailInput && emailInput.value) {
        localStorage.setItem('rememberedEmail', emailInput.value);
      } else {
        localStorage.removeItem('rememberedEmail');
      }
    });
  }
});
</script>

</body>
</html>

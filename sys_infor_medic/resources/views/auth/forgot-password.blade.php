<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mot de passe oublié - SMART-HEALTH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<style>
  /* ============= VARIABLES CSS ============= */
  
  :root {
    --primary: #10b981;
    --primary-dark: #059669;
    --primary-light: #34d399;
    --secondary: #6366f1;
    --secondary-dark: #4f46e5;
    --accent: #f59e0b;
    --danger: #ef4444;
    --warning: #f59e0b;
    --success: #10b981;
    --info: #3b82f6;
    --dark: #1f2937;
    --light: #f8fafc;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --transition-fast: 0.2s ease;
    --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --border-radius: 16px;
    --border-radius-sm: 8px;
    --border-radius-lg: 24px;
  }
  
  /* ============= RESET ET BASE ============= */
  
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    overflow-x: hidden;
  }
  
  /* ============= ARRIÈRE-PLAN ANIMÉ ============= */
  
  .animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
  }
  
  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  
  .bg-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }
  
  .shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: float 20s infinite linear;
  }
  
  .shape:nth-child(1) {
    width: 300px;
    height: 300px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }
  
  .shape:nth-child(2) {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 10%;
    animation-delay: -7s;
  }
  
  .shape:nth-child(3) {
    width: 200px;
    height: 200px;
    bottom: 10%;
    left: 20%;
    animation-delay: -12s;
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-30px) rotate(120deg); }
    66% { transform: translateY(30px) rotate(240deg); }
  }
  
  .particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
  }
  
  .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    bottom: -10px;
    animation: rise 15s infinite linear;
  }
  
  @keyframes rise {
    to {
      bottom: 110%;
      transform: translateX(100px);
    }
  }
  
  /* ============= CONTENEUR PRINCIPAL ============= */
  
  .auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }
  
  .auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius-lg);
    padding: 3rem;
    box-shadow: var(--shadow-xl);
    width: 100%;
    max-width: 500px;
    position: relative;
    overflow: hidden;
  }
  
  .auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 50%, var(--primary) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
  }
  
  @keyframes shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
  }
  
  /* ============= LOGO ET BRANDING ============= */
  
  .logo-container {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .logo-icon {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
  }
  
  .logo-bg {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
    animation: pulse 2s ease-in-out infinite;
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
    font-size: 2.5rem;
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
  
  .logo-bg img[src=""]+.custom-logo,
  .logo-bg img:not([src])+.custom-logo,
  .logo-bg img[src="null"]+.custom-logo {
    display: block;
  }
  
  .pulse-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 28px;
    height: 28px;
    background: var(--accent);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
    animation: heartbeat 1.5s ease-in-out infinite;
  }
  
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  @keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.1); }
    50% { transform: scale(1); }
    75% { transform: scale(1.05); }
  }
  
  .brand-title {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
  }
  
  .brand-subtitle {
    color: var(--gray-600);
    font-size: 1rem;
    margin-bottom: 1rem;
  }
  
  .page-title {
    color: var(--gray-700);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  
  .page-description {
    color: var(--gray-600);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 2rem;
  }
  
  /* ============= FORMULAIRES ============= */
  
  .form-group {
    margin-bottom: 1.5rem;
    position: relative;
    transition: all var(--transition-smooth);
  }
  
  .form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    transition: color var(--transition-fast);
  }
  
  .form-label i {
    font-size: 1rem;
    color: var(--primary);
  }
  
  .form-control {
    background: rgba(248, 250, 252, 0.8);
    border: 2px solid transparent;
    border-radius: var(--border-radius-sm);
    padding: 0.875rem 1rem;
    font-size: 1rem;
    transition: all var(--transition-smooth);
    backdrop-filter: blur(10px);
  }
  
  .form-control:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), 0 0 20px rgba(16, 185, 129, 0.1);
    transform: translateY(-1px);
  }
  
  .form-control::placeholder {
    color: var(--gray-400);
    transition: color var(--transition-fast);
  }
  
  .form-control:focus::placeholder {
    color: var(--gray-300);
  }
  
  /* ============= BOUTONS ============= */
  
  .btn {
    font-weight: 600;
    padding: 0.875rem 1.5rem;
    border-radius: var(--border-radius-sm);
    border: none;
    transition: all var(--transition-smooth);
    position: relative;
    overflow: hidden;
    text-transform: none;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    color: var(--white);
  }
  
  .btn-primary:active {
    transform: translateY(0);
  }
  
  .btn-link {
    color: var(--gray-600);
    background: transparent;
    box-shadow: none;
    font-weight: 500;
  }
  
  .btn-link:hover {
    color: var(--primary);
    background: rgba(16, 185, 129, 0.05);
    transform: none;
  }
  
  /* ============= ALERTES ============= */
  
  .alert {
    border: none;
    border-radius: var(--border-radius-sm);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border-left: 4px solid var(--success);
  }
  
  .alert-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border-left: 4px solid var(--danger);
  }
  
  .alert ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .alert li {
    margin-bottom: 0.25rem;
  }
  
  .alert li:last-child {
    margin-bottom: 0;
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
</style>

<!-- Arrière-plan animé -->
<div class="animated-background">
  <div class="bg-shapes">
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
          <i class="bi bi-key-fill"></i>
        </div>
      </div>
      <h1 class="brand-title">SMART-HEALTH</h1>
      <p class="brand-subtitle">Plateforme e-santé moderne</p>
      <div class="page-title text-center">Mot de passe oublié</div>
      <p class="page-description text-center">
        Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
      </p>
    </div>
    
    <!-- Messages d'état -->
    @if(session('success'))
      <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif
    
    @if($errors->any())
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i>
        <div>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif
    
    <!-- Formulaire de réinitialisation -->
    <form method="POST" action="{{ route('password.email') }}" class="fade-in-up delay-2">
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
               placeholder="votre@email.com" 
               value="{{ old('email') }}" 
               required 
               autofocus>
      </div>
      
      <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-arrow-clockwise me-2"></i>
        Envoyer le lien de réinitialisation
      </button>
      
      <a href="{{ route('login') }}" class="btn btn-link w-100">
        <i class="bi bi-arrow-left me-2"></i>
        Retour à la connexion
      </a>
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
      
      setTimeout(() => {
        if (particle.parentNode) {
          particle.parentNode.removeChild(particle);
        }
      }, 15000);
    }
  }
  
  setInterval(createParticle, 500);
  
  // ============= ANIMATIONS DES INPUTS ============= //
  
  const inputs = document.querySelectorAll('.form-control');
  
  inputs.forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1), 0 0 20px rgba(16, 185, 129, 0.3)';
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'translateY(0)';
      if (!this.matches(':focus')) {
        this.style.boxShadow = '';
      }
    });
  });
  
  // ============= ANIMATION DU BOUTON ============= //
  
  const submitBtn = document.querySelector('button[type="submit"]');
  
  if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
      setTimeout(() => {
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Envoi en cours...';
        this.disabled = true;
      }, 100);
    });
  }
  
  // Focus automatique
  setTimeout(() => {
    const emailInput = document.getElementById('email');
    if (emailInput) {
      emailInput.focus();
    }
  }, 1000);
});
</script>

</body>
</html>

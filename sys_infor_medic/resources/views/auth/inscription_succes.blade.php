<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription réussie - SMART-HEALTH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

@section('body_class', 'no-navbar')

<style>
  /* ============= VARIABLES DYNAMIQUES ============= */
  :root {
    --primary: #10b981;
    --primary-dark: #059669;
    --primary-light: #34d399;
    --secondary: #3b82f6;
    --accent: #f59e0b;
    --danger: #ef4444;
    --success: #10b981;
    --dark: #1f2937;
    --light: #f8fafc;
    --white: #ffffff;
    --border: #e5e7eb;
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
    
    /* Gradients */
    --gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-secondary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    --gradient-card: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.3);
    --shadow-success: 0 0 30px rgba(16, 185, 129, 0.4);
    
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
  
  /* ============= CONFETTIS DE CÉLÉBRATION ============= */
  
  .confetti {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--success);
    animation: confetti-fall 3s ease-in-out infinite;
  }
  
  .confetti:nth-child(odd) {
    background: var(--accent);
    animation-delay: -1s;
  }
  
  .confetti:nth-child(3n) {
    background: var(--secondary);
    animation-delay: -2s;
  }
  
  @keyframes confetti-fall {
    0% {
      transform: translateY(-100vh) rotate(0deg);
      opacity: 1;
    }
    100% {
      transform: translateY(100vh) rotate(360deg);
      opacity: 0;
    }
  }
  
  /* ============= LAYOUT PRINCIPAL ============= */
  
  .success-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    position: relative;
    z-index: 1;
  }
  
  .success-card {
    background: var(--gradient-card);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 32px;
    padding: 3rem;
    width: 100%;
    max-width: 600px;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
    text-align: center;
  }
  
  .success-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--success) 0%, var(--primary) 50%, var(--success) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
  }
  
  .success-card::after {
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
  
  @keyframes shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
  }
  
  @keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* ============= ICÔNE DE SUCCÈS ============= */
  
  .success-icon-container {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
  }
  
  .success-icon {
    width: 120px;
    height: 120px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 4rem;
    position: relative;
    box-shadow: var(--shadow-success);
    animation: successPulse 2s ease-in-out infinite;
  }
  
  .success-icon::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    border: 2px solid rgba(16, 185, 129, 0.3);
    border-radius: 50%;
    animation: ripple 2s ease-out infinite;
  }
  
  .success-icon::after {
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    right: -20px;
    bottom: -20px;
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 50%;
    animation: ripple 2s ease-out infinite;
    animation-delay: 0.5s;
  }
  
  @keyframes successPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  @keyframes ripple {
    0% {
      opacity: 0;
      transform: scale(0.8);
    }
    50% {
      opacity: 1;
    }
    100% {
      opacity: 0;
      transform: scale(1.2);
    }
  }
  
  /* Checkmark animé */
  .checkmark {
    position: relative;
    animation: checkmark-appear 0.6s ease-out 1s both;
  }
  
  @keyframes checkmark-appear {
    0% {
      opacity: 0;
      transform: scale(0);
    }
    50% {
      transform: scale(1.2);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  /* ============= CONTENU ============= */
  
  .success-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    letter-spacing: -0.025em;
  }
  
  .success-message {
    font-size: 1.2rem;
    color: var(--gray-600);
    margin-bottom: 2rem;
    line-height: 1.6;
  }
  
  /* ============= INFORMATIONS UTILISATEUR ============= */
  
  .credentials-container {
    background: var(--gray-50);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--success);
    text-align: left;
    position: relative;
    overflow: hidden;
  }
  
  .credentials-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.02), rgba(59, 130, 246, 0.02));
    pointer-events: none;
  }
  
  .credentials-title {
    color: var(--dark);
    margin-bottom: 1.5rem;
    font-weight: 700;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .credentials-title i {
    color: var(--success);
    font-size: 1.5rem;
  }
  
  .credential-item {
    background: white;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    border: 1px solid var(--gray-200);
    position: relative;
  }
  
  .credential-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }
  
  .credential-label {
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .credential-value {
    color: var(--dark);
    font-size: 1.1rem;
    font-weight: 500;
    font-family: 'Courier New', monospace;
    background: var(--gray-100);
    padding: 0.5rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .copy-btn {
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    opacity: 0.7;
  }
  
  .copy-btn:hover {
    opacity: 1;
    transform: scale(1.05);
  }
  
  .copy-btn.copied {
    background: var(--success);
  }
  
  /* ============= MESSAGE D'AVERTISSEMENT ============= */
  
  .warning-message {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1));
    border-left: 4px solid var(--accent);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    color: var(--gray-700);
  }
  
  .warning-message i {
    color: var(--accent);
    margin-right: 0.5rem;
  }
  
  /* ============= BOUTONS D'ACTION ============= */
  
  .action-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
  }
  
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
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: 200px;
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
    box-shadow: var(--shadow-success);
    color: white;
  }
  
  .btn-secondary {
    background: rgba(255, 255, 255, 0.8);
    color: var(--gray-700);
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
  }
  
  .btn-secondary:hover {
    background: white;
    color: var(--primary);
    border-color: var(--primary);
    transform: translateY(-1px);
  }
  
  /* ============= FOOTER INFO ============= */
  
  .footer-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-200);
    color: var(--gray-500);
    font-size: 0.9rem;
  }
  
  .footer-info p {
    margin-bottom: 0.5rem;
  }
  
  /* ============= RESPONSIVE ============= */
  
  @media (max-width: 768px) {
    .success-card {
      padding: 2rem 1.5rem;
      margin: 1rem;
    }
    
    .success-icon {
      width: 80px;
      height: 80px;
      font-size: 2.5rem;
    }
    
    .success-title {
      font-size: 2rem;
    }
    
    .success-message {
      font-size: 1.1rem;
    }
    
    .credentials-container {
      padding: 1.5rem;
    }
    
    .credential-item {
      padding: 0.75rem 1rem;
    }
    
    .action-buttons {
      gap: 0.75rem;
    }
    
    .btn {
      padding: 0.75rem 1.5rem;
      min-width: 180px;
      font-size: 0.9rem;
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
  
  @keyframes bounceIn {
    0% {
      opacity: 0;
      transform: scale(0.3);
    }
    50% {
      opacity: 1;
      transform: scale(1.05);
    }
    70% {
      transform: scale(0.95);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  .fade-in-up {
    animation: fadeInUp 0.6s ease-out;
  }
  
  .bounce-in {
    animation: bounceIn 0.8s ease-out;
  }
  
  .fade-in-up.delay-1 {
    animation-delay: 0.2s;
    animation-fill-mode: both;
  }
  
  .fade-in-up.delay-2 {
    animation-delay: 0.4s;
    animation-fill-mode: both;
  }
  
  .fade-in-up.delay-3 {
    animation-delay: 0.6s;
    animation-fill-mode: both;
  }
  
  .fade-in-up.delay-4 {
    animation-delay: 0.8s;
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
<div class="success-container">
  <div class="success-card bounce-in">
    <!-- Icône de succès -->
    <div class="success-icon-container">
      <div class="success-icon">
        <i class="bi bi-check-circle-fill checkmark"></i>
      </div>
    </div>
    
    <!-- Titre principal -->
    <h1 class="success-title fade-in-up delay-1">
      Inscription réussie !
    </h1>
    
    <!-- Message de bienvenue -->
    <p class="success-message fade-in-up delay-2">
      Félicitations ! Votre compte SMART-HEALTH a été créé avec succès. 
      Voici vos informations de connexion :
    </p>
    
    <!-- Informations de connexion -->
    <div class="credentials-container fade-in-up delay-2">
      <h4 class="credentials-title">
        <i class="bi bi-key-fill"></i>
        Vos informations de connexion
      </h4>
      
      <div class="credential-item">
        <div class="credential-label">Numéro de dossier</div>
        <div class="credential-value">
          <span>{{ session('numero_dossier') ?: 'Non disponible' }}</span>
          <button class="copy-btn" onclick="copyToClipboard('{{ session('numero_dossier') }}', this)">
            <i class="bi bi-copy"></i>
          </button>
        </div>
      </div>
      
      <div class="credential-item">
        <div class="credential-label">Email de connexion</div>
        <div class="credential-value">
          <span>{{ session('email') ?: 'Non disponible' }}</span>
          <button class="copy-btn" onclick="copyToClipboard('{{ session('email') }}', this)">
            <i class="bi bi-copy"></i>
          </button>
        </div>
      </div>
      
      <div class="credential-item">
        <div class="credential-label">Mot de passe par défaut</div>
        <div class="credential-value">
          <span id="passwordValue">{{ session('password_defaut') ?: 'Non disponible' }}</span>
          <div>
            <button class="copy-btn" onclick="togglePassword()" style="margin-right: 0.5rem;">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
            <button class="copy-btn" onclick="copyToClipboard('{{ session('password_defaut') }}', this)">
              <i class="bi bi-copy"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Message d'avertissement -->
    <div class="warning-message fade-in-up delay-3">
      <p>
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Important :</strong> Ces informations vous ont été envoyées par email. 
        Veuillez les conserver précieusement et changer votre mot de passe lors de votre première connexion.
      </p>
    </div>
    
    <!-- Boutons d'action -->
    <div class="action-buttons fade-in-up delay-4">
      <a href="{{ route('login') }}" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right"></i>
        Se connecter maintenant
      </a>
      
      <a href="{{ route('register') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Retour à l'inscription
      </a>
    </div>
    
    <!-- Informations de contact -->
    <div class="footer-info fade-in-up delay-4">
      <p>
        <strong>Besoin d'aide ?</strong><br>
        Notre équipe support est disponible pour vous accompagner.
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ============= GESTION DES PARTICULES ============= //
  
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
  
  // ============= CONFETTIS DE CÉLÉBRATION ============= //
  
  function createConfetti() {
    const colors = ['var(--success)', 'var(--accent)', 'var(--secondary)', 'var(--primary)'];
    
    for (let i = 0; i < 30; i++) {
      setTimeout(() => {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 3 + 's';
        confetti.style.animationDuration = Math.random() * 2 + 3 + 's';
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
          confetti.remove();
        }, 6000);
      }, i * 100);
    }
  }
  
  // Lancer les confettis après un délai
  setTimeout(createConfetti, 1000);
  
  // ============= GESTION DU MOT DE PASSE ============= //
  
  let passwordVisible = false;
  const originalPassword = '{{ session("password_defaut") }}';
  
  window.togglePassword = function() {
    const passwordValue = document.getElementById('passwordValue');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordVisible) {
      passwordValue.textContent = '••••••••';
      toggleIcon.className = 'bi bi-eye';
    } else {
      passwordValue.textContent = originalPassword;
      toggleIcon.className = 'bi bi-eye-slash';
    }
    
    passwordVisible = !passwordVisible;
  };
  
  // Masquer le mot de passe par défaut
  const passwordValue = document.getElementById('passwordValue');
  if (passwordValue && originalPassword) {
    passwordValue.textContent = '••••••••';
  }
  
  // ============= FONCTION DE COPIE ============= //
  
  window.copyToClipboard = function(text, button) {
    if (text && text !== 'Non disponible') {
      navigator.clipboard.writeText(text).then(() => {
        // Animation de succès
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i>';
        button.classList.add('copied');
        
        // Feedback visuel temporaire
        setTimeout(() => {
          button.innerHTML = originalContent;
          button.classList.remove('copied');
        }, 2000);
        
        // Toast de succès
        showToast('Copié dans le presse-papiers !', 'success');
      }).catch(err => {
        console.error('Erreur lors de la copie:', err);
        showToast('Erreur lors de la copie', 'error');
      });
    }
  };
  
  // ============= SYSTÈME DE TOAST ============= //
  
  function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? 'var(--gradient-primary)' : 'var(--danger)'};
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      box-shadow: var(--shadow-lg);
      z-index: 9999;
      font-weight: 600;
      animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }
  
  // ============= GESTION DES CLICS ============= //
  
  // Empêcher les clics multiples sur les boutons
  const buttons = document.querySelectorAll('.btn');
  buttons.forEach(button => {
    if (button.tagName === 'A') return; // Skip les liens
    
    button.addEventListener('click', function() {
      this.style.pointerEvents = 'none';
      setTimeout(() => {
        this.style.pointerEvents = 'auto';
      }, 2000);
    });
  });
  
  // ============= EFFET EASTER EGG ============= //
  
  let clickCount = 0;
  const successIcon = document.querySelector('.success-icon');
  
  if (successIcon) {
    successIcon.addEventListener('click', function() {
      clickCount++;
      if (clickCount >= 5) {
        createConfetti();
        clickCount = 0;
        showToast('🎉 Bienvenue dans SMART-HEALTH ! 🎉', 'success');
      }
    });
  }
  
  // ============= RACCOURCIS CLAVIER ============= //
  
  document.addEventListener('keydown', function(e) {
    // Entrée pour aller à la connexion
    if (e.key === 'Enter') {
      window.location.href = "{{ route('login') }}";
    }
    
    // Ctrl+C pour copier l'email
    if (e.ctrlKey && e.key === 'c') {
      const email = '{{ session("email") }}';
      if (email) {
        navigator.clipboard.writeText(email);
        showToast('Email copié !', 'success');
      }
    }
  });
});

// ============= ANIMATIONS CSS DYNAMIQUES ============= //

const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  
  @keyframes fadeOut {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);
</script>

</body>
</html>

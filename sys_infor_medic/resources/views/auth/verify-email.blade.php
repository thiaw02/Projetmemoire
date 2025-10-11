<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vérification d'email - SMART-HEALTH</title>
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
    text-align: center;
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
  
  .pulse-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 28px;
    height: 28px;
    background: var(--info);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
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
  
  .alert-info {
    background: rgba(59, 130, 246, 0.1);
    color: var(--info);
    border-left: 4px solid var(--info);
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
    cursor: pointer;
    min-width: 160px;
    margin: 0.5rem;
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
    text-decoration: none;
  }
</style>

<!-- Arrière-plan animé -->
<div class="animated-background">
  <div class="bg-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
</div>

<!-- Contenu principal -->
<div class="auth-container">
  <div class="auth-card">
    <!-- Logo dynamique -->
    <div class="logo-container">
      <div class="logo-icon">
        <div class="logo-bg">
          <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="SMART-HEALTH" onerror="this.style.display='none'">
          <i class="bi bi-hospital" style="font-size: 2.5rem; color: white;"></i>
        </div>
        <div class="pulse-indicator">
          <i class="bi bi-envelope-check-fill"></i>
        </div>
      </div>
      <h1 class="brand-title">SMART-HEALTH</h1>
      <p class="brand-subtitle">Plateforme e-santé moderne</p>
      <div class="page-title">Vérification d'email</div>
      <p class="page-description">
        Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email 
        en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'email, 
        nous pouvons vous en renvoyer un autre.
      </p>
    </div>
    
    <!-- Messages d'état -->
    @if (session('status') == 'verification-link-sent')
      <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        <div>Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.</div>
      </div>
    @endif
    
    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i>
      <div>Veuillez vérifier votre boîte de réception (y compris les spams) et cliquer sur le lien de vérification.</div>
    </div>
    
    <!-- Actions -->
    <div class="d-flex flex-column align-items-center">
      <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
        @csrf
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-arrow-clockwise"></i>
          Renvoyer l'email de vérification
        </button>
      </form>
      
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-secondary">
          <i class="bi bi-box-arrow-left"></i>
          Se déconnecter
        </button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

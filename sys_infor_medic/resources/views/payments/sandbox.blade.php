<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sandbox Paiement - {{ config('app.name', 'Smart Health') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
<div class="sandbox-container">
  <div class="sandbox-wrapper">
    <div class="sandbox-card">
      {{-- Header moderne --}}
      <div class="sandbox-header">
        <div class="provider-badge">
          <i class="bi bi-gear-fill"></i>
          {{ strtoupper($order->provider ?? 'LOCAL') }}
        </div>
        <h2 class="sandbox-title">
          <i class="bi bi-credit-card-2-front"></i>
          Sandbox Paiement
        </h2>
        <p class="sandbox-subtitle">Mode test - Simulation de paiement</p>
      </div>

      {{-- Détails de la commande --}}
      <div class="sandbox-body">
        <div class="order-info">
          <h4>
            <i class="bi bi-receipt"></i>
            Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
          </h4>
          
          <div class="items-list">
            @foreach($order->items as $it)
              <div class="item">
                <div class="item-details">
                  <i class="bi bi-dot"></i>
                  <span class="item-label">{{ $it->label }}</span>
                </div>
                <div class="item-amount">{{ number_format($it->amount, 0, ',', ' ') }} XOF</div>
              </div>
            @endforeach
          </div>
          
          <div class="total-amount">
            <span>Total à payer</span>
            <span class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</span>
          </div>
        </div>

        {{-- Actions de simulation --}}
        <div class="sandbox-actions">
          <h5><i class="bi bi-play-circle"></i> Actions de test</h5>
          <div class="action-buttons">
            <a href="{{ route('payments.success', ['order' => $order->id]) }}" class="btn btn-success-modern">
              <i class="bi bi-check-circle-fill"></i>
              <span>Simuler succès</span>
              <small>Paiement réussi</small>
            </a>
            <a href="{{ route('payments.cancel', ['order' => $order->id]) }}" class="btn btn-cancel-modern">
              <i class="bi bi-x-circle-fill"></i>
              <span>Simuler échec</span>
              <small>Paiement annulé</small>
            </a>
          </div>
        </div>

        {{-- Info sandbox --}}
        <div class="sandbox-info">
          <div class="info-icon">
            <i class="bi bi-info-circle-fill"></i>
          </div>
          <div class="info-content">
            <strong>Mode SANDBOX activé</strong>
            <p>Cette interface simule les paiements. Configurez vos clés API Wave/Orange Money dans le fichier .env pour passer en mode production.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Styles intégrés -->
<style>
  /* Reset et base */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  html, body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    height: 100%;
    width: 100%;
  }
  
  /* Supprimer tous les styles par défaut */
  h1, h2, h3, h4, h5, h6, p {
    margin: 0;
    padding: 0;
  }
  
  a {
    text-decoration: none;
    color: inherit;
  }
  
  button {
    border: none;
    background: none;
    cursor: pointer;
  }
  
  /* Conteneur principal pour le sandbox */
  .main-sandbox-container {
    width: 100%;
    height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
  }
<style>
  /* Variables pour sandbox */
  :root {
    --sandbox-primary: #0ea5e9;
    --sandbox-success: #10b981;
    --sandbox-warning: #f59e0b;
    --sandbox-danger: #ef4444;
    --sandbox-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --sandbox-card: #ffffff;
    --sandbox-accent: #6366f1;
    --sandbox-glow: rgba(99, 102, 241, 0.3);
  }

  /* Style global pour sandbox */
  body {
    background: var(--sandbox-bg) !important;
    background-attachment: fixed;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
  }

  /* Particules d'arrière-plan animées */
  body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
      radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 40% 40%, rgba(239, 68, 68, 0.1) 0%, transparent 50%);
    z-index: -1;
    animation: particleFloat 20s ease-in-out infinite;
  }

  @keyframes particleFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
  }

  .sandbox-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
    /* Remplacer la position fixed par relative */
    position: relative;
    width: auto;
    height: auto;
  }

  .sandbox-wrapper {
    max-width: 650px;
    width: 100%;
    animation: slideInUp 0.8s ease-out;
  }

  @keyframes slideInUp {
    from {
      opacity: 0;
      transform: translateY(50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .sandbox-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 32px;
    box-shadow: 
      0 32px 64px rgba(0, 0, 0, 0.1),
      0 0 0 1px rgba(255, 255, 255, 0.2),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.2);
    position: relative;
    transition: all 0.3s ease;
  }

  .sandbox-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.7s;
  }

  .sandbox-card:hover::before {
    left: 100%;
  }

  .sandbox-card:hover {
    transform: translateY(-5px);
    box-shadow: 
      0 40px 80px rgba(0, 0, 0, 0.15),
      0 0 0 1px rgba(255, 255, 255, 0.3),
      inset 0 1px 0 rgba(255, 255, 255, 0.4);
  }

  /* Header sandbox */
  .sandbox-header {
    background: linear-gradient(135deg, var(--sandbox-accent) 0%, var(--sandbox-primary) 50%, #0284c7 100%);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .sandbox-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: headerFloat 15s ease-in-out infinite;
  }

  .sandbox-header::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -20%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
    border-radius: 50%;
    animation: headerFloat 18s ease-in-out infinite reverse;
  }

  @keyframes headerFloat {
    0%, 100% { 
      transform: translateY(0px) rotateZ(0deg);
      opacity: 0.3;
    }
    50% { 
      transform: translateY(-30px) rotateZ(180deg);
      opacity: 0.1;
    }
  }

  .provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
      0 8px 32px rgba(0, 0, 0, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 2;
    animation: badgePulse 3s ease-in-out infinite;
  }

  @keyframes badgePulse {
    0%, 100% {
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2),
        0 0 0 rgba(255, 255, 255, 0.3);
    }
    50% {
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.3),
        0 0 20px rgba(255, 255, 255, 0.2);
    }
  }

  .provider-badge i {
    font-size: 1rem;
    animation: badgeIconSpin 4s linear infinite;
  }

  @keyframes badgeIconSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .sandbox-title {
    font-size: 2.25rem;
    font-weight: 900;
    margin: 0 0 0.75rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    position: relative;
    z-index: 2;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    animation: titleGlow 2s ease-in-out infinite alternate;
  }

  @keyframes titleGlow {
    from {
      text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    to {
      text-shadow: 
        0 4px 8px rgba(0, 0, 0, 0.2),
        0 0 20px rgba(255, 255, 255, 0.2);
    }
  }

  .sandbox-title i {
    font-size: 2rem;
    animation: titleIconFloat 3s ease-in-out infinite;
  }

  @keyframes titleIconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
  }

  .sandbox-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
    margin: 0;
  }

  /* Corps sandbox */
  .sandbox-body {
    padding: 2.5rem 2rem;
  }

  .order-info {
    margin-bottom: 2rem;
  }

  .order-info h4 {
    color: #1e293b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .items-list {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
  }

  .item:last-child {
    border-bottom: none;
  }

  .item-details {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .item-label {
    font-weight: 500;
    color: #374151;
  }

  .item-amount {
    font-weight: 600;
    color: var(--sandbox-success);
  }

  .total-amount {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    padding: 1.5rem;
    border-radius: 12px;
    border: 2px solid var(--sandbox-success);
  }

  .total-amount span:first-child {
    font-weight: 600;
    color: #065f46;
  }

  .total-amount .amount {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--sandbox-success);
  }

  /* Actions sandbox */
  .sandbox-actions {
    margin-bottom: 2rem;
  }

  .sandbox-actions h5 {
    color: #1e293b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-success-modern, .btn-cancel-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 2rem 1.5rem;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    font-weight: 700;
    position: relative;
    transform-style: preserve-3d;
    cursor: pointer;
    overflow: hidden;
  }

  .btn-success-modern::before, .btn-cancel-modern::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: 0;
  }

  .btn-success-modern:hover::before, .btn-cancel-modern:hover::before {
    width: 300px;
    height: 300px;
  }

  .btn-success-modern *, .btn-cancel-modern * {
    position: relative;
    z-index: 1;
  }

  .btn-success-modern {
    background: linear-gradient(145deg, var(--sandbox-success), #059669, #047857);
    color: white;
    box-shadow: 
      0 8px 20px rgba(16, 185, 129, 0.3),
      0 2px 4px rgba(0, 0, 0, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
  }

  .btn-success-modern:hover {
    background: linear-gradient(145deg, #059669, #047857, #065f46);
    transform: translateY(-8px) scale(1.05);
    box-shadow: 
      0 20px 40px rgba(16, 185, 129, 0.4),
      0 0 20px rgba(16, 185, 129, 0.2),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
    color: white;
  }

  .btn-success-modern:active {
    transform: translateY(-2px) scale(0.98);
    box-shadow: 
      0 5px 15px rgba(16, 185, 129, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
  }

  .btn-cancel-modern {
    background: linear-gradient(145deg, #ffffff, #f8fafc, #f1f5f9);
    color: var(--sandbox-danger);
    box-shadow: 
      0 8px 20px rgba(239, 68, 68, 0.15),
      0 2px 4px rgba(0, 0, 0, 0.05),
      inset 0 1px 0 rgba(255, 255, 255, 0.8),
      0 0 0 2px rgba(239, 68, 68, 0.2);
  }

  .btn-cancel-modern:hover {
    background: linear-gradient(145deg, var(--sandbox-danger), #dc2626, #b91c1c);
    color: white;
    transform: translateY(-8px) scale(1.05);
    box-shadow: 
      0 20px 40px rgba(239, 68, 68, 0.4),
      0 0 20px rgba(239, 68, 68, 0.2),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
  }

  .btn-cancel-modern:active {
    transform: translateY(-2px) scale(0.98);
    box-shadow: 
      0 5px 15px rgba(239, 68, 68, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
  }

  .btn-success-modern i, .btn-cancel-modern i {
    font-size: 2rem;
    transition: all 0.3s ease;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
  }

  .btn-success-modern:hover i {
    animation: successPulse 0.6s ease;
  }

  .btn-cancel-modern:hover i {
    animation: cancelShake 0.6s ease;
  }

  @keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }

  @keyframes cancelShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
  }

  .btn-success-modern span, .btn-cancel-modern span {
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .btn-success-modern small, .btn-cancel-modern small {
    opacity: 0.9;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
  }

  /* Info sandbox */
  .sandbox-info {
    display: flex;
    gap: 1.5rem;
    background: linear-gradient(135deg, rgba(239, 246, 255, 0.8), rgba(219, 234, 254, 0.8));
    backdrop-filter: blur(10px);
    padding: 2rem;
    border-radius: 20px;
    border: 2px solid rgba(99, 102, 241, 0.2);
    box-shadow: 
      0 8px 32px rgba(99, 102, 241, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
  }

  .sandbox-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--sandbox-primary), var(--sandbox-accent));
    border-radius: 0 2px 2px 0;
  }

  .sandbox-info::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
    border-radius: 50%;
    animation: infoFloat 12s ease-in-out infinite;
  }

  @keyframes infoFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
  }

  .info-icon {
    flex-shrink: 0;
    position: relative;
    z-index: 2;
  }

  .info-icon i {
    font-size: 2rem;
    color: var(--sandbox-primary);
    background: linear-gradient(135deg, var(--sandbox-primary), var(--sandbox-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.2));
    animation: infoIconPulse 2s ease-in-out infinite;
  }

  @keyframes infoIconPulse {
    0%, 100% {
      transform: scale(1);
      filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.2));
    }
    50% {
      transform: scale(1.1);
      filter: drop-shadow(0 4px 8px rgba(99, 102, 241, 0.3));
    }
  }

  .info-content {
    position: relative;
    z-index: 2;
  }

  .info-content strong {
    color: var(--sandbox-accent);
    display: block;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
    font-weight: 800;
    text-shadow: 0 1px 2px rgba(99, 102, 241, 0.1);
  }

  .info-content p {
    color: #4f46e5;
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.6;
    font-weight: 500;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .sandbox-container {
      padding: 1rem;
    }

    .sandbox-header {
      padding: 2rem 1.5rem;
    }

    .sandbox-title {
      font-size: 1.5rem;
    }

    .sandbox-body {
      padding: 2rem 1.5rem;
    }

    .action-buttons {
      grid-template-columns: 1fr;
    }

    .total-amount {
      flex-direction: column;
      gap: 0.5rem;
      text-align: center;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Effet de parallaxe sur les particules d'arrière-plan
  document.addEventListener('mousemove', function(e) {
    const x = e.clientX / window.innerWidth;
    const y = e.clientY / window.innerHeight;
    
    document.body.style.setProperty('--mouse-x', x);
    document.body.style.setProperty('--mouse-y', y);
  });

  // Animation d'apparition en séquence
  const elements = document.querySelectorAll('.order-info, .sandbox-actions, .sandbox-info');
  elements.forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
      el.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, 300 + (index * 200));
  });

  // Effet de click sur les boutons
  const buttons = document.querySelectorAll('.btn-success-modern, .btn-cancel-modern');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      // Créer un effet d'onde
      const ripple = document.createElement('div');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      
      ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: ripple 0.6s linear;
        z-index: 0;
      `;
      
      this.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });

  // Animation de loading avant redirection
  const successBtn = document.querySelector('.btn-success-modern');
  const cancelBtn = document.querySelector('.btn-cancel-modern');
  
  [successBtn, cancelBtn].forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const originalContent = this.innerHTML;
      const isSuccess = this.classList.contains('btn-success-modern');
      
      // Animation de loading
      this.innerHTML = `
        <i class="bi bi-hourglass-split" style="animation: spin 1s linear infinite;"></i>
        <span>${isSuccess ? 'Traitement...' : 'Annulation...'}</span>
      `;
      
      this.style.pointerEvents = 'none';
      this.style.opacity = '0.8';
      
      // Rediriger après l'animation
      setTimeout(() => {
        window.location.href = this.href;
      }, 1500);
    });
  });

  // Effet de particules sur hover des boutons
  buttons.forEach(button => {
    button.addEventListener('mouseenter', function() {
      createParticles(this);
    });
  });

  function createParticles(element) {
    for (let i = 0; i < 6; i++) {
      setTimeout(() => {
        const particle = document.createElement('div');
        particle.style.cssText = `
          position: absolute;
          width: 4px;
          height: 4px;
          background: rgba(255, 255, 255, 0.8);
          border-radius: 50%;
          pointer-events: none;
          z-index: 1000;
        `;
        
        const rect = element.getBoundingClientRect();
        particle.style.left = (rect.left + Math.random() * rect.width) + 'px';
        particle.style.top = (rect.top + Math.random() * rect.height) + 'px';
        
        document.body.appendChild(particle);
        
        const animation = particle.animate([
          { 
            transform: 'translateY(0px) scale(1)', 
            opacity: 1 
          },
          { 
            transform: `translateY(-${50 + Math.random() * 50}px) scale(0)`, 
            opacity: 0 
          }
        ], {
          duration: 1000 + Math.random() * 500,
          easing: 'ease-out'
        });
        
        animation.onfinish = () => particle.remove();
      }, i * 100);
    }
  }
});

// Keyframes CSS pour les nouvelles animations
const additionalStyles = `
  @keyframes ripple {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
</script>

</body>
</html>

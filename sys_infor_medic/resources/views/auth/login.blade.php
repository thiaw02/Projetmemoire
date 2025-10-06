@extends('layouts.app')

@section('content')
<style>
  /* Page sans arrière-plan: override body pour cette page uniquement */
  body { background-color: #ffffff !important; }
  /* Pleine largeur pour cette page */
  body > .container { max-width: 100% !important; padding-left: 0; padding-right: 0; }
  .auth-wrapper { min-height: 100vh; display: flex; flex-direction: column; padding: 0; }
.login-card { max-width: 560px; width: 100%; background: #fff; border: 2px solid #28a745; border-radius: 16px; padding: 1.75rem; box-shadow: 0 16px 40px rgba(39,174,96,.18); }
  .auth-footer { margin-top: auto; }
  .platform-intro h2 { font-size: 2.25rem; font-weight: 800; }
  .platform-intro .text-muted { font-size: 1.125rem; }
  .platform-intro li { font-size: 1rem; }
  .brand { display: flex; align-items: center; gap: .6rem; justify-content: center; margin-bottom: .25rem; }
  .brand img { width: 56px; height: 56px; }
  .brand span { font-weight: 900; color: #27ae60; letter-spacing: .6px; font-size: 1.25rem; }
  .tagline { color: #475467; text-align: center; margin-bottom: .75rem; }
  .features { display: flex; gap: .5rem; flex-wrap: wrap; justify-content: center; margin-bottom: 1rem; }
  .features .badge { background: #f0f9f4; color: #145a32; border: 1px solid #a3d9a5; }
  .divider { display: flex; align-items: center; gap: .75rem; color: #98a2b3; font-size: .9rem; margin: .75rem 0; }
  .divider::before, .divider::after { content: ""; flex: 1 1 auto; height: 1px; background: #e5e7eb; }
  .form-check-label { font-size: .9rem; color: #475467; }
  .muted-links a { color: #2d7a2b; text-decoration: none; }
.muted-links a:hover { text-decoration: underline; }
  .input-group .form-control { border-right: 0; }
  .input-group-text { background: #fff; cursor: pointer; border-left: 0; }

  /* Hero doctor icon */
  .hero-icon { width: 88px; height: 88px; border: 3px solid #28a745; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #28a745; position: relative; box-shadow: 0 4px 16px rgba(39,174,96,.2); }
  .hero-icon img { width: 60px; height: 60px; object-fit: contain; }
  .hero-icon .pulse-badge { position: absolute; right: -6px; bottom: -6px; width: 26px; height: 26px; border-radius: 50%; background: #e53935; color: #fff; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(229,57,53,.35); }
  .hero-icon .pulse-badge i { font-size: 14px; }

  /* Animations */
  .slide-in { animation: slideIn .35s ease forwards; }
  @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container auth-wrapper">
  <!-- Zone principale 2 colonnes plein écran -->
  <div class="row g-0 flex-grow-1 w-100">
    <div class="col-lg-6 d-flex align-items-center p-5 order-2 order-lg-1">
      <div class="w-100 platform-intro" style="max-width: 640px;">
        <div class="text-center mb-3">
          <div class="hero-icon position-relative mx-auto">
            <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
            <span class="pulse-badge"><i class="bi bi-heart-pulse-fill"></i></span>
          </div>
        </div>
        <h2 class="mb-1 text-center">Portail SMART‑HEALTH</h2>
        <div class="text-muted text-center mb-3">Votre accès unifié e‑santé</div>
        <p class="mb-3 text-muted text-center">Accédez à vos outils e‑santé: rendez‑vous, dossier patient, messagerie et plus encore.</p>
        <ul class="list-unstyled text-muted mb-4">
          <li class="mb-1">• Prise de rendez‑vous simplifiée</li>
          <li class="mb-1">• Dossiers patients accessibles</li>
          <li class="mb-1">• Communication sécurisée</li>
        </ul>
        <div class="small text-muted">En vous connectant, vous acceptez nos <a href="#">conditions d'utilisation</a> et notre <a href="#">politique de confidentialité</a>.</div>
      </div>
    </div>

    <div class="col-lg-6 d-flex align-items-center justify-content-center p-5 order-1 order-lg-2 bg-white">
      <div class="w-100" style="max-width: 560px;">
        <!-- Carte CTA visible par défaut -->
        <div id="ctaCard" class="login-card text-center">
          <div class="mb-3">
            <div class="hero-icon position-relative mb-2">
              <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
              <span class="pulse-badge"><i class="bi bi-heart-pulse-fill"></i></span>
            </div>
            <h4 class="mb-1">Accéder à mon espace</h4>
            <div class="text-muted">Connectez-vous pour continuer</div>
          </div>
          <button id="openLogin" class="btn btn-success w-100 btn-lg">Accéder</button>
        </div>

        <!-- Carte formulaire masquée initialement -->
      <div id="loginCard" class="login-card d-none">
          <div class="text-center mb-2">
            <div class="hero-icon position-relative mx-auto">
              <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
              <span class="pulse-badge"><i class="bi bi-heart-pulse-fill"></i></span>
            </div>
          </div>
          <h4 class="mb-1 text-center">SMART-HEALTH</h4>
          <div class="tagline text-center">Plateforme e‑santé moderne</div>

          {{-- Alertes --}}
          @if(session('success'))
            <div class="alert alert-success text-center py-2 mb-3">{{ session('success') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger py-2 mb-3">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Adresse Email</label>
              <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email') }}" placeholder="nom@domaine.com" required>
            </div>
            <div class="mb-2">
              <label for="password" class="form-label">Mot de passe</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="••••••••" required>
                <span class="input-group-text" id="togglePwd" aria-label="Afficher/Masquer"><i class="bi bi-eye"></i></span>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 muted-links">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
              </div>
              <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-success w-100 btn-lg">Se connecter</button>

            <div class="text-center muted-links mt-2">
              <a href="{{ route('register') }}">Nouveau ? Créer un compte patient</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

@section('scripts')
<script>
  (function(){
    const toggle = document.getElementById('togglePwd');
    const pwd = document.getElementById('password');
    toggle?.addEventListener('click', ()=>{
      const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
      pwd.setAttribute('type', type);
      toggle.querySelector('i')?.classList.toggle('bi-eye');
      toggle.querySelector('i')?.classList.toggle('bi-eye-slash');
    });

    // Afficher le formulaire au clic sur Accéder
    const openBtn = document.getElementById('openLogin');
    const cta = document.getElementById('ctaCard');
    const card = document.getElementById('loginCard');
    openBtn?.addEventListener('click', ()=>{
      cta?.classList.add('d-none');
      card?.classList.remove('d-none');
      card?.classList.add('slide-in');
      setTimeout(()=>{ document.getElementById('email')?.focus(); }, 50);
    });
  })();
</script>
@endsection
@endsection

@extends('layouts.app')

@section('content')
<style>
  /* Page sans arrière-plan */
  body { background-color: #ffffff !important; }
  /* Pleine largeur et pleine hauteur */
  body > .container { max-width: 100% !important; padding-left: 0; padding-right: 0; }
  .auth-wrapper { min-height: 100vh; display: flex; flex-direction: column; padding: 0; }
  .register-box { max-width: 1000px; width: 100%; background: #fff; border: 2px solid #28a745; border-radius: 16px; padding: 2rem 3rem; box-shadow: 0 16px 40px rgba(39,174,96,.18); }
  .slide-in { animation: slideIn .35s ease forwards; }
  @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
  .brand { display: flex; align-items: center; gap: .6rem; justify-content: center; margin-bottom: .25rem; }
  .brand img { width: 56px; height: 56px; }
  .brand span { font-weight: 900; color: #27ae60; letter-spacing: .6px; font-size: 1.25rem; }
  .tagline { color: #475467; text-align: center; margin-bottom: 1rem; }
  .form-label { font-weight: 600; }
  .form-control { border-radius: 12px; }
  .alert-danger { background-color: #f8d7da; color: #842029; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #f5c2c7; }
  /* Hero doctor icon */
  .hero-icon { width: 88px; height: 88px; border: 3px solid #28a745; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #28a745; position: relative; box-shadow: 0 4px 16px rgba(39,174,96,.2); }
  .hero-icon img { width: 60px; height: 60px; object-fit: contain; }
  .hero-icon .pulse-badge { position: absolute; right: -6px; bottom: -6px; width: 26px; height: 26px; border-radius: 50%; background: #e53935; color: #fff; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(229,57,53,.35); }
  .hero-icon .pulse-badge i { font-size: 14px; }
.auth-footer { margin-top: auto; }
  .platform-intro h2 { font-size: 2.25rem; font-weight: 800; }
  .platform-intro .text-muted { font-size: 1.125rem; }
</style>

<div class="container auth-wrapper">
  <div class="row g-0 flex-grow-1 w-100">

    <div class="col-12 d-flex align-items-center justify-content-center p-5">
      <div class="w-100" style="max-width: 800px;">

        <!-- Carte formulaire masquée initialement -->
        <div id="registerCard" class="register-box">
        <div class="text-center mb-2">
          <div class="hero-icon position-relative mx-auto">
            <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
            <span class="pulse-badge"><i class="bi bi-heart-pulse-fill"></i></span>
          </div>
        </div>
        <h4 class="text-center text-success mb-1">SMART‑HEALTH</h4>
        <div class="tagline text-center">Rejoignez SMART‑HEALTH : rendez‑vous, dossier patient et suivi simplifiés.</div>
        <div class="text-center text-muted mb-3 mt-1">Inscription Patient</div>

        {{-- Erreurs de validation --}}
        @if($errors->any())
            <div class="alert alert-danger py-2 mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
<input type="text" name="nom" class="form-control form-control-lg" required value="{{ old('nom') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
<input type="text" name="prenom" class="form-control form-control-lg" required value="{{ old('prenom') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
<input type="email" name="email" class="form-control form-control-lg" required value="{{ old('email') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
<input type="text" name="telephone" class="form-control form-control-lg" value="{{ old('telephone') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sexe</label>
<select name="sexe" class="form-control form-control-lg" required>
                        <option value="">-- Choisissez --</option>
                        <option value="Masculin" {{ old('sexe') == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                        <option value="Féminin" {{ old('sexe') == 'Féminin' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de naissance</label>
<input type="date" name="date_naissance" class="form-control form-control-lg" required value="{{ old('date_naissance') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse</label>
<input type="text" name="adresse" class="form-control form-control-lg" value="{{ old('adresse') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Groupe sanguin</label>
<input type="text" name="groupe_sanguin" class="form-control form-control-lg" value="{{ old('groupe_sanguin') }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Antécédents</label>
<textarea name="antecedents" class="form-control form-control-lg" rows="3">{{ old('antecedents') }}</textarea>
                </div>
            </div>

            <input type="hidden" name="role" value="patient">

            <button type="submit" class="btn btn-success w-100 mb-2">S'inscrire</button>
            <div class="text-center">
              <a href="{{ route('login') }}" class="text-decoration-none">Déjà un compte ? Se connecter</a>
            </div>
        </form>
      </div>
    </div>
  </div>

</div>

@endsection

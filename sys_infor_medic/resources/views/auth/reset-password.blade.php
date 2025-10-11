@extends('layouts.app')

@section('content')
<style>
  .reset-box { 
    max-width: 520px; 
    margin: 3rem auto; 
    background: #fff; 
    border: 2px solid #28a745; 
    border-radius: .75rem; 
    padding: 1.5rem; 
    box-shadow: 0 4px 12px rgba(0,0,0,.15); 
  }
  
  .brand-logo {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    position: relative;
  }
  
  .brand-logo img {
    width: 36px;
    height: 36px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
  }
  
  .brand-logo .custom-logo {
    width: 36px;
    height: 36px;
    display: none;
  }
  
  .brand-logo .custom-logo svg {
    width: 100%;
    height: 100%;
    fill: white;
  }
  
  .brand-logo .fallback-icon {
    font-size: 1.5rem;
    color: white;
    display: none;
  }
</style>
<div class="container">
  <div class="reset-box">
    <div class="brand text-center mb-2">
      <div class="brand-logo">
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
      <div class="fw-bold text-success">SMART-HEALTH</div>
    </div>
    <h4 class="text-success mb-3 text-center">Nouveau mot de passe</h4>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="mb-3">
        <label class="form-label">Adresse email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Nouveau mot de passe</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button class="btn btn-success w-100">Réinitialiser</button>
      <a class="btn btn-link w-100 mt-2" href="{{ route('login') }}">Retour à la connexion</a>
    </form>
  </div>
</div>
@endsection

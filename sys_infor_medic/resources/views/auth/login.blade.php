@extends('layouts.app')

@section('content')
<style>
  .auth-wrapper { min-height: calc(100vh - 40px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
  .login-box { max-width: 430px; width: 100%; background: #fff; border: 2px solid #28a745; border-radius: .75rem; padding: 1.75rem; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
  .brand { display: flex; align-items: center; gap: .5rem; justify-content: center; margin-bottom: .75rem; }
  .brand img { width: 48px; height: 48px; }
  .brand span { font-weight: 800; color: #27ae60; letter-spacing: .5px; }
  .form-text-sm { font-size: .9rem; }
</style>

<div class="container auth-wrapper">
    <div class="login-box">
        <div class="brand">
          <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
          <span>SMART-HEALTH</span>
        </div>
        <h2 class="mb-3 text-center text-success">Connexion</h2>

        {{-- Message après inscription --}}
        @if(session('success'))
            <div class="alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- Erreurs de validation --}}
        @if($errors->any())
            <div class="alert-danger">
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
                <input type="email" name="email" id="email" class="form-control" 
                    value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('password.request') }}" class="text-decoration-none">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-success w-100">Se connecter</button>
            <a href="{{ route('register') }}" class="d-block text-center text-primary mt-3">S'inscrire</a>
        </form>
    </div>
</div>
@endsection

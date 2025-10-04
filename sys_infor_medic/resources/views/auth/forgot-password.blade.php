@extends('layouts.app')

@section('content')
<style>
  .forgot-box { max-width: 520px; margin: 3rem auto; background: #fff; border: 2px solid #28a745; border-radius: .75rem; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
</style>
<div class="container">
  <div class="forgot-box">
    <div class="brand text-center mb-2">
      <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo" width="48" height="48">
      <div class="fw-bold text-success">SMART-HEALTH</div>
    </div>
    <h4 class="text-success mb-3 text-center">Réinitialiser le mot de passe</h4>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Adresse email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
      </div>
      <button class="btn btn-success w-100">Envoyer le lien de réinitialisation</button>
      <a class="btn btn-link w-100 mt-2" href="{{ route('login') }}">Retour à la connexion</a>
    </form>
  </div>
</div>
@endsection

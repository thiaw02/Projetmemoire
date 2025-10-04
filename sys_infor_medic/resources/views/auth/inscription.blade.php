@extends('layouts.app')

@section('content')
<style>
  .auth-wrapper { min-height: calc(100vh - 40px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
  .register-box { max-width: 1000px; width: 100%; background: #fff; border: 2px solid #28a745; border-radius: .75rem; padding: 2rem 3rem; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
  .brand { display: flex; align-items: center; gap: .5rem; justify-content: center; margin-bottom: .75rem; }
  .brand img { width: 48px; height: 48px; }
  .brand span { font-weight: 800; color: #27ae60; letter-spacing: .5px; }
  .form-label { font-weight: 600; }
  .form-control { border-radius: 10px; }
  .alert-danger { background-color: #f8d7da; color: #842029; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c2c7; }
</style>

<div class="container auth-wrapper">
    <div class="register-box">
        <div class="brand">
          <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo">
          <span>SMART-HEALTH</span>
        </div>
        <h4 class="text-center text-success mb-3">Inscription Patient</h4>

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

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required value="{{ old('nom') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required value="{{ old('prenom') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sexe</label>
                    <select name="sexe" class="form-control" required>
                        <option value="">-- Choisissez --</option>
                        <option value="Masculin" {{ old('sexe') == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                        <option value="Féminin" {{ old('sexe') == 'Féminin' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control" required value="{{ old('date_naissance') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control" value="{{ old('adresse') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Groupe sanguin</label>
                    <input type="text" name="groupe_sanguin" class="form-control" value="{{ old('groupe_sanguin') }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Antécédents</label>
                    <textarea name="antecedents" class="form-control" rows="2">{{ old('antecedents') }}</textarea>
                </div>
            </div>

            <input type="hidden" name="role" value="patient">

            <button type="submit" class="btn btn-success w-100 mb-3">S'inscrire</button>
            <a href="{{ route('login') }}" class="btn btn-link w-100 text-center">Déjà un compte ? Se connecter</a>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<h2>Ajouter un utilisateur</h2>

<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Retour à la liste</a>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" name="nom" id="nom" class="form-control" required value="{{ old('nom') }}">
    </div>
    <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" name="prenom" id="prenom" class="form-control" required value="{{ old('prenom') }}">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
    </div>
    <div class="mb-3">
        <label for="telephone" class="form-label">Téléphone</label>
        <input type="text" name="telephone" id="telephone" class="form-control" required value="{{ old('telephone') }}">
    </div>
    <div class="mb-3">
        <label for="adresse" class="form-label">Adresse</label>
        <input type="text" name="adresse" id="adresse" class="form-control" required value="{{ old('adresse') }}">
    </div>
    <div class="mb-3">
        <label for="date_naissance" class="form-label">Date de naissance</label>
        <input type="date" name="date_naissance" id="date_naissance" class="form-control" required value="{{ old('date_naissance') }}">
    </div>
    <div class="mb-3">
        <label for="sexe" class="form-label">Sexe</label>
        <select name="sexe" id="sexe" class="form-select" required>
            <option value="">-- Sélectionner --</option>
            <option value="Homme" {{ old('sexe')=='Homme' ? 'selected' : '' }}>Homme</option>
            <option value="Femme" {{ old('sexe')=='Femme' ? 'selected' : '' }}>Femme</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Rôle</label>
        <select name="role" id="role" class="form-select" required>
            <option value="">-- Sélectionner un rôle --</option>
            <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Administrateur</option>
            <option value="medecin" {{ old('role')=='medecin' ? 'selected' : '' }}>Médecin</option>
            <option value="infirmier" {{ old('role')=='infirmier' ? 'selected' : '' }}>Infirmier</option>
            <option value="secretaire" {{ old('role')=='secretaire' ? 'selected' : '' }}>Secrétaire</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="specialite" class="form-label">Spécialité (uniquement pour les médecins)</label>
        <input type="text" name="specialite" id="specialite" class="form-control" value="{{ old('specialite') }}">
    </div>

    {{-- ✅ Le mot de passe sera généré automatiquement, pas besoin de champ --}}

    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>
@endsection

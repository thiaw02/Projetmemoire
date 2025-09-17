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
        <label for="name" class="form-label">Nom</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
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
        <label for="specialite" class="form-label">Spécialité (facultatif)</label>
        <input type="text" name="specialite" id="specialite" class="form-control" value="{{ old('specialite') }}">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>
@endsection

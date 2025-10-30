@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="card shadow-sm border-0" style="border-radius:16px; overflow:hidden;">
  <div class="d-flex justify-content-between align-items-center p-3" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff;">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-person-heart" style="font-size: 1.25rem;"></i>
      <h5 class="mb-0">Ajouter un patient</h5>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">Retour Dashboard</a>
  </div>
  <div class="card-body">

    @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
          </ul>
      </div>
    @endif

    <form action="{{ route('admin.patients.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required value="{{ old('nom') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required value="{{ old('prenom') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Sexe</label>
            <select name="sexe" class="form-select" required>
                <option value="">-- Choisir --</option>
                <option value="Masculin" {{ old('sexe')=='Masculin'?'selected':'' }}>Masculin</option>
                <option value="Féminin" {{ old('sexe')=='Féminin'?'selected':'' }}>Féminin</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" required value="{{ old('date_naissance') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ old('adresse') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Groupe sanguin</label>
            <input type="text" name="groupe_sanguin" class="form-control" value="{{ old('groupe_sanguin') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Antécédents</label>
            <textarea name="antecedents" class="form-control" rows="2">{{ old('antecedents') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Service assigné</label>
            <select name="services[]" class="form-select">
                <option value="">-- Sélectionner un service --</option>
                @foreach(($services ?? []) as $srv)
                    <option value="{{ $srv->id }}" {{ collect(old('services', []))->contains($srv->id) ? 'selected' : '' }}>{{ $srv->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mot de passe (laisser vide pour générer)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Ajouter</button>
    </form>

</div>
</div>
@endsection

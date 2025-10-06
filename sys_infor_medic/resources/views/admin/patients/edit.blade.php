@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Modifier patient</h5>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Retour au dashboard</a>
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

    <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
      @csrf
      @method('PUT')
      @php $p = $patient->patient; @endphp
      <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required value="{{ old('nom', $p->nom ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required value="{{ old('prenom', $p->prenom ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $patient->email) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $p->telephone ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Secrétaire assigné(e)</label>
            <select name="secretary_user_id" class="form-select">
                <option value="">-- Aucune --</option>
                @foreach(($secretaires ?? []) as $sec)
                    @php $assigned = $p->secretary_user_id ?? null; @endphp
                    <option value="{{ $sec->id }}" {{ old('secretary_user_id', $assigned)==$sec->id?'selected':'' }}>{{ $sec->name }} ({{ $sec->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Sexe</label>
            <select name="sexe" class="form-select" required>
                @php $sx = old('sexe', $p->sexe ?? ''); @endphp
                <option value="">-- Choisir --</option>
                <option value="Masculin" {{ $sx=='Masculin'?'selected':'' }}>Masculin</option>
                <option value="Féminin" {{ $sx=='Féminin'?'selected':'' }}>Féminin</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" required value="{{ old('date_naissance', ($p && $p->date_naissance) ? \Carbon\Carbon::parse($p->date_naissance)->format('Y-m-d') : '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $p->adresse ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Groupe sanguin</label>
            <input type="text" name="groupe_sanguin" class="form-control" value="{{ old('groupe_sanguin', $p->groupe_sanguin ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Antécédents</label>
            <textarea name="antecedents" class="form-control" rows="2">{{ old('antecedents', $p->antecedents ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
    </form>

</div>
</div>
@endsection

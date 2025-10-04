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
    <div class="row g-3" id="roleFields">
      <div class="col-md-6 role-medecin d-none">
        <label class="form-label">Spécialité</label>
        <input type="text" name="specialite" class="form-control" value="{{ old('specialite') }}" placeholder="Ex: Cardiologie">
      </div>
      <div class="col-md-6 role-medecin d-none">
        <label class="form-label">Matricule</label>
        <input type="text" name="matricule" class="form-control" value="{{ old('matricule') }}">
      </div>
      <div class="col-md-6 role-medecin d-none">
        <label class="form-label">Cabinet</label>
        <input type="text" name="cabinet" class="form-control" value="{{ old('cabinet') }}">
      </div>
      <div class="col-12 role-medecin d-none">
        <label class="form-label">Horaires</label>
        <textarea name="horaires" class="form-control" rows="2" placeholder="Ex: Lun-Ven 9:00-17:00">{{ old('horaires') }}</textarea>
      </div>
      <div class="col-md-6 role-staff d-none">
        <label class="form-label">Téléphone professionnel</label>
        <input type="text" name="pro_phone" class="form-control" value="{{ old('pro_phone') }}" placeholder="Ex: +221 77 000 00 00">
      </div>
    </div>

    <div class="mb-3 mt-2">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>

@section('scripts')
<script>
  (function(){
    const roleSel = document.getElementById('role');
    function toggleFields(){
      const val = roleSel.value;
      document.querySelectorAll('.role-medecin').forEach(el=> el.classList.toggle('d-none', val!=='medecin'));
      document.querySelectorAll('.role-staff').forEach(el=> el.classList.toggle('d-none', !['secretaire','infirmier','admin'].includes(val)));
    }
    roleSel?.addEventListener('change', toggleFields);
    toggleFields();
  })();
</script>
@endsection

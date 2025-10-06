@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Modifier utilisateur</h5>
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

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-3">
          <label for="name" class="form-label">Nom</label>
          <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $user->name) }}">
      </div>
      <div class="mb-3">
          <label for="email" class="form-label">Adresse email</label>
          <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $user->email) }}">
      </div>
      <div class="mb-3">
          <label for="role" class="form-label">Rôle</label>
          <select name="role" id="role" class="form-select" required>
              <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>Administrateur</option>
              <option value="medecin" {{ (old('role', $user->role) == 'medecin') ? 'selected' : '' }}>Médecin</option>
              <option value="infirmier" {{ (old('role', $user->role) == 'infirmier') ? 'selected' : '' }}>Infirmier</option>
              <option value="secretaire" {{ (old('role', $user->role) == 'secretaire') ? 'selected' : '' }}>Secrétaire</option>
          </select>
      </div>
      <div class="row g-3" id="roleFields">
        <div class="col-md-6 role-medecin d-none">
          <label class="form-label">Spécialité</label>
          <input type="text" name="specialite" class="form-control" value="{{ old('specialite', $user->specialite) }}" placeholder="Ex: Cardiologie">
        </div>
        <div class="col-md-6 role-medecin d-none">
          <label class="form-label">Matricule</label>
          <input type="text" name="matricule" class="form-control" value="{{ old('matricule', $user->matricule) }}">
        </div>
        <div class="col-md-6 role-medecin d-none">
          <label class="form-label">Cabinet</label>
          <input type="text" name="cabinet" class="form-control" value="{{ old('cabinet', $user->cabinet) }}">
        </div>
        <div class="col-12 role-medecin d-none">
          <label class="form-label">Horaires</label>
          <textarea name="horaires" class="form-control" rows="2" placeholder="Ex: Lun-Ven 9:00-17:00">{{ old('horaires', $user->horaires) }}</textarea>
        </div>
        <div class="col-md-6 role-staff d-none">
          <label class="form-label">Téléphone professionnel</label>
          <input type="text" name="pro_phone" class="form-control" value="{{ old('pro_phone', $user->pro_phone) }}" placeholder="Ex: +221 77 000 00 00">
        </div>
      </div>

      <div class="mb-3 mt-2">
          <label for="password" class="form-label">Mot de passe (laisser vide pour ne pas changer)</label>
          <input type="password" name="password" id="password" class="form-control">
      </div>
      <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>

  </div>
</div>
@endsection

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

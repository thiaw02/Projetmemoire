@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0 text-success">Paramètres du compte</h2>
      @php
        $role = $user->role;
        $dash = $role==='admin' ? route('admin.dashboard') : ($role==='secretaire' ? route('secretaire.dashboard') : ($role==='medecin' ? route('medecin.dashboard') : ($role==='infirmier' ? route('infirmier.dashboard') : ($role==='patient' ? route('patient.dashboard') : route('login')))));
      @endphp
      <a href="{{ $dash }}" class="btn btn-outline-secondary btn-sm">Retour au dashboard</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card mb-3">
      <div class="card-header">Informations personnelles</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PATCH')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nom complet</label>
              <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
            </div>
            @if($user->role === 'medecin')
            <div class="col-md-6">
              <label class="form-label">Spécialité</label>
              <input type="text" name="specialite" class="form-control" value="{{ old('specialite', $user->specialite) }}" placeholder="Ex: Cardiologie">
            </div>
            @endif
          </div>
          @if(in_array($user->role, ['secretaire','infirmier']))
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Téléphone professionnel</label>
              <input type="text" name="pro_phone" class="form-control" value="{{ old('pro_phone', $user->pro_phone) }}" placeholder="Ex: +221 77 000 00 00">
            </div>
          </div>
          @endif
          @if($user->role === 'medecin')
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Matricule professionnel</label>
              <input type="text" name="matricule" class="form-control" value="{{ old('matricule', $user->matricule) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cabinet</label>
              <input type="text" name="cabinet" class="form-control" value="{{ old('cabinet', $user->cabinet) }}" placeholder="Ex: Cabinet Médical X">
            </div>
            <div class="col-12">
              <label class="form-label">Horaires</label>
              <textarea name="horaires" class="form-control" rows="2" placeholder="Ex: Lun-Ven 9:00-17:00">{{ old('horaires', $user->horaires) }}</textarea>
            </div>
          </div>
          @endif
          <button class="btn btn-success mt-3">Enregistrer</button>
        </form>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Mot de passe</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.password.update') }}">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Mot de passe actuel</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Nouveau mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Confirmer</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
          </div>
          <button class="btn btn-success mt-3">Mettre à jour</button>
        </form>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Photo de profil</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3 align-items-center">
            <div class="col-md-6">
              <input type="file" name="avatar" accept="image/*" class="form-control" required>
            </div>
            <div class="col-md-6">
              <button class="btn btn-primary">Téléverser</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    @if(($user->role ?? '') === 'patient')
    <div class="card mb-3">
      <div class="card-header">Profil Patient</div>
      <div class="card-body">
        @php $p = $user->patient; @endphp
        <form method="POST" action="{{ route('profile.patient.update') }}">
          @csrf
          @method('PUT')
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
              <label class="form-label">Téléphone</label>
              <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $p->telephone ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Sexe</label>
              @php $sx = old('sexe', $p->sexe ?? ''); @endphp
              <select name="sexe" class="form-select" required>
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
            <div class="col-md-12">
              <label class="form-label">Antécédents</label>
              <textarea name="antecedents" class="form-control" rows="2">{{ old('antecedents', $p->antecedents ?? '') }}</textarea>
            </div>
          </div>
          <button class="btn btn-success mt-3">Enregistrer</button>
        </form>

        <hr class="my-4">
        <h6 class="mb-3">Pièces jointes</h6>
        @if(!empty($documents) && count($documents))
          <ul class="list-group mb-3">
            @foreach($documents as $doc)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ $doc->label ?? 'Document' }} <span class="text-muted">({{ $doc->type ?? 'fichier' }})</span></div>
                  <a href="{{ $doc->file_path }}" target="_blank">Voir / Télécharger</a>
                </div>
                <span class="badge bg-light text-dark">{{ optional($doc->created_at)->format('d/m/Y') }}</span>
                <form method="POST" action="{{ route('profile.patient.document.delete', $doc->id) }}" onsubmit="return confirm('Supprimer ce document ?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger ms-2">Supprimer</button>
                </form>
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-muted mb-2">Aucun document ajouté pour le moment.</div>
        @endif

        <form method="POST" action="{{ route('profile.patient.document.upload') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
          @csrf
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
              <option value="">—</option>
              <option value="identite">Pièce d'identité</option>
              <option value="assurance">Assurance</option>
              <option value="autre">Autre</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Libellé</label>
            <input type="text" name="label" class="form-control" placeholder="Ex: CNI recto">
          </div>
          <div class="col-md-4">
            <label class="form-label">Fichier</label>
            <input type="file" name="file" class="form-control" required>
          </div>
          <div class="col-12">
            <button class="btn btn-outline-primary">Ajouter un document</button>
          </div>
        </form>

      </div>
    </div>
    @endif

    @if(($user->role ?? '') === 'admin')
    <div class="card mb-3">
      <div class="card-header">Paramètres plateforme (Admin)</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nom de la plateforme</label>
              <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? '' }}">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-4">
                <input type="checkbox" name="allow_registrations" value="1" class="form-check-input" id="regChk" {{ (($settings['allow_registrations'] ?? '1') === '1') ? 'checked' : '' }}>
                <label for="regChk" class="form-check-label">Autoriser les inscriptions publiques</label>
              </div>
            </div>
          </div>
          <button class="btn btn-success mt-3">Enregistrer</button>
        </form>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection

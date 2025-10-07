@extends('layouts.app')

@section('content')
<style>
  /* Harmoniser la présentation avec les autres pages (dashboard, médecins, etc.) */
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
  <h4 class="mb-0">Liste des utilisateurs</h4>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
<div class="d-flex justify-content-between align-items-center mb-2">
  <form method="GET" class="d-flex gap-2" role="search" aria-label="Filtrer les utilisateurs">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Recherche (nom/email)" value="{{ $filters['q'] ?? '' }}" style="max-width: 220px;">
    <select name="role" class="form-select form-select-sm" style="max-width: 180px;">
      @php($r = $filters['role'] ?? 'all')
      <option value="all" {{ $r==='all'?'selected':'' }}>Tous rôles</option>
      <option value="admin" {{ $r==='admin'?'selected':'' }}>Admin</option>
      <option value="secretaire" {{ $r==='secretaire'?'selected':'' }}>Secrétaire</option>
      <option value="medecin" {{ $r==='medecin'?'selected':'' }}>Médecin</option>
      <option value="infirmier" {{ $r==='infirmier'?'selected':'' }}>Infirmier</option>
      <option value="patient" {{ $r==='patient'?'selected':'' }}>Patient</option>
    </select>
    <select name="active" class="form-select form-select-sm" style="max-width: 160px;">
      @php($a = $filters['active'] ?? 'all')
      <option value="all" {{ $a==='all'?'selected':'' }}>Tous statuts</option>
      <option value="1" {{ $a==='1'?'selected':'' }}>Actifs</option>
      <option value="0" {{ $a==='0'?'selected':'' }}>Inactifs</option>
    </select>
    <button class="btn btn-outline-secondary btn-sm">Filtrer</button>
  </form>
  <div class="btn-group">
    <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm" title="Ajouter"><i class="bi bi-plus-lg"></i> Ajouter</a>
    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
      <span class="visually-hidden">Actions</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="{{ route('admin.users.export', request()->query()) }}"><i class="bi bi-filetype-csv me-1"></i> Exporter CSV</a></li>
      <li><a class="dropdown-item" href="{{ route('admin.audit.index') }}"><i class="bi bi-clipboard-data me-1"></i> Voir les logs</a></li>
    </ul>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Spécialité</th>
            <th>Actif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->specialite ?? '-' }}</td>
            <td>
              <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                <button class="btn btn-sm {{ $user->active ? 'btn-success' : 'btn-outline-secondary' }}" title="{{ $user->active ? 'Désactiver' : 'Activer' }}">
                  {{ $user->active ? 'Actif' : 'Inactif' }}
                </button>
              </form>
            </td>
            <td>
<div class="d-flex gap-1" role="group" aria-label="Actions utilisateur">
                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
<div class="mt-2">{{ $users->links() }}</div>
@endsection

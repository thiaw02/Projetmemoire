@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
  <div class="d-flex align-items-center gap-2">
    <h4 class="mb-0">Liste des patients</h4>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
  </div>
  <div class="d-flex align-items-center gap-2">
    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-funnel"></i> Filtres
      </button>
      <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 440px;">
        <form method="GET" class="row g-2" role="search" aria-label="Filtrer les patients">
          <div class="col-12">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Recherche (nom/email)" value="{{ request('q') }}">
          </div>
          <div class="col-6">
            <label class="form-label small text-muted mb-1">Statut</label>
            <select name="active" class="form-select form-select-sm">
              @php($a = request('active','all'))
              <option value="all" {{ $a==='all'?'selected':'' }}>Tous statuts</option>
              <option value="1" {{ $a==='1'?'selected':'' }}>Actifs</option>
              <option value="0" {{ $a==='0'?'selected':'' }}>Inactifs</option>
            </select>
          </div>
          <div class="col-12 text-end">
            <button class="btn btn-primary btn-sm"><i class="bi bi-check2"></i> Appliquer</button>
          </div>
        </form>
      </div>
    </div>
    <a href="{{ route('admin.patients.create') }}" class="btn btn-success btn-sm" title="Ajouter"><i class="bi bi-plus-lg"></i> Ajouter</a>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm" title="Listes avancées">Listes avancées</a>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Actif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $patient)
        <tr>
            <td>{{ $patient->name }}</td>
            <td>{{ $patient->email }}</td>
            <td>
              <form method="POST" action="{{ route('admin.users.updateActive', $patient->id) }}" class="mb-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="active" value="{{ $patient->active ? 0 : 1 }}">
                <button class="btn btn-sm btn-outline-secondary">{{ $patient->active ? 'Actif' : 'Inactif' }}</button>
              </form>
            </td>
            <td>
              <div class="d-flex gap-1" role="group" aria-label="Actions patient">
                <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
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
@endsection

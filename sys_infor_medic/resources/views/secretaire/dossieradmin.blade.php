@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Boutons --}}
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> Retour vers le dashboard
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPatientModal">
            <i class="bi bi-person-plus"></i> Ajouter un patient
        </button>
    </div>

    <div class="card shadow p-4">
        <h3 class="text-success mb-3">📁 Dossiers Administratifs</h3>
        <p class="text-muted">Liste de tous les patients.</p>
        
        <table class="table table-bordered table-hover mt-3">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $index => $patient)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $patient->nom }} {{ $patient->prenom }}</td>
                    <td>{{ $patient->email }}</td>
                    <td>{{ $patient->telephone }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewPatientModal{{ $patient->id }}">
                            Voir / Modifier
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun patient enregistré.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Modals des patients --}}
@foreach($patients as $patient)
<div class="modal fade" id="viewPatientModal{{ $patient->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Patient : {{ $patient->nom }} {{ $patient->prenom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.updatePatient', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="{{ $patient->nom }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="{{ $patient->prenom }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $patient->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ $patient->telephone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sexe</label>
                            <select name="sexe" class="form-select">
                                <option value="Homme" {{ $patient->sexe == 'Homme' ? 'selected' : '' }}>Homme</option>
                                <option value="Femme" {{ $patient->sexe == 'Femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="{{ $patient->date_naissance }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secrétaire assigné(e)</label>
                            <select name="secretary_user_id" class="form-select">
                                <option value="">-- Aucune --</option>
                                @foreach(($secretaires ?? []) as $sec)
                                    <option value="{{ $sec->id }}" {{ ($patient->secretary_user_id ?? null) == $sec->id ? 'selected' : '' }}>{{ $sec->name }} ({{ $sec->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ $patient->adresse }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Groupe sanguin</label>
                            <input type="text" name="groupe_sanguin" class="form-control" value="{{ $patient->groupe_sanguin }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Antécédents</label>
                            <input type="text" name="antecedents" class="form-control" value="{{ $patient->antecedents }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Ajouter un patient --}}
<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Ajouter un patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.storePatient') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sexe</label>
                            <select name="sexe" class="form-select">
                                <option value="Homme">Homme</option>
                                <option value="Femme">Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Groupe sanguin</label>
                            <input type="text" name="groupe_sanguin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Antécédents</label>
                            <input type="text" name="antecedents" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

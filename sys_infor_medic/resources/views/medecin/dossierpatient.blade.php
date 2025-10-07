@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🩺 Dossiers Médicaux des Patients</h3>
    <a href="{{ route('medecin.dashboard') }}" class="btn btn-secondary">
        ← Retour au dashboard
    </a>
</div>

    @if($patients->isEmpty())
        <p>Aucun patient pour le moment.</p>
    @else
        <div class="card">
            <div class="card-header bg-primary text-white">
                Liste des patients
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Sexe</th>
                            <th>Date de naissance</th>
                            <th>Téléphone</th>
                            <th>Groupe sanguin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>{{ $patient->nom }}</td>
                                <td>{{ $patient->prenom }}</td>
                                <td>{{ $patient->sexe }}</td>
                                <td>{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</td>
                                <td>{{ $patient->telephone ?? '-' }}</td>
                                <td>{{ $patient->groupe_sanguin ?? '-' }}</td>
<td>
                                    <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn btn-sm btn-primary">
                                        Ouvrir le dossier
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

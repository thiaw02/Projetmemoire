@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">💊 Ordonnances</h3>

    <!-- Bouton Retour -->
    <a href="{{ route('medecin.dashboard') }}" class="btn btn-secondary mb-3">← Retour au Dashboard</a>

    <!-- Bouton pour afficher le formulaire -->
    <button class="btn btn-primary mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#formOrdonnance" aria-expanded="false" aria-controls="formOrdonnance">
        ➕ Ajouter une ordonnance
    </button>

    <!-- Formulaire pour ajouter une ordonnance (collapse) -->
    <div class="collapse" id="formOrdonnance">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Rédiger une ordonnance
            </div>
            <div class="card-body">
                <form action="{{ route('medecin.ordonnances.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-control" required>
                            <option value="">-- Sélectionner un patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="medicaments" class="form-label">Médicaments / Instructions</label>
                        <textarea name="medicaments" id="medicaments" class="form-control" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Ajouter l'ordonnance</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des ordonnances -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            Ordonnances déjà rédigées
        </div>
        <div class="card-body">
            @if($ordonnances->isEmpty())
                <p>Aucune ordonnance pour le moment.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Médicaments / Instructions</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordonnances as $ordonnance)
                            <tr>
                                <td>{{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}</td>
                                <td>{{ $ordonnance->medicaments }}</td>
                                <td>{{ \Carbon\Carbon::parse($ordonnance->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Historique des consultations</h2>

    @if($consultations->isEmpty())
        <p>Aucune consultation enregistrée.</p>
    @else
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Date</th>
                    <th>Symptômes</th>
                    <th>Diagnostic</th>
                    <th>Traitement</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultations as $consultation)
                    <tr>
                        <td>{{ $consultation->patient->nom ?? 'N/A' }}</td>
                        <td>{{ $consultation->medecin->name ?? 'N/A' }}</td>
                        <td>{{ $consultation->date_consultation }}</td>
                        <td>{{ $consultation->symptomes ?? '-' }}</td>
                        <td>{{ $consultation->diagnostic ?? '-' }}</td>
                        <td>{{ $consultation->traitement ?? '-' }}</td>
                        <td>{{ $consultation->statut ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="mt-3">
        <a href="{{ route('infirmier.dashboard') }}" class="btn btn-secondary">⬅ Retour au tableau de bord</a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Liste des Suivis</h1>

    @if($suivis->isEmpty())
        <p class="text-center">Aucun suivi enregistré.</p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Patient ID</th>
                        <th>Température (°C)</th>
                        <th>Tension</th>
                        <th>Date de suivi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suivis as $suivi)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $suivi->patient_id }}</td> <!-- On affiche patient_id directement -->
                            <td>{{ $suivi->temperature }}</td>
                            <td>{{ $suivi->tension }}</td>
                            <td>{{ $suivi->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="text-center mt-4">
    <a href="{{ route('infirmier.dashboard') }}" class="btn btn-secondary">⬅ Retour au tableau de bord</a>
</div>

</div>
@endsection

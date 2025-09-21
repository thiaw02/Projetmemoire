@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Bouton retour --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">Retour</a>
    </div>

    {{-- Bouton Planifier un rendez-vous --}}
    <div class="mb-3">
        <button class="btn btn-success" id="toggleFormBtn">📅 Planifier un rendez-vous</button>
    </div>

    {{-- Formulaire caché --}}
    <div class="card shadow p-4 mb-4" id="rdvForm" style="display:none;">
        <h3 class="text-success mb-4">📄 Nouveau rendez-vous</h3>

        <form action="{{ route('secretaire.rendezvous.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="patient" class="form-label">Patient</label>
                <select name="patient_id" id="patient" class="form-control" required>
                    <option value="">-- Sélectionnez un patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="medecin" class="form-label">Médecin</label>
                <select name="medecin_id" id="medecin" class="form-control" required>
                    <option value="">-- Sélectionnez un médecin --</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">{{ $medecin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="heure" class="form-label">Heure</label>
                <input type="time" name="heure" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif</label>
                <input type="text" name="motif" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Planifier</button>
        </form>
    </div>

    {{-- Liste des rendez-vous --}}
    <div class="card shadow p-4">
        <h3 class="text-primary mb-4">📅 Rendez-vous existants</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rendezvous as $rdv)
                    <tr>
                        <td>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</td>
                        <td>{{ $rdv->medecin->name }}</td>
                        <td>{{ $rdv->date }}</td>
                        <td>{{ $rdv->heure }}</td>
                        <td>{{ $rdv->statut }}</td>
                        <td>
                            <a href="{{ route('secretaire.rendezvous.confirm', $rdv->id) }}" class="btn btn-success btn-sm">✔ Confirmer</a>
                            <a href="{{ route('secretaire.rendezvous.cancel', $rdv->id) }}" class="btn btn-danger btn-sm">✖ Annuler</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.getElementById('toggleFormBtn').addEventListener('click', function() {
        var form = document.getElementById('rdvForm');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    });
</script>
@endsection

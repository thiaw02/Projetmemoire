@extends('layouts.app')

@section('content')
    <h3>Créer un nouveau suivi patient</h3>

    <form action="{{ route('suivi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="patient_id" class="form-label">Patient ID</label>
            <input type="number" name="patient_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="temperature" class="form-label">Température</label>
            <input type="text" name="temperature" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tension" class="form-label">Tension</label>
            <input type="text" name="tension" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection

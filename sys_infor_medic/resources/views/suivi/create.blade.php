@extends('layouts.app')

@section('content')
    <h3>📋 Saisir un suivi patient</h3>

    <form action="{{ route('suivi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Patient</label>
            <select name="patient_id" class="form-control">
                @foreach(App\Models\Patient::all() as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Température (°C)</label>
            <input type="text" name="temperature" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tension</label>
            <input type="text" name="tension" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
@endsection

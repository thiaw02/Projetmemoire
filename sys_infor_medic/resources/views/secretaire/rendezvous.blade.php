@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Bouton retour vers le dashboard --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> <!-- icône optionnelle -->
            Retour vers le dashboard
        </a>
    </div>
<div class="card shadow p-4">
    <h3 class="text-success mb-4">📅 Planification de Rendez-vous</h3>

    <form>
        <div class="mb-3">
            <label for="patient" class="form-label">Nom du Patient</label>
            <input type="text" class="form-control" id="patient" placeholder="Ex : Fatou Sow">
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date du Rendez-vous</label>
            <input type="date" class="form-control" id="date">
        </div>

        <div class="mb-3">
            <label for="heure" class="form-label">Heure</label>
            <input type="time" class="form-control" id="heure">
        </div>

        <button type="submit" class="btn btn-success">Planifier</button>
    </form>
</div>
@endsection

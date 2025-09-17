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
        <h3 class="text-success mb-4">🏥 Gestion des Admissions</h3>

        <form>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom Complet</label>
                <input type="text" class="form-control" id="nom" placeholder="Ex : Mamadou Diop">
            </div>

            <div class="mb-3">
                <label for="date_admission" class="form-label">Date d'Admission</label>
                <input type="date" class="form-control" id="date_admission">
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif d'Admission</label>
                <textarea class="form-control" id="motif" rows="3" placeholder="Ex : Consultation générale..."></textarea>
            </div>

            <button type="submit" class="btn btn-success">Enregistrer l'admission</button>
        </form>
    </div>
</div>
@endsection

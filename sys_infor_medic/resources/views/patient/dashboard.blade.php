@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Déconnexion</button>
    </form>
</div>

<div class="card">
    <div class="card-header text-success">
        Tableau de Bord Médecin
    </div>
    <div class="card-body">
        <p class="mb-4">Bienvenue, Dr. {{ auth()->user()->name }} ! Voici un résumé rapide de votre activité.</p>

        <div class="row">
            <!-- Consultations à venir -->
            <div class="col-md-6 mb-3">
                <div class="card border-success shadow-sm">
                    <div class="card-header bg-success text-white">
                        Consultations à venir
                    </div>
                    <div class="card-body">
                        @if($consultations->isEmpty())
                            <p>Aucune consultation à venir.</p>
                        @else
                            <ul>
                                @foreach($consultations as $rdv)
                                    <li>Patient : {{ $rdv->patient->nom }} - {{ $rdv->date->format('d M Y') }} à {{ $rdv->date->format('H:i') }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dossiers récents consultés -->
            <div class="col-md-6 mb-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Dossiers récents consultés
                    </div>
                    <div class="card-body">
                        @if($dossiersRecents->isEmpty())
                            <p>Aucun dossier consulté récemment.</p>
                        @else
                            <ul>
                                @foreach($dossiersRecents as $patient)
                                    <li>{{ $patient->nom }} - {{ $patient->pathologie_principale ?? 'Non spécifiée' }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-grid gap-2 d-md-block">
            <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-outline-success me-2">📁 Consulter les dossiers</a>
            <a href="{{ route('medecin.consultations') }}" class="btn btn-outline-primary me-2">🩺 Ajouter une consultation</a>
            <a href="{{ route('medecin.ordonnances') }}" class="btn btn-outline-warning me-2">💊 Rédiger une ordonnance</a>
            <a href="#" class="btn btn-outline-info">📝 Créer un acte médical</a>
        </div>
    </div>
</div>
@endsection

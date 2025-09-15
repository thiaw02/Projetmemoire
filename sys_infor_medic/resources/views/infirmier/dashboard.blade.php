@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Déconnexion</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header text-primary">
            Tableau de Bord Infirmier
        </div>
        <div class="card-body">
            <p class="mb-4">
                Bienvenue, {{ Auth::user()->name ?? 'infirmier' }} ! Voici un aperçu de vos activités.
            </p>

            <div class="row">
                <!-- Suivis en cours -->
                <div class="col-md-6 mb-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">
                            Suivis en cours
                        </div>
                        <div class="card-body">
                            <ul>
                                @forelse($suivis as $suivi)
                                    <li>
                                        Patient :
                                        {{ $suivi->patient->nom ?? 'Inconnu' }}
                                        {{ $suivi->patient->prenom ?? '' }}
                                        - Température : {{ $suivi->temperature ?? 'N/A' }}°C
                                        - Tension : {{ $suivi->tension ?? 'N/A' }}
                                    </li>
                                @empty
                                    <li>Aucun suivi en cours</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Dossiers à mettre à jour -->
                <div class="col-md-6 mb-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning text-white">
                            Dossiers à mettre à jour
                        </div>
                        <div class="card-body">
                            <ul>
                                @forelse($dossiers as $dossier)
                                    <li>
                                        {{ $dossier->patient->nom ?? 'Inconnu' }}
                                        {{ $dossier->patient->prenom ?? '' }}
                                        - {{ $dossier->observation ?? 'Observation manquante' }}
                                    </li>
                                @empty
                                    <li>Aucun dossier en attente</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('suivi.create') }}" class="btn btn-outline-info me-2">📋 Saisir un suivi patient</a>
                <a href="{{ route('dossier.index') }}" class="btn btn-outline-warning me-2">📁 Mettre à jour un dossier</a>
                <a href="{{ route('historique.index') }}" class="btn btn-outline-success">🔍 Voir l’historique des soins</a>
            </div>
        </div>
    </div>
@endsection

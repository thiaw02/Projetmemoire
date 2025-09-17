@extends('layouts.app')

@section('content')
    {{-- Bouton Quitter --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('home') }}" class="btn btn-secondary">Quitter</a>
    </div>

    <div class="card">
        <div class="card-header text-primary">
            📊 Tableau de Bord Infirmier
        </div>
        <div class="card-body">
            <p class="mb-4">Bienvenue, {{ Auth::user()->nom ?? 'Infirmier(e)' }} ! Voici un aperçu de vos activités.</p>

            <div class="row">
                {{-- 🔹 Suivis en cours --}}
                <div class="col-md-6 mb-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">
                            🩺 Suivis en cours
                        </div>
                        <div class="card-body">
                            @if($suivisEnCours->isEmpty())
                                <p class="text-muted">Aucun suivi en cours.</p>
                            @else
                                <ul>
                                    @foreach($suivisEnCours as $suivi)
                                        <li>
                                            Patient : {{ $suivi->patient->prenom ?? 'Nom inconnu' }}
                                            {{ $suivi->patient->nom ?? '' }} –
                                            🌡️ Température : {{ $suivi->temperature }}°C –
                                            💓 Tension : {{ $suivi->tension }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 🔹 Dossiers à mettre à jour --}}
                <div class="col-md-6 mb-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning text-white">
                            📁 Dossiers à mettre à jour
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @forelse($dossiersAMettreAJour as $dossier)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $dossier->patient->nom ?? 'Nom inconnu' }}
                                            {{ $dossier->patient->prenom ?? '' }}</strong><br>
                                            Dernière mise à jour :
                                            {{ $dossier->updated_at->format('d/m/Y') }}
                                        </div>
                                        <a href="{{ route('patients.dossier', $dossier->patient_id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            🔍 Voir dossier
                                        </a>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted text-center">
                                        Aucun dossier à mettre à jour.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- 🔹 Boutons dynamiques --}}
            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('suivi.create') }}" class="btn btn-outline-info me-2">
                    📋 Saisir un suivi patient
                </a>

                @if($dossiersAMettreAJour->isNotEmpty())
                    <a href="{{ route('dossier.edit', ['id' => $dossiersAMettreAJour->first()->id]) }}"
                       class="btn btn-outline-warning me-2">
                        📁 Mettre à jour un dossier
                    </a>
                @endif

                <a href="{{ route('historique.index') }}" class="btn btn-outline-success">
                    📖 Voir l’historique des soins
                </a>
            </div>
        </div>
    </div>
@endsection

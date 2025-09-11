@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Déconnexion</button>
    </form>
</div>

<div class="card shadow">
    <div class="card-header text-success fw-bold">
        Tableau de Bord Médecin
    </div>
    <div class="card-body">
        <p class="mb-4">
            Bienvenue, <strong>Dr. {{ $medecin->name ?? 'Utilisateur' }}</strong> ! 
            Voici un résumé rapide de votre activité.
        </p>

        <div class="row">
            <!-- Consultations à venir -->
            <div class="col-md-6 mb-3">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        🩺 Consultations à venir
                    </div>
                    <div class="card-body">
                        @if($consultations->isEmpty())
                            <p class="text-muted">Aucune consultation prévue.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($consultations as $c)
                                    <li>
                                        <strong>Patient :</strong> {{ $c->patient->nom ?? 'N/A' }}  
                                        <span class="text-muted">
                                            - {{ \Carbon\Carbon::parse($c->date_consultation)->format('d/m/Y à H:i') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('medecin.consultations') }}" class="btn btn-sm btn-outline-success mt-2">
                                ➕ Voir toutes les consultations
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dossiers récents -->
            <div class="col-md-6 mb-3">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        📂 Dossiers récents consultés
                    </div>
                    <div class="card-body">
                        @if($dossiers->isEmpty())
                            <p class="text-muted">Aucun dossier consulté récemment.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($dossiers as $d)
                                    <li>
                                        {{ $d->patient->nom ?? 'N/A' }} - 
                                        <em>{{ $d->diagnostic ?? 'N/A' }}</em>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-sm btn-outline-primary mt-2">
                                📁 Voir tous les dossiers
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Patients suivis -->
            <div class="col-md-6 mb-3">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        👤 Patients suivis
                    </div>
                    <div class="card-body">
                        @if($patients->isEmpty())
                            <p class="text-muted">Aucun patient suivi pour le moment.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($patients as $p)
                                    <li>
                                        {{ $p->nom }} {{ $p->prenom }} 
                                        <span class="text-muted"> - Dossier #{{ $p->id }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-sm btn-outline-info mt-2">
                            📂 Accéder aux dossiers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-6 mb-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        🔔 Notifications
                    </div>
                    <div class="card-body">
                        @if($notifications->isEmpty())
                            <p class="text-muted">Aucune notification récente.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($notifications as $n)
                                    <li>
                                        <strong>{{ $n->titre }}</strong> 
                                        <span class="text-muted">- {{ $n->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <!-- Boutons actions -->
        <div class="d-grid gap-2 d-md-block text-center">
            <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-outline-success me-2">
                📁 Consulter les dossiers
            </a>
            <a href="{{ route('medecin.consultations') }}" class="btn btn-outline-primary me-2">
                🩺 Ajouter une consultation
            </a>
            <a href="{{ route('medecin.ordonnances') }}" class="btn btn-outline-warning me-2">
                💊 Rédiger une ordonnance
            </a>
        </div>
    </div>
</div>
@endsection

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
            <p class="mb-4">Bienvenue, Dr. Ndiaye ! Voici un résumé rapide de votre activité.</p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">
                            Consultations à venir
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Patient : Ousmane Sow - 22 juillet à 10h00</li>
                                <li>Patient : Fatou Diop - 22 juillet à 11h30</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white">
                            Dossiers récents consultés
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Awa Ba - Diabète type 2</li>
                                <li>Moussa Gaye - Hypertension</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-block">
                <a href="#" class="btn btn-outline-success me-2">📁 Consulter les dossiers</a>
                <a href="#" class="btn btn-outline-primary me-2">🩺 Ajouter une consultation</a>
                <a href="#" class="btn btn-outline-warning me-2">💊 Rédiger une ordonnance</a>
                <a href="#" class="btn btn-outline-info">📝 Créer un acte médical</a>
            </div>
        </div>
    </div>
@endsection

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
            <p class="mb-4">Bienvenue, Infirmier(e) Diallo ! Voici un aperçu de vos activités.</p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">
                            Suivis en cours
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Patient : Ousmane Sow - Température : 37,8°C - Tension : 12/8</li>
                                <li>Patient : Aïcha Ba - Température : 38,1°C - Tension : 13/9</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning text-white">
                            Dossiers à mettre à jour
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Abdoulaye Ndiaye - Observation manquante</li>
                                <li>Fatou Seck - Analyse en attente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-block">
                <a href="#" class="btn btn-outline-info me-2">📋 Saisir un suivi patient</a>
                <a href="#" class="btn btn-outline-warning me-2">📁 Mettre à jour un dossier</a>
                <a href="#" class="btn btn-outline-success">🔍 Voir l’historique des soins</a>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
<style>
    .home-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
        background: linear-gradient(to bottom right, #f0f8ff, #e0f7fa);
    }

    .home-container h1 {
        font-size: 3rem;
        color: #003366;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .home-container p {
        font-size: 1.25rem;
        color: #555;
        margin-bottom: 2rem;
    }

    .home-buttons a {
        display: inline-block;
        width: 250px;
        padding: 15px 0;
        font-size: 1.2rem;
        margin: 10px;
        border-radius: 10px;
        text-decoration: none;
        color: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .home-buttons a:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }

    .btn-infirmier {
        background-color: #28a745;
    }

    .btn-patient {
        background-color: #007bff;
    }
</style>

<div class="home-container">
    <h1>Bienvenue sur le Système d'Information Médical</h1>
    <p>Veuillez choisir votre accès pour continuer</p>

    <div class="home-buttons">
        <!-- Bouton accès infirmier -->
        <a href="{{ route('infirmier.dashboard') }}" class="btn-infirmier">
            🏥 Accéder au tableau de bord infirmier
        </a>

        <!-- Bouton accès patient -->
        <a href="{{ route('login') }}" class="btn-patient">
            👤 Accéder à l’espace patient
        </a>
    </div>
</div>
@endsection

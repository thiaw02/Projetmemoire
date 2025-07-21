@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');

    .full-height {
        position: relative;
        height: 100vh;
        background-image: url('{{ asset("images/LOGO.png") }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 1000px 1000px;
        background-attachment: fixed;
    }

    .full-height::before {
        content: "";
        position: absolute;
        inset: 0;
        background-color: rgba(255, 255, 255, 0.75);
        z-index: 1;
    }

    .welcome-text {
        position: absolute;
        top: 60px;
        width: 100%;
        text-align: center;
        z-index: 2;
        animation: fadeIn 2s ease-in-out;
    }

    .welcome-text h1 {
        font-size: 3.5rem;
        font-family: 'Poppins', sans-serif;
        color: #002147; /* Bleu marine */
        margin-bottom: 0;
    }

    .welcome-text h2 {
        font-size: 3rem;
        color: #28a745; /* Vert */
        font-family: 'Poppins', sans-serif;
        margin-top: 0.5rem;
    }

    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(-30px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .login-box {
        position: relative;
        z-index: 2;
        max-width: 450px;
        width: 100%;
        background-color: #ffffff;
        border: 2px solid #28a745;
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        transition: transform 0.4s ease;
    }

    .login-box:hover {
        transform: scale(1.02);
    }

    .btn-success {
        background-color: #28a745;
        border: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #218838;
    }
</style>

<div class="container d-flex justify-content-center align-items-center full-height">
    <div class="welcome-text">
        <h1>Bienvenue sur votre plateforme</h1>
        <h2>SMART-HEALTH</h2>
    </div>
    <div class="login-box">
        <h2 class="mb-4 text-center text-success">Connexion</h2>
        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="admin">Administrateur</option>
                    <option value="secretaire">Secrétaire</option>
                    <option value="medecin">Médecin</option>
                    <option value="infirmier">Infirmier</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Se connecter</button>
        </form>
    </div>
</div>
@endsection

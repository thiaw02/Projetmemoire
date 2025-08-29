@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap');

    .full-height {
        position: relative;
        height: 100vh;
        background-image: url('{{ asset("images/LOGO.png") }}');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 900px 900px;
        background-attachment: fixed;
    }

    .full-height::before {
        content: "";
        position: absolute;
        inset: 0;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 1;
    }

    .login-box {
        position: relative;
        z-index: 2;
        max-width: 450px;
        width: 100%;
        background-color: #fff;
        border: 2px solid #28a745;
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .welcome-text {
        position: absolute;
        top: 10%;
        width: 100%;
        z-index: 2;
        text-align: center;
        font-family: 'Raleway', sans-serif;
    }

    .welcome-text h1 {
        font-size: 3rem;
        color: #003366; /* Bleu marine */
        margin-bottom: 0.5rem;
    }

    .welcome-text h2 {
        font-size: 2.5rem;
        color: #28a745; /* Vert */
        margin-top: 0;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        border: 1px solid #f5c2c7;
    }
</style>

<div class="container d-flex justify-content-center align-items-center full-height">
    <div class="welcome-text">
        <h1>BIENVENUE SUR VOTRE PLATEFORME</h1>
        <h2>SMART-HEALTH</h2>
    </div>

    <div class="login-box">
        <h2 class="mb-4 text-center text-success">Connexion</h2>

        {{-- Message après inscription --}}
        @if(session('success'))
            <div class="alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- Erreurs de validation --}}
        @if($errors->any())
            <div class="alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" name="email" id="email" class="form-control" 
                    value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Se connecter</button>
            <a href="{{ route('register') }}" class="d-block text-center text-primary mt-3">S'inscrire</a>
        </form>
    </div>
</div>
@endsection

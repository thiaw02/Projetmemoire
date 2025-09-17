@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="alert alert-success text-center">
        <h3 class="mb-4">Inscription réussie !</h3>
        <p><strong>Votre numéro de dossier :</strong> {{ session('numero_dossier') }}</p>
        <p><strong>Email de connexion :</strong> {{ session('email') }}</p>
        <p><strong>Mot de passe par défaut :</strong> {{ session('password_defaut') }}</p>
        <p>Ces informations vous ont été envoyées par email. Veuillez les conserver précieusement.</p>
        <a href="{{ route('login') }}" class="btn btn-primary mt-3">Se connecter</a>
    </div>
</div>
@endsection

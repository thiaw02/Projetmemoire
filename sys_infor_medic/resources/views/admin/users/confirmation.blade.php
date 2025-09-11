@extends('layouts.app')

@section('content')
<div class="card p-4 shadow-lg">
    <h2 class="text-success">✅ Utilisateur ajouté avec succès</h2>
    <p><strong>Nom :</strong> {{ $user->name }}</p>
    <p><strong>Email :</strong> {{ $user->email }}</p>
    <p><strong>Mot de passe généré :</strong> 
        <span class="badge bg-primary">{{ $password }}</span>
    </p>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Retour au tableau de bord</a>
</div>
@endsection

@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap');

    .full-height {
        position: relative;
        height: 100vh;
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

    .register-box {
        position: relative;
        z-index: 2;
        max-width: 1000px;
        width: 100%;
        background-color: #fff;
        border: 2px solid #28a745;
        border-radius: 0.75rem;
        padding: 2rem 3rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .welcome-text {
        position: absolute;
        top: 2%;
        width: 100%;
        z-index: 2;
        text-align: center;
        font-family: 'Raleway', sans-serif;
    }

    .welcome-text h1 {
        font-size: 3rem;
        color: #003366;
        margin-bottom: 0.5rem;
    }

    .welcome-text h2 {
        font-size: 2.5rem;
        color: #28a745;
        margin-top: 0;
    }

    .form-label {
        font-weight: 600;
    }

    .form-control {
        border-radius: 10px;
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

    <div class="register-box">
        <h4 class="text-center text-success mb-4">Modifier un Patient</h4>

        {{-- Affichage des erreurs --}}
        @if($errors->any())
            <div class="alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.patients.update', $patient->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required value="{{ old('nom', $patient->nom) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required value="{{ old('prenom', $patient->prenom) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', $patient->email) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $patient->telephone) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sexe</label>
                    <select name="sexe" class="form-control" required>
                        <option value="">-- Choisissez --</option>
                        <option value="Homme" {{ old('sexe', $patient->sexe) == 'Homme' ? 'selected' : '' }}>Homme</option>
                        <option value="Femme" {{ old('sexe', $patient->sexe) == 'Femme' ? 'selected' : '' }}>Femme</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control" required value="{{ old('date_naissance', $patient->date_naissance) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $patient->adresse) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Groupe sanguin</label>
                    <input type="text" name="groupe_sanguin" class="form-control" value="{{ old('groupe_sanguin', $patient->groupe_sanguin) }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Antécédents</label>
                    <textarea name="antecedents" class="form-control" rows="2">{{ old('antecedents', $patient->antecedents) }}</textarea>
                </div>

                {{-- ⚠️ Le mot de passe n’est pas obligatoire en modification --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe (laisser vide si inchangé)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-3">Mettre à jour le Patient</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-link w-100 text-center">Retour au dashboard</a>
        </form>
    </div>
</div>
@endsection

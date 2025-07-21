@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Déconnexion</button>
    </form>
</div>

<h2 class="mb-4">Dashboard Administrateur</h2>

{{-- Nav tabs --}}
<ul class="nav nav-tabs" id="adminTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Gérer utilisateurs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">Statistiques globales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="false">Superviser rôles</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="false">Gestion rôles & permissions</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">Historique connexions</button>
    </li>
</ul>

<div class="tab-content mt-3" id="adminTabContent">
    {{-- Gérer utilisateurs --}}
    <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
        <h4>Liste des utilisateurs</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Données simulées --}}
                @php
                    $users = [
                        ['name'=>'Alice Dupont', 'email'=>'alice@example.com', 'role'=>'Médecin'],
                        ['name'=>'Bob Martin', 'email'=>'bob@example.com', 'role'=>'Infirmier'],
                        ['name'=>'Claire Durant', 'email'=>'claire@example.com', 'role'=>'Secrétaire'],
                    ];
                @endphp

                @foreach($users as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['role'] }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-success">Ajouter un utilisateur</button>
    </div>

    {{-- Statistiques globales --}}
    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
        <h4>Statistiques globales</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Répartition des rôles</div>
                    <div class="card-body">
                        <canvas id="rolesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Types de Rendez-vous</div>
                    <div class="card-body">
                        <canvas id="rendezvousChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Superviser rôles --}}
    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
        <h4>Superviser les rôles</h4>
        <ul>
            <li>Secrétaire</li>
            <li>Médecin</li>
            <li>Infirmier</li>
            <li>Patient</li>
        </ul>
        <p>Fonctionnalité à implémenter : modification/assignation des rôles.</p>
    </div>

    {{-- Gestion rôles & permissions --}}
    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
        <h4>Gestion des rôles et permissions</h4>
        <p>Fonctionnalité à venir pour gérer finement les accès par rôle.</p>
    </div>

    {{-- Historique connexions --}}
    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <h4>Historique des connexions</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Date & Heure</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                {{-- Données simulées --}}
                @php
                    $history = [
                        ['user'=>'Alice Dupont', 'datetime'=>'2025-07-21 08:30', 'ip'=>'192.168.0.10'],
                        ['user'=>'Bob Martin', 'datetime'=>'2025-07-21 09:15', 'ip'=>'192.168.0.11'],
                        ['user'=>'Claire Durant', 'datetime'=>'2025-07-21 10:05', 'ip'=>'192.168.0.12'],
                    ];
                @endphp
                @foreach($history as $h)
                <tr>
                    <td>{{ $h['user'] }}</td>
                    <td>{{ $h['datetime'] }}</td>
                    <td>{{ $h['ip'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données simulées chart
    const rolesData = {
        labels: ['Administrateurs', 'Médecins', 'Infirmiers', 'Secrétaires', 'Patients'],
        datasets: [{
            label: 'Nombre d\'utilisateurs',
            data: [3, 8, 5, 4, 20],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545'],
        }]
    };

    const rendezvousData = {
        labels: ['Consultation', 'Suivi', 'Urgence'],
        datasets: [{
            data: [12, 7, 3],
            backgroundColor: ['#20c997', '#fd7e14', '#e83e8c'],
        }]
    };

    new Chart(document.getElementById('rolesChart'), {
        type: 'bar',
        data: rolesData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Utilisateurs par rôle' }
            }
        }
    });

    new Chart(document.getElementById('rendezvousChart'), {
        type: 'doughnut',
        data: rendezvousData,
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Types de rendez-vous' }
            }
        }
    });
</script>
@endsection

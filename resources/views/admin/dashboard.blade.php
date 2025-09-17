@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Déconnexion</button>
    </form>
</div>

<h2 class="mb-4">Dashboard Administrateur</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Nav tabs --}}
<ul class="nav nav-tabs" id="adminTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Gérer utilisateurs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="patients-tab" data-bs-toggle="tab" data-bs-target="#patients" type="button" role="tab" aria-controls="patients" aria-selected="false">Gérer patients</button>
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

        <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">Ajouter un utilisateur</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Spécialité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @if($user->role !== 'patient')
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->specialite ?? '-' }}</td>
                        <td>
                            <a href="/admin/users/edit.php?id={{ $user->id }}" class="btn btn-sm btn-primary">Modifier</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- Gérer patients --}}
    <div class="tab-pane fade" id="patients" role="tabpanel" aria-labelledby="patients-tab">
        <h4>Liste des patients</h4>

        <a href="{{ route('admin.patients.create') }}" class="btn btn-success mb-3">Ajouter un patient</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @if($user->role === 'patient')
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="/admin/patients/edit.php?id={{ $user->id }}" class="btn btn-sm btn-primary">Modifier</a>
                            <form action="{{ route('admin.patients.destroy', $user->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce patient ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
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

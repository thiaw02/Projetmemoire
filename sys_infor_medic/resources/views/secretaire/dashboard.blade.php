@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Déconnexion --}}
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Déconnexion</button>
        </form>
    </div>

    {{-- Titre --}}
    <h2 class="mb-4 text-success fw-bold">Tableau de bord - Secrétaire</h2>

    {{-- Cartes de navigation --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title fw-semibold text-success">📁 Dossiers administratifs</h5>
                    <p class="card-text text-muted">Consultez et gérez les dossiers patients.</p>
                    <a href="{{ url('/secretaire/dossieradmin') }}" class="btn btn-success w-75">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title fw-semibold text-success">📅 Rendez-vous</h5>
                    <p class="card-text text-muted">Planifiez les rendez-vous des patients.</p>
                    <a href="{{ url('/secretaire/rendezvous') }}" class="btn btn-success w-75">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title fw-semibold text-success">🏥 Admissions</h5>
                    <p class="card-text text-muted">Gérez les admissions des patients.</p>
                    <a href="{{ url('/secretaire/admissions') }}" class="btn btn-success w-75">Accéder</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row mt-5 g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-semibold">📊 Statistiques des Rendez-vous</div>
                <div class="card-body">
                    <canvas id="rendezvousChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-semibold">📈 Admissions par mois</div>
                <div class="card-body">
                    <canvas id="admissionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des rendez-vous
    new Chart(document.getElementById('rendezvousChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Rendez-vous',
                data: [20, 25, 18, 30, 28, 35],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: false }
            }
        }
    });

    // Graphique des admissions
    new Chart(document.getElementById('admissionsChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Admissions',
                data: [10, 15, 8, 20, 18, 22],
                backgroundColor: '#20c997',
                borderRadius: 5,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection

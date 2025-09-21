@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Déconnexion</button>
        </form>
    </div>

    <h2 class="mb-4 text-success fw-bold">Tableau de bord - Secrétaire</h2>

    <div class="mb-4">
        <a href="{{ route('secretaire.dossiersAdmin') }}" class="btn btn-outline-success me-2">📁 Dossiers administratifs</a>
        <a href="{{ route('secretaire.rendezvous') }}" class="btn btn-outline-primary me-2">📅 Rendez-vous</a>
        <a href="{{ route('secretaire.admissions') }}" class="btn btn-outline-warning me-2">🏥 Admissions</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-semibold">📊 Rendez-vous des 6 derniers mois</div>
                <div class="card-body">
                    <canvas id="rendezvousChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white fw-semibold">📈 Admissions des 6 derniers mois</div>
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
document.addEventListener("DOMContentLoaded", function() {
   
    // Graphique Rendez-vous
    new Chart(document.getElementById('rendezvousChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Rendez-vous',
                data: rendezvousData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' }, title: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Graphique Admissions
    new Chart(document.getElementById('admissionsChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Admissions',
                data: admissionsData,
                backgroundColor: '#ffc107',
                borderRadius: 5,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endsection

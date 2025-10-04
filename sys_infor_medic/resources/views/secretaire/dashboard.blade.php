@extends('layouts.app')

@section('content')
<style>
  /* Largeur confortable et sidebar compacte */
  body > .container { max-width: 1500px !important; }
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  /* Boutons d'accès rapides */
  .quick-actions .btn { min-width: 230px; }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

    <div class="admin-header d-flex align-items-center justify-content-between mb-3">
      <h2 class="mb-0 text-success fw-bold">Tableau de bord - Secrétaire</h2>
      @php
        $unread = \App\Models\Message::whereNull('read_at')->whereHas('conversation', function($q){ $uid = auth()->id(); $q->where('user_one_id',$uid)->orWhere('user_two_id',$uid);})->where('sender_id','!=',auth()->id())->count();
      @endphp
      <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm me-2 position-relative">
        <i class="bi bi-bell"></i>
        @if($unread>0)
          <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unread>0 ? '' : 'd-none' }}">{{ $unread }}</span>
        @endif
      </a>
      <form action="{{ route('logout') }}" method="POST" class="ms-1">
        @csrf
        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</button>
      </form>
    </div>

    <div class="mb-4 quick-actions d-flex flex-wrap gap-2">
        <a href="{{ route('secretaire.dossiersAdmin') }}" class="btn btn-outline-success"><i class="bi bi-folder2-open me-1"></i> Dossiers administratifs</a>
        <a href="{{ route('secretaire.rendezvous') }}" class="btn btn-outline-primary"><i class="bi bi-calendar2-week me-1"></i> Rendez-vous</a>
        <a href="{{ route('secretaire.admissions') }}" class="btn btn-outline-warning"><i class="bi bi-hospital me-1"></i> Admissions</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-semibold"><i class="bi bi-graph-up me-1"></i> Rendez-vous des 6 derniers mois</div>
                <div class="card-body">
                    <canvas id="rendezvousChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white fw-semibold"><i class="bi bi-bar-chart-line me-1"></i> Admissions des 6 derniers mois</div>
                <div class="card-body">
                    <canvas id="admissionsChart" height="200"></canvas>
                </div>
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

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
    </div>

    <div class="mb-4 quick-actions d-flex flex-wrap gap-2">
        <a href="{{ route('secretaire.dossiersAdmin') }}" class="btn btn-outline-success"><i class="bi bi-folder2-open me-1"></i> Dossiers administratifs</a>
        <a href="{{ route('secretaire.rendezvous') }}" class="btn btn-outline-primary"><i class="bi bi-calendar2-week me-1"></i> Rendez-vous</a>
        <a href="{{ route('secretaire.admissions') }}" class="btn btn-outline-warning"><i class="bi bi-hospital me-1"></i> Admissions</a>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <div class="text-muted small">RDV demandés</div>
            <div class="display-6">{{ $pendingRdvCount }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <div class="text-muted small">Patients à traiter</div>
            <div class="display-6">{{ $patientsATraiterCount }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <div class="text-muted small">Patients (total)</div>
            <div class="display-6">{{ $totalPatients }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h5 class="mb-0">Statistiques</h5>
      <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-secondary" data-sec-window="2">2 mois</button>
        <button type="button" class="btn btn-outline-secondary" data-sec-window="6">6 mois</button>
        <button type="button" class="btn btn-outline-secondary active" data-sec-window="12">12 mois (1 an)</button>
      </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-semibold"><i class="bi bi-graph-up me-1"></i> Rendez-vous (<span id="secWindowLabelRdv">12 derniers mois</span>)</div>
                <div class="card-body">
                    <canvas id="rendezvousChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white fw-semibold"><i class="bi bi-bar-chart-line me-1"></i> Admissions (<span id="secWindowLabelAdm">12 derniers mois</span>)</div>
                <div class="card-body">
                    <canvas id="admissionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des demandes de rendez-vous -->
    <div class="row g-4 mt-3">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-light fw-semibold"><i class="bi bi-list-check me-1"></i> Demandes de rendez-vous</div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Motif</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pendingRdvList as $rdv)
                    <tr>
                      <td>{{ $rdv->patient->nom ?? ($rdv->patient->user->name ?? '—') }} {{ $rdv->patient->prenom ?? '' }}</td>
                      <td>{{ $rdv->medecin->name ?? '—' }}</td>
                      <td>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
                      <td>{{ $rdv->heure }}</td>
                      <td>{{ $rdv->motif ?? '—' }}</td>
                      <td class="d-flex gap-1">
                        <a href="{{ route('secretaire.rendezvous.confirm', $rdv->id) }}" class="btn btn-sm btn-success">Confirmer</a>
                        <a href="{{ route('secretaire.rendezvous.cancel', $rdv->id) }}" class="btn btn-sm btn-outline-secondary">Annuler</a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted">Aucune demande en attente</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
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
   
    // Injecte les données depuis PHP (12 mois max)
    const months = @json($months);
    const rendezvousDataFull = @json($rendezvousData);
    const admissionsDataFull = @json($admissionsData);

    function lastN(arr, n){ return (arr || []).slice(Math.max((arr || []).length - n, 0)); }
    function labelText(n){ if(n===2) return '2 derniers mois'; if(n===6) return '6 derniers mois'; return '12 derniers mois'; }

    const rdvCtx = document.getElementById('rendezvousChart').getContext('2d');
    const admCtx = document.getElementById('admissionsChart').getContext('2d');

    let windowN = 12;

    function buildRdvConfig(n){
        return {
            type: 'line',
            data: {
                labels: lastN(months, n),
                datasets: [{
                    label: 'Rendez-vous',
                    data: lastN(rendezvousDataFull, n),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' }, title: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        };
    }

    function buildAdmConfig(n){
        return {
            type: 'bar',
            data: {
                labels: lastN(months, n),
                datasets: [{
                    label: 'Admissions',
                    data: lastN(admissionsDataFull, n),
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
        };
    }

    let rdvChart = new Chart(rdvCtx, buildRdvConfig(windowN));
    let admChart = new Chart(admCtx, buildAdmConfig(windowN));

    function setWindow(n){
        windowN = n;
        document.getElementById('secWindowLabelRdv').textContent = labelText(n);
        document.getElementById('secWindowLabelAdm').textContent = labelText(n);
        rdvChart.destroy();
        admChart.destroy();
        rdvChart = new Chart(rdvCtx, buildRdvConfig(n));
        admChart = new Chart(admCtx, buildAdmConfig(n));
        document.querySelectorAll('[data-sec-window]').forEach(b=>b.classList.toggle('active', parseInt(b.dataset.secWindow,10)===n));
    }

    document.querySelectorAll('[data-sec-window]').forEach(btn=>{
        btn.addEventListener('click', ()=> setWindow(parseInt(btn.dataset.secWindow,10)) );
    });
});
</script>
@endsection

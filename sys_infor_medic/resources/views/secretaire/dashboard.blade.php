@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

{{-- Header moderne pour secrétaire --}}
<div class="secretary-modern-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-person-workspace"></i>
      <span>Secrétariat</span>
    </div>
    <div class="header-badge">
      <i class="bi bi-shield-check"></i>
      <span>{{ Auth::user()->name }}</span>
    </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Onglets modernes pour secrétaire --}}
<div class="tab-scroll">
<ul class="nav nav-tabs secretary-tabs flex-nowrap" id="secretaireTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab"><i class="bi bi-speedometer2 me-1"></i> Vue d'ensemble</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab"><i class="bi bi-wallet2 me-1"></i> Paiements</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="quick-actions-tab" data-bs-toggle="tab" data-bs-target="#quick-actions" type="button" role="tab"><i class="bi bi-lightning me-1"></i> Actions rapides</button>
    </li>
</ul>
</div>

    <div class="tab-content" id="secretaireTabContent">

        {{-- Vue d'ensemble --}}
        <div class="tab-pane fade show active" id="overview" role="tabpanel">

            {{-- KPIs modernes pour secrétaire --}}
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="kpi-card">
                  <div class="kpi-icon rdv-pending">
                    <i class="bi bi-clock-history"></i>
                  </div>
                  <div class="kpi-content">
                    <div class="kpi-value">{{ $pendingRdvCount }}</div>
                    <div class="kpi-label">RDV demandés</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="kpi-card">
                  <div class="kpi-icon patients-waiting">
                    <i class="bi bi-person-plus"></i>
                  </div>
                  <div class="kpi-content">
                    <div class="kpi-value">{{ $patientsATraiterCount }}</div>
                    <div class="kpi-label">Patients à traiter</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="kpi-card">
                  <div class="kpi-icon patients-total">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div class="kpi-content">
                    <div class="kpi-value">{{ $totalPatients }}</div>
                    <div class="kpi-label">Patients (total)</div>
                  </div>
                </div>
              </div>
            </div>

    <!-- Statistiques ---->
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

    {{-- Liste des demandes de rendez-vous avec style moderne --}}
    <div class="row g-4 mt-3">
      <div class="col-12">
        <div class="table-container">
          <div class="table-header">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Demandes de rendez-vous</h5>
          </div>
          <div class="table-responsive">
            <table class="table secretary-table">
              <thead>
                <tr>
                  <th><i class="bi bi-person me-1"></i>Patient</th>
                  <th><i class="bi bi-person-badge me-1"></i>Médecin</th>
                  <th><i class="bi bi-calendar me-1"></i>Date</th>
                  <th><i class="bi bi-clock me-1"></i>Heure</th>
                  <th><i class="bi bi-chat-text me-1"></i>Motif</th>
                  <th><i class="bi bi-gear me-1"></i>Actions</th>
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
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('secretaire.rendezvous.confirm', $rdv->id) }}" class="btn btn-sm btn-success">Confirmer</a>
                        <a href="{{ route('secretaire.rendezvous.cancel', $rdv->id) }}" class="btn btn-sm btn-outline-secondary">Annuler</a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Aucune demande en attente</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
        </div> {{-- Fin onglet overview --}}

        {{-- Onglet Paiements --}}
        <div class="tab-pane fade" id="payments" role="tabpanel">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-icon payments-month">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value">{{ number_format(($totalPaymentsThisMonth ?? 0) / 1000, 0) }}K</div>
                            <div class="kpi-label">Paiements ce mois</div>
                            <div class="kpi-sub">XOF</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-icon payments-pending">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value">{{ $pendingPayments ?? 0 }}</div>
                            <div class="kpi-label">En attente</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-icon payments-total">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value">{{ ($recentOrders ?? collect())->count() }}</div>
                            <div class="kpi-label">Total transactions</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Paiements récents</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-wallet2 me-1"></i> Gérer les paiements</a>
                    <a href="{{ route('secretaire.payments.export.csv') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i> Export CSV</a>
                </div>
            </div>

            <div class="table-container">
                <table class="table secretary-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-calendar me-1"></i>Date</th>
                            <th><i class="bi bi-person me-1"></i>Patient</th>
                            <th><i class="bi bi-tag me-1"></i>Libellé</th>
                            <th class="text-end"><i class="bi bi-currency-exchange me-1"></i>Montant</th>
                            <th><i class="bi bi-building me-1"></i>Prestataire</th>
                            <th><i class="bi bi-check-circle me-1"></i>Statut</th>
                            <th class="text-end"><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                            <tr>
                                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $o->user->name ?? '—' }}</td>
                                <td>{{ optional($o->items->first())->label ?? '—' }}</td>
                                <td class="text-end">{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</td>
                                <td>{{ strtoupper($o->provider ?? '—') }}</td>
                                <td>
                                    <span class="badge {{ $o->status==='paid' ? 'bg-success' : ($o->status==='pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $o->status }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($o->payment_url && $o->status==='pending')
                                            <a class="btn btn-outline-primary btn-sm" href="{{ $o->payment_url }}" target="_blank">Ouvrir</a>
                                        @endif
                                        @if($o->status==='paid')
                                            <a href="{{ route('payments.receipt', $o->id) }}" class="btn btn-outline-success btn-sm">Quittance</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted text-center py-4">Aucun paiement récent</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Onglet Actions rapides avec design moderne --}}
        <div class="tab-pane fade" id="quick-actions" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="action-card admin-card">
                        <div class="action-header">
                            <div class="action-icon bg-success">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <h5 class="action-title">Gestion administrative</h5>
                        </div>
                        <div class="action-body">
                            <p class="action-description">Gérez les dossiers administratifs des patients et leurs informations personnelles.</p>
                            <a href="{{ route('secretaire.dossiersAdmin') }}" class="btn btn-success btn-action">
                                <i class="bi bi-folder2-open me-2"></i> Dossiers administratifs
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card planning-card">
                        <div class="action-header">
                            <div class="action-icon bg-primary">
                                <i class="bi bi-calendar2-week"></i>
                            </div>
                            <h5 class="action-title">Planification</h5>
                        </div>
                        <div class="action-body">
                            <p class="action-description">Planifiez, confirmez et gérez les rendez-vous des patients avec les médecins.</p>
                            <a href="{{ route('secretaire.rendezvous') }}" class="btn btn-primary btn-action">
                                <i class="bi bi-calendar2-week me-2"></i> Rendez-vous
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card hospital-card">
                        <div class="action-header">
                            <div class="action-icon bg-warning">
                                <i class="bi bi-hospital"></i>
                            </div>
                            <h5 class="action-title">Hospitalisation</h5>
                        </div>
                        <div class="action-body">
                            <p class="action-description">Gérez les admissions et les sorties des patients hospitalisés.</p>
                            <a href="{{ route('secretaire.admissions') }}" class="btn btn-warning btn-action">
                                <i class="bi bi-hospital me-2"></i> Admissions
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card payments-card">
                        <div class="action-header">
                            <div class="action-icon bg-info">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h5 class="action-title">Paiements avancés</h5>
                        </div>
                        <div class="action-body">
                            <p class="action-description">Gestion complète des paiements : création de liens, exports et paramètres.</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('secretaire.payments') }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-wallet2 me-1"></i> Paiements
                                </a>
                                <a href="{{ route('secretaire.payments.settings') }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-gear me-1"></i> Tarifs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- Fin tab-content --}}
  </div>
</div>

{{-- Styles modernes pour le tableau de bord secrétaire --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne secrétaire */
  .secretary-modern-header {
    background: linear-gradient(135deg, #16a085 0%, #27ae60 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(22, 160, 133, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
    font-size: 1.2rem;
  }
  
  .header-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.9rem;
  }
  
  /* Onglets secrétaire modernes */
  .secretary-tabs { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 0.25rem; 
    background: #f8fafc; 
    padding: 0.5rem; 
    border-radius: 12px;
    margin-bottom: 1rem;
  }
  
  .secretary-tabs .nav-link { 
    flex: 0 0 auto; 
    min-width: 140px; 
    text-align: center; 
    padding: 0.7rem 0.9rem; 
    border: none; 
    border-radius: 8px;
    white-space: nowrap; 
    color: #64748b; 
    font-weight: 500;
    font-size: 0.85rem;
    background: transparent;
    transition: all 0.2s ease;
  }
  
  .secretary-tabs .nav-link:hover {
    background: #e2e8f0;
    color: #475569;
  }
  
  .secretary-tabs .nav-link.active { 
    background: #16a085; 
    color: white;
    box-shadow: 0 2px 8px rgba(22, 160, 133, 0.2);
  }
  
  .tab-scroll { overflow: visible; }
  
  /* KPIs modernes pour secrétaire */
  .kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(22, 160, 133, 0.08);
    border: 1px solid rgba(22, 160, 133, 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
    height: 100px;
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #16a085, #27ae60);
  }
  
  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(22, 160, 133, 0.15);
  }
  
  .kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .kpi-icon.rdv-pending { background: linear-gradient(135deg, #f39c12, #e67e22); }
  .kpi-icon.patients-waiting { background: linear-gradient(135deg, #3498db, #2980b9); }
  .kpi-icon.patients-total { background: linear-gradient(135deg, #27ae60, #16a085); }
  .kpi-icon.payments-month { background: linear-gradient(135deg, #27ae60, #16a085); }
  .kpi-icon.payments-pending { background: linear-gradient(135deg, #f39c12, #e67e22); }
  .kpi-icon.payments-total { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .kpi-content {
    flex: 1;
  }
  
  .kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }
  
  .kpi-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
  }
  
  .kpi-sub {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
  }
  
  /* Tableaux modernes pour secrétaire */
  .table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(22, 160, 133, 0.1);
  }
  
  .table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .table-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .secretary-table {
    margin: 0;
  }
  
  .secretary-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 0.75rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .secretary-table td {
    padding: 0.75rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .secretary-table tbody tr:hover {
    background: #f8fafc;
  }
  
  /* Cartes d'actions modernes */
  .action-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(22, 160, 133, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
  }
  
  .action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .action-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid rgba(22, 160, 133, 0.1);
  }
  
  .action-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .action-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
  }
  
  .action-body {
    padding: 1.5rem;
  }
  
  .action-description {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
  }
  
  .btn-action {
    padding: 0.7rem 1.2rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  
  /* Responsive pour secrétaire */
  @media (max-width: 1200px) {
    .kpi-value { font-size: 2rem; }
    .kpi-icon { width: 50px; height: 50px; font-size: 20px; }
    .kpi-card { padding: 20px; height: 90px; }
  }
  
  @media (max-width: 768px) {
    .kpi-value { font-size: 1.75rem; }
    .kpi-label { font-size: 0.75rem; }
    .kpi-icon { width: 45px; height: 45px; font-size: 18px; }
    .kpi-card { padding: 16px; height: 80px; gap: 12px; }
    
    .header-content {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .action-card {
      margin-bottom: 1rem;
    }
  }
  
  /* Boutons optimisés */
  .btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 6px;
  }
</style>
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
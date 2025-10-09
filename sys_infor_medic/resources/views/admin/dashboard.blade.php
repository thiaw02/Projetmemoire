@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

<div class="admin-header d-flex align-items-center justify-content-between mb-3">
  <h2 class="mb-0">Dashboard Administrateur</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted small">Utilisateurs</div>
        <div class="display-6">{{ $kpis['totalUsers'] ?? ($users->count() ?? 0) }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted small">Patients</div>
        <div class="display-6">{{ $kpis['totalPatients'] ?? ($rolesCount['patient'] ?? ($users?->where('role','patient')->count() ?? 0)) }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted small">RDV (mois)</div>
        <div class="display-6">{{ $kpis['rdvThisMonth'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted small">Consultations (mois)</div>
        <div class="display-6">{{ $kpis['consultsThisMonth'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="text-muted small">Paiements (mois)</div>
        <div class="h5 mb-0">{{ number_format($kpis['paymentsPaidThisMonth'] ?? 0, 0, ',', ' ') }} XOF</div>
        <div class="small text-muted">En attente: {{ $kpis['paymentsPending'] ?? 0 }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Styles locaux pour onglets sur une ligne --}}
<style>
  /* Pleine largeur pour ce dashboard */
  /* Conteneur un peu réduit par rapport à la pleine largeur */
  body > .container { max-width: 1500px !important; }
  .page-section { padding-left: .75rem; padding-right: .75rem; }
  .content-card { background: #fff; border-radius: .75rem; box-shadow: 0 8px 24px rgba(0,0,0,.06); padding: 1rem; }

  /* Header collant pour un comportement moderne */
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  /* Tabs modernes sur une ligne */
  /* Onglets sur une seule ligne, tous visibles sans défilement */
  .admin-tabs { display: flex; flex-wrap: nowrap; justify-content: flex-start; gap: .5rem; }
  .admin-tabs .nav-link { flex: 0 0 auto; min-width: 160px; text-align: center; padding: .5rem .75rem; border: 0; border-bottom: 2px solid transparent; white-space: nowrap; color: #27ae60; font-weight: 600; }
  .admin-tabs .nav-link.active { border-bottom-color: #27ae60; color: #145a32; background: transparent; }
  .tab-scroll { overflow: visible; }
  .tab-scroll::-webkit-scrollbar { height: 0; }
  .tab-scroll::-webkit-scrollbar-thumb { background: transparent; }
  /* Sidebar collante et un peu plus compacte */
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
</style>
{{-- Nav tabs --}}
<div class="tab-scroll">
<ul class="nav nav-tabs admin-tabs flex-nowrap" id="adminTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true"><i class="bi bi-people me-1"></i> Gérer utilisateurs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="patients-tab" data-bs-toggle="tab" data-bs-target="#patients" type="button" role="tab" aria-controls="patients" aria-selected="false"><i class="bi bi-person-vcard me-1"></i> Gérer patients</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false"><i class="bi bi-graph-up me-1"></i> Statistiques globales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="false"><i class="bi bi-person-gear me-1"></i> Superviser rôles</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="false"><i class="bi bi-shield-lock me-1"></i> Gestion rôles & permissions</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false"><i class="bi bi-wallet2 me-1"></i> Paiements</button>
    </li>
</ul>
</div>

<div class="tab-content mt-3" id="adminTabContent">

    {{-- Gérer utilisateurs --}}
    <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Liste des utilisateurs</h4>
          <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchUsers" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Liste avancée</a>
            <a href="{{ route('admin.affectations.index') }}" class="btn btn-outline-success btn-sm" title="Affecter infirmiers aux médecins">Affectations Médecin–Infirmier</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm" title="Ajouter un utilisateur" aria-label="Ajouter un utilisateur">➕ Ajouter</a>
          </div>
        </div>

        <table class="table table-striped" id="usersTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Spécialité</th>
                    <th>Liens</th>
                    <th>Actif</th>
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
                          @if($user->role==='medecin')
                            @php
                              $names = ($user->nurses ?? collect())->pluck('name')->all();
                            @endphp
                            <span class="badge bg-light text-dark border" title="{{ implode(', ', $names) }}">{{ count($names) }} infirmier(s)</span>
                          @elseif($user->role==='infirmier')
                            @php
                              $names = ($user->doctors ?? collect())->pluck('name')->all();
                            @endphp
                            <span class="badge bg-light text-dark border" title="{{ implode(', ', $names) }}">{{ count($names) }} médecin(s)</span>
                          @else
                            —
                          @endif
                        </td>
                        <td>
                          <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}" class="mb-0">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary">{{ $user->active ? 'Actif' : 'Inactif' }}</button>
                          </form>
                        </td>
                        <td>
                          <div class="d-flex gap-2" role="group" aria-label="Actions utilisateur">
<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                          </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- Gérer patients --}}
    <div class="tab-pane fade" id="patients" role="tabpanel" aria-labelledby="patients-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Liste des patients</h4>
          <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchPatients" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm" title="Listes avancées">Listes avancées</a>
            <a href="{{ route('admin.patients.create') }}" class="btn btn-success btn-sm" title="Ajouter un patient" aria-label="Ajouter un patient">➕ Ajouter</a>
          </div>
        </div>

        <table class="table table-striped" id="patientsTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date création</th>
                    <th>Actif</th>
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
                          <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}" class="mb-0">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary">{{ $user->active ? 'Actif' : 'Inactif' }}</button>
                          </form>
                        </td>
                        <td>
                          <div class="d-flex gap-2" role="group" aria-label="Actions patient">
                            <a href="{{ route('admin.patients.edit', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.patients.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce patient ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                          </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Statistiques globales --}}
    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h4 class="mb-0">Statistiques globales</h4>
          <div class="btn-group" role="group" aria-label="Fenêtre temporelle">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-window="2">2 mois</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-window="6">6 mois</button>
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-window="12">12 mois (1 an)</button>
          </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">Répartition des rôles</div>
                    <div class="card-body">
                        <canvas id="rolesChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">Statuts des Rendez-vous</div>
                    <div class="card-body">
                        <canvas id="rendezvousChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Volumes mensuels (<span id="windowLabel">12 derniers mois</span>)</div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="260"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Rendez-vous par statut (<span id="windowLabelStatus">12 derniers mois</span>)</div>
                    <div class="card-body">
                        <canvas id="rdvStatusMonthlyChart" height="240"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Superviser rôles --}}
    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
        <h4 class="mb-3">Superviser les rôles</h4>
        <div class="row g-3 mb-3">
          @foreach($rolesCount ?? [] as $roleName => $count)
            <div class="col-md-2">
              <div class="card text-center">
                <div class="card-body py-2">
                  <div class="text-muted small" style="text-transform: capitalize;">{{ $roleName }}</div>
                  <div class="h4 mb-0">{{ $count }}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Rôle actuel</th>
                <th>Changer de rôle</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $u)
                <tr>
                  <td>{{ $u->name }}</td>
                  <td>{{ $u->email }}</td>
                  <td><span class="badge bg-success" style="text-transform: capitalize;">{{ $u->role }}</span></td>
                  <td>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary js-open-role-modal"
                            data-user-id="{{ $u->id }}"
                            data-user-name="{{ $u->name }}"
                            data-current-role="{{ $u->role }}"
                            data-bs-toggle="modal"
                            data-bs-target="#roleUpdateModal"
                            title="Changer le rôle">
                      <i class="bi bi-person-gear"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
    </div>

    {{-- Gestion rôles & permissions (vue simplifiée) --}}
    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
        <h4 class="mb-2">Accès indispensables</h4>
        <form method="POST" action="{{ route('admin.permissions.save') }}" id="permForm">
          @csrf

          <style>
            .perm-card .card-header { display: flex; align-items: center; gap: .5rem; font-weight: 600; }
            .perm-card .module-icon { font-size: 1.1rem; }
            .perm-table th, .perm-table td { vertical-align: middle; }
            .perm-level-group .btn { min-width: 92px; }
          </style>

          <div class="card perm-card shadow-sm">
            <div class="card-header">
              <i class="bi {{ $essentialModule['icon'] }} module-icon"></i>
              <span>{{ $essentialModule['title'] }}</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm perm-table mb-0">
                  <thead>
                    <tr>
                      <th style="min-width: 320px;">Rôle</th>
                      <th class="text-center">Niveau d'accès</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(['admin','secretaire','medecin','infirmier','patient'] as $r)
                      @php
                        $keys = array_map(fn($p)=>$p['key'], $essentialModule['permissions']);
                        $valsByKey = [];
                        foreach ($keys as $k) { $valsByKey[$k] = ($rolePermissions[$r][$k] ?? false); }
                        $countTrue = count(array_filter($valsByKey));
                        if ($countTrue === count($keys)) {
                          $currentLevel = 'full';
                        } elseif ($countTrue === 1) {
                          $viewKey = null;
                          foreach ($keys as $k) { if (str_ends_with($k, '.view')) { $viewKey = $k; break; } }
                          $currentLevel = ($viewKey && !empty($valsByKey[$viewKey])) ? 'read' : 'full';
                        } else {
                          $currentLevel = $countTrue > 0 ? 'full' : 'none';
                        }
                      @endphp
                      <tr>
                        <td class="text-capitalize">
                          <i class="bi bi-person-badge text-muted me-1"></i> {{ $r }}
                        </td>
                        <td class="text-center">
                          <div class="btn-group btn-group-sm perm-level-group" role="group" aria-label="Niveau d'accès">
                            @foreach([['none','Aucun'],['read','Lecture'],['full','Complet']] as $opt)
                              @php $id = 'lvl_ess_'.preg_replace('/\W+/','_', $essentialModule['title'].'_'.$r.'_'.$opt[0]); @endphp
                              <input type="radio" class="btn-check" name="levels[{{ $essentialModule['title'] }}][{{ $r }}]" id="{{ $id }}" autocomplete="off" value="{{ $opt[0] }}" {{ $currentLevel===$opt[0]?'checked':'' }}>
                              <label class="btn btn-outline-secondary" for="{{ $id }}">{{ $opt[1] }}</label>
                            @endforeach
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Enregistrer</button>
          </div>
        </form>
</div>

    {{-- Paiements --}}
    <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Paiements récents</h4>
        <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-primary btn-sm">Gérer (Secrétaire)</a>
      </div>
      @php($recentOrders = \App\Models\Order::with('items','user')->orderByDesc('created_at')->take(20)->get())
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Date</th>
              <th>Patient</th>
              <th>Libellé</th>
              <th class="text-end">Montant</th>
              <th>Prestataire</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $o)
              <tr>
                <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $o->user->name ?? '—' }}</td>
                <td>{{ optional($o->items->first())->label ?? '—' }}</td>
                <td class="text-end">{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</td>
                <td>{{ strtoupper($o->provider ?? '—') }}</td>
                <td>
                  <span class="badge {{ $o->status==='paid' ? 'bg-success' : ($o->status==='pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $o->status }}</span>
                </td>
                <td class="text-end d-flex gap-1 justify-content-end">
                  @if($o->status==='paid')
                    <a href="{{ route('payments.receipt', $o->id) }}" class="btn btn-outline-success btn-sm">Quittance</a>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-muted">Aucun paiement</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

</div>
@endsection

<!-- Modal de mise à jour de rôle -->
<div class="modal fade" id="roleUpdateModal" tabindex="-1" aria-labelledby="roleUpdateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleUpdateLabel">Changer le rôle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <form id="roleUpdateForm" method="POST" action="#">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-2 small text-muted">Utilisateur: <span id="roleUserName">—</span></div>
          <div class="mb-2">Sélectionnez le nouveau rôle:</div>
          <div class="d-flex flex-column gap-1">
            @foreach(['admin'=>'Administrateur','secretaire'=>'Secrétaire','medecin'=>'Médecin','infirmier'=>'Infirmier','patient'=>'Patient'] as $val=>$label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="role" id="role_{{ $val }}" value="{{ $val }}">
              <label class="form-check-label" for="role_{{ $val }}">{{ $label }}</label>
            </div>
            @endforeach
          </div>
          <div class="alert alert-warning mt-3 p-2" role="alert">
            Confirmez-vous la modification du rôle de cet utilisateur ?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Confirmer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Toast succès -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
  <div id="roleToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="roleToastBody">
        Rôle mis à jour.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Rôle: ouverture du modal et soumission
    (function(){
      const modalEl = document.getElementById('roleUpdateModal');
      const form = document.getElementById('roleUpdateForm');
      const userNameSpan = document.getElementById('roleUserName');
      if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget; // bouton déclencheur
          if (!button) return;
          const uid = button.getAttribute('data-user-id');
          const uname = button.getAttribute('data-user-name') || '';
          const current = button.getAttribute('data-current-role') || '';
          if (userNameSpan) userNameSpan.textContent = uname;
          (form?.querySelectorAll('input[name=\"role\"]')||[]).forEach(r=>{ r.checked = (r.value===current); });
          if (form) form.action = `/admin/users/${uid}/role`;
        });
      }

      // Toast succès si session('success')
      const hasSuccess = {{ session('success') ? 'true' : 'false' }};
      if (hasSuccess) {
        const toastEl = document.getElementById('roleToast');
        const toastBody = document.getElementById('roleToastBody');
        if (toastBody) toastBody.textContent = @json(session('success'));
        if (toastEl) new bootstrap.Toast(toastEl, { delay: 3000 }).show();
      }
    })();

    // Données dynamiques transmises depuis le contrôleur
    const rolesCount = @json($rolesCount ?? []);
    const months = @json($months ?? []);
    const rdvSeries = @json($rendezvousCounts ?? []);
    const admissionsSeries = @json($admissionsCounts ?? []);
    const consultsSeries = @json($consultationsCounts ?? []);
    const patientsSeries = @json($patientsCounts ?? []);
    const rdvStatusCounts = @json($rdvStatusCounts ?? []);
    const rdvPendingSeriesFull = @json($rdvPendingSeries ?? []);
    const rdvConfirmedSeriesFull = @json($rdvConfirmedSeries ?? []);
    const rdvCancelledSeriesFull = @json($rdvCancelledSeries ?? []);
    
    // Variables globales pour les graphiques
    let chartsInitialized = false;
    let monthlyChart, rdvStatusMonthlyChart;
    let windowMonths = 12; // défaut 12 mois
    
    // Palette
    const colors = {
      green: '#27ae60', darkGreen: '#145a32', teal: '#20c997', orange: '#fd7e14', pink: '#e83e8c', blue: '#3b82f6', red: '#ef4444', yellow: '#eab308'
    };
    
    // Outil: slicer pour fenêtre N derniers mois
    function lastN(arr, n){ return (arr || []).slice(Math.max((arr || []).length - n, 0)); }
    
    function windowLabelText(n){
      if (n===2) return '2 derniers mois';
      if (n===6) return '6 derniers mois';
      return '12 derniers mois';
    }
    
    function buildMonthlyConfig(n){
      const lbls = lastN(months, n);
      return {
        type: 'line',
        data: {
          labels: lbls,
          datasets: [
            { label: 'Rendez-vous', data: lastN(rdvSeries, n), borderColor: colors.green, backgroundColor: colors.green + '33', tension: .3, fill: true },
            { label: 'Consultations', data: lastN(consultsSeries, n), borderColor: colors.blue, backgroundColor: colors.blue + '33', tension: .3, fill: true },
            { label: 'Admissions', data: lastN(admissionsSeries, n), borderColor: colors.orange, backgroundColor: colors.orange + '33', tension: .3, fill: true },
            { label: 'Patients (créés)', data: lastN(patientsSeries, n), borderColor: colors.pink, backgroundColor: colors.pink + '33', tension: .3, fill: true }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' }, title: { display: true, text: windowLabelText(n) } },
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
      };
    }
    
    function buildRdvStatusMonthlyConfig(n){
      const lbls = lastN(months, n);
      return {
        type: 'line',
        data: {
          labels: lbls,
          datasets: [
            { label: 'RDV en attente', data: lastN(rdvPendingSeriesFull, n), borderColor: colors.yellow, backgroundColor: colors.yellow + '33', tension: .3, fill: true },
            { label: 'RDV confirmés', data: lastN(rdvConfirmedSeriesFull, n), borderColor: colors.green, backgroundColor: colors.green + '33', tension: .3, fill: true },
            { label: 'RDV annulés', data: lastN(rdvCancelledSeriesFull, n), borderColor: colors.red, backgroundColor: colors.red + '33', tension: .3, fill: true }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' }, title: { display: true, text: windowLabelText(n) } },
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
      };
    }
    
    // Fonction pour initialiser tous les graphiques
    function initializeCharts() {
        if (chartsInitialized) return;
        
        // Vérifier si Chart.js est chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js n\'est pas chargé !');
            return;
        }
        
        console.log('Initialisation des graphiques...');
        initRolesChart();
        initRendezVousChart();
        initMonthlyCharts();
        
        chartsInitialized = true;
    }
    
    // Graphique des rôles
    function initRolesChart() {

        const rolesLabels = Object.keys(rolesCount);
        const rolesValues = Object.values(rolesCount);
        
        const rolesChartElement = document.getElementById('rolesChart');
        if (!rolesChartElement) {
            console.error('Élément rolesChart non trouvé !');
            return;
        }
        
        new Chart(rolesChartElement, {
          type: 'bar',
          data: {
            labels: rolesLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{
              label: "Utilisateurs",
              data: rolesValues,
              backgroundColor: [colors.blue, colors.green, colors.yellow, colors.teal, colors.red]
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false }, title: { display: true, text: 'Utilisateurs par rôle' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
        });
    }
    
    // Graphique des statuts RDV
    function initRendezVousChart() {

        const rdvStatusLabels = Object.keys(rdvStatusCounts).map(s => (s || 'non défini').replace('_',' '));
        const rdvStatusValues = Object.values(rdvStatusCounts);
        
        const rendezvousChartElement = document.getElementById('rendezvousChart');
        if (!rendezvousChartElement) {
            console.error('Élément rendezvousChart non trouvé !');
            return;
        }
        
        new Chart(rendezvousChartElement, {
          type: 'doughnut',
          data: {
            labels: rdvStatusLabels,
            datasets: [{
              data: rdvStatusValues,
              backgroundColor: [colors.green, colors.orange, colors.red, colors.blue, colors.pink, colors.yellow]
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Répartition des statuts de RDV' } }
          }
        });
    }
    
    // Graphiques mensuels
    function initMonthlyCharts() {
        console.log('Initialisation des graphiques mensuels...');
        
        // Vérifier les données nécessaires
        if (!months || months.length === 0) {
            console.error('Données des mois manquantes !', months);
            return;
        }
        
        if (!rdvSeries || rdvSeries.length === 0) {
            console.error('Données RDV manquantes !', rdvSeries);
            return;
        }
        
        console.log('Vérification des éléments canvas...');
        const monthlyChartElement = document.getElementById('monthlyChart');
        const rdvStatusMonthlyElement = document.getElementById('rdvStatusMonthlyChart');
        
        if (!monthlyChartElement) {
            console.error('Élément monthlyChart non trouvé !', monthlyChartElement);
            return;
        }
        
        if (!rdvStatusMonthlyElement) {
            console.error('Élément rdvStatusMonthlyChart non trouvé !', rdvStatusMonthlyElement);
            return;
        }
        
        console.log('Éléments canvas trouvés:', {
            monthlyChart: monthlyChartElement,
            rdvStatusChart: rdvStatusMonthlyElement
        });
        
        try {
            console.log('Création du graphique monthlyChart...');
            const monthlyCtx = monthlyChartElement.getContext('2d');
            const monthlyConfig = buildMonthlyConfig(windowMonths);
            console.log('Configuration monthlyChart:', monthlyConfig);
            monthlyChart = new Chart(monthlyCtx, monthlyConfig);
            console.log('MonthlyChart créé:', monthlyChart);
        } catch (error) {
            console.error('Erreur lors de la création du monthlyChart:', error);
        }
        
        try {
            console.log('Création du graphique rdvStatusMonthlyChart...');
            const rdvStatusMonthlyCtx = rdvStatusMonthlyElement.getContext('2d');
            const rdvStatusConfig = buildRdvStatusMonthlyConfig(windowMonths);
            console.log('Configuration rdvStatusChart:', rdvStatusConfig);
            rdvStatusMonthlyChart = new Chart(rdvStatusMonthlyCtx, rdvStatusConfig);
            console.log('RdvStatusChart créé:', rdvStatusMonthlyChart);
        } catch (error) {
            console.error('Erreur lors de la création du rdvStatusChart:', error);
        }
        
        console.log('Graphiques mensuels - Processus terminé !');
    }



    function setWindow(n){
      windowMonths = n;
      document.getElementById('windowLabel').textContent = windowLabelText(n);
      document.getElementById('windowLabelStatus').textContent = windowLabelText(n);
      
      // Vérifier que les graphiques existent avant de les détruire
      if (monthlyChart) {
          monthlyChart.destroy();
      }
      if (rdvStatusMonthlyChart) {
          rdvStatusMonthlyChart.destroy();
      }
      
      // Recréer les graphiques s'ils existaient
      const monthlyChartElement = document.getElementById('monthlyChart');
      const rdvStatusMonthlyElement = document.getElementById('rdvStatusMonthlyChart');
      
      if (monthlyChartElement && rdvStatusMonthlyElement) {
          const monthlyCtx = monthlyChartElement.getContext('2d');
          const rdvStatusMonthlyCtx = rdvStatusMonthlyElement.getContext('2d');
          
          monthlyChart = new Chart(monthlyCtx, buildMonthlyConfig(n));
          rdvStatusMonthlyChart = new Chart(rdvStatusMonthlyCtx, buildRdvStatusMonthlyConfig(n));
      }
      
      // Boutons actifs
      document.querySelectorAll('[data-window]').forEach(b=>b.classList.toggle('active', parseInt(b.dataset.window,10)===n));
    }

    // Bind boutons
    document.querySelectorAll('[data-window]').forEach(btn=>{
      btn.addEventListener('click', ()=> setWindow(parseInt(btn.dataset.window,10)) );
    });
    
    // Initialiser les graphiques quand l'onglet statistiques est ouvert
    const statsTabButton = document.getElementById('stats-tab');
    if (statsTabButton) {
        statsTabButton.addEventListener('click', function() {
            // Attendre un peu que l'onglet soit visible
            setTimeout(initializeCharts, 100);
        });
    }
    
    // Si l'onglet statistiques est déjà actif au chargement de la page
    const statsTab = document.getElementById('stats');
    if (statsTab && statsTab.classList.contains('active')) {
        setTimeout(initializeCharts, 100);
    }

    // Filtres simples tables Admin
    function filterTable(inputId, tableId){
      const q = (document.getElementById(inputId)?.value || '').toLowerCase();
      const rows = document.querySelectorAll(`#${tableId} tbody tr`);
      rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    }
    document.getElementById('searchUsers')?.addEventListener('input', ()=>filterTable('searchUsers','usersTable'));
    document.getElementById('searchPatients')?.addEventListener('input', ()=>filterTable('searchPatients','patientsTable'));

    // Debug: Affichage des données pour vérification
    console.log('Données pour graphiques:');
    console.log('Rôles:', rolesCount);
    console.log('Mois:', months);
    console.log('RDV:', rdvSeries);
    console.log('Consultations:', consultsSeries);
    console.log('Admissions:', admissionsSeries);
    console.log('Patients:', patientsSeries);
    console.log('Statuts RDV:', rdvStatusCounts);
    console.log('RDV Pending:', rdvPendingSeriesFull);
    console.log('RDV Confirmed:', rdvConfirmedSeriesFull);
    console.log('RDV Cancelled:', rdvCancelledSeriesFull);
</script>
@endsection

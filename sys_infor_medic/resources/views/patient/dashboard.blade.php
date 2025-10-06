@extends('layouts.app')

@section('content')
<style>
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  .sidebar-sticky { position: sticky; top: 1rem; }
  /* Forcer cette page à occuper toute la largeur */
  body > .container { max-width: 100% !important; padding-left: 0; padding-right: 0; }
  .page-section { padding-left: 1rem; padding-right: 1rem; }
  .content-card { background: #fff; border-radius: .75rem; box-shadow: 0 8px 24px rgba(0,0,0,.06); padding: 1rem; }
  .role-tabs { flex-wrap: nowrap; gap: .25rem; }
  .role-tabs .nav-link { padding: .5rem .75rem; border: 0; border-bottom: 2px solid transparent; white-space: nowrap; color: #27ae60; font-weight: 600; }
  .role-tabs .nav-link.active { border-bottom-color: #27ae60; color: #145a32; background: transparent; }
  .tab-scroll { overflow-x: auto; -ms-overflow-style: none; scrollbar-width: thin; }
  .tab-scroll::-webkit-scrollbar { height: 6px; }
  .tab-scroll::-webkit-scrollbar-thumb { background: rgba(39,174,96,.35); border-radius: 3px; }
  /* Réduction de la taille du calendrier + disposition côte à côte */
  .rdv-layout #calendar { min-height: 420px; }
  .rdv-layout .fc .fc-toolbar-title { font-size: 1.05rem; }
  .rdv-layout .fc .fc-daygrid-day-number { font-size: .9rem; }
  .rdv-layout .content-card { height: 100%; }
  @media (min-width: 992px){
    .rdv-layout .col-lg-7, .rdv-layout .col-lg-5 { display: flex; }
    .rdv-layout .content-card { width: 100%; }
  }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

    <div class="admin-header d-flex align-items-center justify-content-between mb-3">
      <h2 class="mb-0 text-success">Espace Patient</h2>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="page-section">
      <div class="content-card">

        {{-- Onglets de navigation --}}
        <div class="tab-scroll">
        <ul class="nav nav-tabs role-tabs mb-3 flex-nowrap" id="patientTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="rdv-tab" data-bs-toggle="tab" data-bs-target="#rdv" type="button" role="tab"><i class="bi bi-calendar2-week me-1"></i> Prendre un rendez-vous</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dossier-tab" data-bs-toggle="tab" data-bs-target="#dossier" type="button" role="tab"><i class="bi bi-file-earmark-medical me-1"></i> Mon dossier médical</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mesrdv-tab" data-bs-toggle="tab" data-bs-target="#mesrdv" type="button" role="tab"><i class="bi bi-list-check me-1"></i> Mes rendez-vous</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Historique</button>
            </li>
        </ul>
        </div>

        <div class="tab-content" id="patientTabContent">
            {{-- Onglet RDV (prise) --}}
            <div class="tab-pane fade show active" id="rdv" role="tabpanel">
                <div class="row g-3 align-items-start rdv-layout">
                    <div class="col-lg-7">
                        <div class="card content-card">
                            <div class="card-header">Prochain rendez-vous</div>
                            <div class="card-body">
                                @if($nextRdv)
                                  <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                    <div>
                                      <div class="fw-semibold text-success">{{ \Carbon\Carbon::parse($nextRdv->date)->translatedFormat('d F Y') }} à {{ $nextRdv->heure }}</div>
                                      <div class="text-muted">Avec: <strong>{{ $nextRdv->medecin->name ?? '—' }}</strong></div>
                                      <div class="mt-1">
                                        <span class="badge {{ (strtolower($nextRdv->statut)==='confirmé' || strtolower($nextRdv->statut)==='confirme') ? 'bg-success' : ((strtolower($nextRdv->statut)==='annulé' || strtolower($nextRdv->statut)==='annule') ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ str_replace('_',' ', $nextRdv->statut ?? 'en_attente') }}</span>
                                      </div>
                                    </div>
                                    <div class="text-end small text-muted">
                                      Dernière mise à jour<br>{{ optional($nextRdv->updated_at)->format('d/m/Y H:i') }}
                                    </div>
                                  </div>
                                @else
                                  <div class="text-muted">Aucun prochain rendez-vous planifié.</div>
                                @endif
                                <hr>
                                <div class="row g-3">
                                  <div class="col-6">
                                    <div class="p-3 rounded bg-light border">
                                      <div class="small text-muted">Consultations totales</div>
                                      <div class="h5 mb-0">{{ $stats['totalConsultations'] ?? 0 }}</div>
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="p-3 rounded bg-light border">
                                      <div class="small text-muted">RDV en attente</div>
                                      <div class="h5 mb-0">{{ $stats['rdvEnAttente'] ?? 0 }}</div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card content-card">
                            <div class="card-header bg-success text-white">Demande de rendez-vous</div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('patient.storeRendez') }}" id="rdvForm">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label">Date</label>
                                            <input type="date" name="date" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Heure</label>
                                            <input type="time" name="heure" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Médecin</label>
                                            <select name="medecin_id" class="form-select" required>
                                                <option value="">-- Choisir --</option>
                                                @foreach($medecins as $med)
                                                    <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="motif" class="form-label">Motif</label>
                                            <textarea name="motif" class="form-control" rows="3" placeholder="Motif du rendez-vous..." required></textarea>
                                        </div>
                                    </div>
                                    <button class="btn btn-success w-100 mt-2">Envoyer la demande</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Onglet Dossier Médical --}}
            <div class="tab-pane fade" id="dossier" role="tabpanel">
                <h4 class="mb-3">📁 Mon dossier médical</h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">📝 Ordonnances</div>
                            <div class="card-body">
                                @forelse($ordonnances as $ord)
                                    <div class="mb-2">
                                        <div class="small text-muted">{{ optional($ord->date_ordonnance)->format('d/m/Y') ?? (string)($ord->date_ordonnance ?? '') }}</div>
                                        <div>{{ $ord->contenu ?? $ord->medicaments ?? '—' }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted">Aucune ordonnance.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">🔬 Analyses</div>
                            <div class="card-body">
                                @forelse($analyses as $an)
                                    <div class="mb-2">
                                        <div class="small text-muted">{{ optional($an->date_analyse)->format('d/m/Y') ?? (string)($an->date_analyse ?? '') }}</div>
                                        <div><strong>Type:</strong> {{ $an->type_analyse ?? $an->type ?? '—' }}</div>
                                        <div><strong>Résultat:</strong> {{ $an->resultats ?? $an->resultat ?? '—' }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted">Aucune analyse.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">💉 Consultations</div>
                            <div class="card-body">
                                @forelse($consultations as $c)
                                    <div class="mb-2">
                                        <div class="small text-muted">{{ optional($c->date_consultation)->format('d/m/Y') ?? (string)($c->date_consultation ?? '') }}</div>
                                        <div><strong>Médecin:</strong> {{ $c->medecin->name ?? '—' }}</div>
                                        <div><strong>Diagnostic:</strong> {{ $c->diagnostic ?? '—' }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted">Aucune consultation.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Onglet Mes Rendez-vous (listing) --}}
            <div class="tab-pane fade" id="mesrdv" role="tabpanel">
                <h4 class="mb-3">📅 Mes rendez-vous</h4>
                <table class="table table-striped table-bordered bg-light">
                    <thead class="table-success">
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Médecin</th>
                            <th>Motif</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendezVous as $rdv)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
                                <td>{{ $rdv->heure }}</td>
                                <td>{{ $rdv->medecin->name ?? '—' }}</td>
                                <td>{{ $rdv->motif }}</td>
                                <td>
                                    @php $s = strtolower((string)$rdv->statut); @endphp
                                    <span class="badge {{ $s==='confirmé' || $s==='confirme' ? 'bg-success' : ($s==='annulé' || $s==='annule' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                        {{ str_replace('_',' ', $rdv->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Aucun rendez-vous.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Onglet Historique (consultations) --}}
            <div class="tab-pane fade" id="historique" role="tabpanel">
                <h4 class="mb-3">📜 Historique de mes consultations</h4>
                <table class="table table-striped table-bordered bg-light">
                    <thead class="table-success">
                        <tr>
                            <th>Date</th>
                            <th>Diagnostic</th>
                            <th>Médecin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $c)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($c->date_consultation)->format('d/m/Y') }}</td>
                                <td>{{ $c->diagnostic ?? '—' }}</td>
                                <td>{{ $c->medecin->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Aucune consultation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Aucun calendrier à initialiser désormais. Vous pouvez ajouter ici des scripts pour valider le formulaire si besoin.
</script>
@endsection

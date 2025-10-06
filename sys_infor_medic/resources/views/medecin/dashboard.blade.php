@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  .quick-actions .btn { min-width: 220px; }
  .card-body.scrollable { max-height: 360px; overflow: auto; }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

<div class="admin-header d-flex align-items-center justify-content-between mb-3">
  <h2 class="mb-0 text-success">Tableau de Bord Médecin</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <p class="mb-0">Bienvenue, Dr. {{ auth()->user()->name }} ! Voici un résumé rapide de votre activité.</p>
          <input type="text" id="searchMedecin" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
        </div>

        <!-- Boutons principaux -->
        <div class="mb-4 quick-actions d-flex flex-wrap gap-2">
            <a href="{{ route('medecin.consultations') }}" class="btn btn-outline-primary">🩺 Consultations</a>
            <a href="{{ route('medecin.ordonnances') }}" class="btn btn-outline-warning">💊 Ordonnances</a>
            <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-outline-success">📁 Consulter les dossiers</a>
        </div>

        <div class="row">
            <!-- RDV confirmés à venir -->
            <div class="col-md-6 mb-3">
                <div class="card border-success shadow-sm">
                    <div class="card-header bg-success text-white">
                        RDV confirmés à venir
                    </div>
                    <div class="card-body scrollable">
                        @if(($upcomingRdv ?? collect())->isEmpty())
                            <p>Aucun RDV à venir.</p>
                        @else
                            <div class="d-flex justify-content-end mb-2">
                              <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-success" data-rdv-filter="day">Jour</button>
                                <button type="button" class="btn btn-outline-success" data-rdv-filter="week">Semaine</button>
                                <button type="button" class="btn btn-outline-success active" data-rdv-filter="all">Tous</button>
                              </div>
                            </div>
                            <ul id="rdv-upcoming-list" class="list-group list-group-flush">
                                @foreach($upcomingRdv as $rdv)
<li class="list-group-item d-flex justify-content-between align-items-center" data-date="{{ \Carbon\Carbon::parse($rdv->date)->toDateString() }}" data-dt="{{ \Carbon\Carbon::parse(($rdv->date ?? '') . ' ' . ($rdv->heure ?? '00:00'))->format('Y-m-d\TH:i') }}">
                                      <div>
                                        <div class="fw-semibold d-flex align-items-center gap-2">
                                          <i class="bi bi-person-circle text-success"></i>
                                          <a href="{{ route('medecin.dossierpatient', ['patient_id' => optional($rdv->patient)->id]) }}" class="text-decoration-none">
                                            {{ $rdv->patient->nom ?? ($rdv->patient->user->name ?? '—') }} {{ $rdv->patient->prenom ?? '' }}
                                          </a>
                                          <span class="badge {{ in_array(strtolower($rdv->statut), ['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array(strtolower($rdv->statut), ['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                            {{ str_replace('_',' ', $rdv->statut ?? 'confirmé') }}
                                          </span>
                                        </div>
                                        <div class="small text-muted">Prochain RDV</div>
                                      </div>
                                      <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark border">
                                          {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }} {{ $rdv->heure }}
                                        </span>
                                        <a href="{{ route('medecin.consultations', ['patient_id' => optional($rdv->patient)->id, 'date_time' => \Carbon\Carbon::parse(($rdv->date ?? '') . ' ' . ($rdv->heure ?? '00:00'))->format('Y-m-d\\TH:i')]) }}" class="btn btn-sm btn-success">
                                          Créer consultation
                                        </a>
                                        <a href="{{ route('medecin.ordonnances', ['patient_id' => optional($rdv->patient)->id]) }}" class="btn btn-sm btn-outline-warning">
                                          Rédiger ordonnance
                                        </a>
                                      </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dossiers récents consultés -->
            <div class="col-md-6 mb-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Dossiers récents consultés
                    </div>
                    <div class="card-body scrollable">
                        @if($dossiersRecents->isEmpty())
                            <p class="mb-0">Aucun dossier consulté récemment.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($dossiersRecents as $patient)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <div>
                                        <i class="bi bi-folder2-open me-1 text-primary"></i>
                                        <span class="fw-semibold">{{ $patient->nom }} {{ $patient->prenom }}</span>
                                      </div>
                                      <a href="{{ route('medecin.consultations', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-outline-primary">
                                        Ouvrir le dossier
                                      </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs en bas -->
        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <div class="card text-center shadow-sm">
              <div class="card-body py-3">
                <div class="text-muted small">À consulter (RDV confirmés)</div>
                <div class="display-6">{{ $stats['aConsulter'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center shadow-sm">
              <div class="card-body py-3">
                <div class="text-muted small">RDV en attente</div>
                <div class="display-6">{{ $stats['rdvEnAttente'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center shadow-sm">
              <div class="card-body py-3">
                <div class="text-muted small">Patients traités (mois)</div>
                <div class="display-6">{{ $stats['consultesCeMois'] ?? 0 }}</div>
              </div>
            </div>
          </div>
        </div>

</div>
</div>
  </div>
</div>
<script>
  (function(){
    const inp = document.getElementById('searchMedecin');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('.card .card-body ul li, .card .card-body .card').forEach(el=>{
        const t = el.innerText?.toLowerCase?.() || '';
        if (el.tagName==='LI' || el.classList.contains('card')) {
          el.style.display = t.includes(q) ? '' : 'none';
        }
      });
    }
    inp?.addEventListener('input', filter);

    // Filtres RDV: jour / semaine / tous
    const rdvList = document.getElementById('rdv-upcoming-list');
    const rdvFilterBtns = document.querySelectorAll('[data-rdv-filter]');

    function formatDate(d){ return d.toISOString().slice(0,10); }
    function startOfWeek(date){ const d = new Date(date); const day = (d.getDay()+6)%7; d.setDate(d.getDate()-day); d.setHours(0,0,0,0); return d; }
    function endOfWeek(date){ const d = startOfWeek(date); d.setDate(d.getDate()+6); d.setHours(23,59,59,999); return d; }

function applyRdvFilter(mode){
      if(!rdvList) return;
      const today = new Date();
      const todayStr = formatDate(today);
      const startW = startOfWeek(today);
      const endW = endOfWeek(today);
      rdvList.querySelectorAll('li[data-date]')?.forEach(li=>{
        const d = li.getAttribute('data-date');
        if(!d){ li.style.display=''; return; }
        let show = true;
        if(mode==='day'){
          show = (d === todayStr);
        } else if(mode==='week'){
          const ds = new Date(d+'T00:00:00');
          show = (ds>=startW && ds<=endW);
        } else {
          show = true;
        }
        li.style.display = show ? '' : 'none';
      });
      rdvFilterBtns.forEach(b=>b.classList.toggle('active', b.getAttribute('data-rdv-filter')===mode));
      sortRdvListAsc();
    }

    function sortRdvListAsc(){
      if(!rdvList) return;
      const items = Array.from(rdvList.querySelectorAll('li[data-dt]'));
      items.sort((a,b)=> new Date(a.getAttribute('data-dt')) - new Date(b.getAttribute('data-dt')));
      items.forEach(li=> rdvList.appendChild(li));
    }

    rdvFilterBtns.forEach(btn=>{
      btn.addEventListener('click', ()=> applyRdvFilter(btn.getAttribute('data-rdv-filter')));
    });

    // Tri initial par prochain RDV
    sortRdvListAsc();

  })();
</script>
@endsection

@extends('layouts.app')

@section('content')
<style>
  /* Dashboard Médecin Moderne */
  body > .container { max-width: 1500px !important; }
  .medecin-header { 
    position: sticky; 
    top: 0; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    z-index: 10; 
    padding: 1.5rem 0;
    border-radius: 0 0 16px 16px;
    margin-bottom: 2rem;
  }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  
  /* Stats Cards KPIs */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border-left: 5px solid;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
  }
  
  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    opacity: 0.1;
    border-radius: 50%;
    background: var(--stat-color);
    transform: translate(20px, -20px);
  }
  
  .stat-card.rdv-confirme {
    border-left-color: #10b981;
    --stat-color: #10b981;
  }
  .stat-card.rdv-attente {
    border-left-color: #f59e0b;
    --stat-color: #f59e0b;
  }
  .stat-card.patients-traites {
    border-left-color: #3b82f6;
    --stat-color: #3b82f6;
  }
  .stat-card.infirmiers {
    border-left-color: #8b5cf6;
    --stat-color: #8b5cf6;
  }
  
  .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1;
  }
  
  .stat-label {
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
  }
  
  .stat-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
  }
  
  /* Actions rapides modernes */
  .actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .action-card {
    background: white;
    border: 2px solid transparent;
    border-radius: 16px;
    padding: 1.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
  }
  
  .action-card:hover {
    transform: translateY(-2px);
    border-color: var(--action-color);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    text-decoration: none;
  }
  
  .action-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
  }
  
  .action-card.consultations {
    --action-color: #3b82f6;
  }
  .action-card.consultations .action-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  }
  
  .action-card.ordonnances {
    --action-color: #f59e0b;
  }
  .action-card.ordonnances .action-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
  }
  
  .action-card.dossiers {
    --action-color: #10b981;
  }
  .action-card.dossiers .action-icon {
    background: linear-gradient(135deg, #10b981, #059669);
  }
  
  .action-card.analyses {
    --action-color: #8b5cf6;
  }
  .action-card.analyses .action-icon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  }
  
  .action-content h5 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  .action-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
  }
  
  /* Cards modernes pour RDV et dossiers */
  .modern-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .modern-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
  }
  
  .modern-card-header {
    padding: 1.5rem;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: between;
    gap: 0.5rem;
    font-weight: 600;
  }
  
  .rdv-card .modern-card-header {
    background: linear-gradient(135deg, #10b981, #059669);
  }
  
  .dossiers-card .modern-card-header {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  }
  
  .infirmiers-card .modern-card-header {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  }
  
  .modern-card-body {
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
  }
  
  /* Liste d'éléments stylisée */
  .styled-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .styled-list-item {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-left: 4px solid #10b981;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .styled-list-item:hover {
    background: #e6fffa;
    transform: translateX(4px);
  }
  
  .styled-list-item:last-child {
    margin-bottom: 0;
  }
  
  .patient-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .patient-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    flex-shrink: 0;
  }
  
  .patient-details h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #1f2937;
  }
  
  .patient-meta {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
  }
  
  /* Filtres RDV modernes */
  .rdv-filters {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    justify-content: center;
  }
  
  .filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 20px;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .filter-btn:hover {
    border-color: #10b981;
    color: #10b981;
  }
  
  .filter-btn.active {
    background: #10b981;
    border-color: #10b981;
    color: white;
  }
  
  /* Actions boutons stylisés */
  .action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    text-decoration: none;
  }
  
  .btn-action:hover {
    transform: scale(1.1);
  }
  
  .btn-primary-action {
    background: #3b82f6;
    color: white;
  }
  
  .btn-success-action {
    background: #10b981;
    color: white;
  }
  
  .btn-warning-action {
    background: #f59e0b;
    color: white;
  }
  
  .btn-secondary-action {
    background: #6b7280;
    color: white;
  }
  
  /* États vides stylisés */
  .empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6b7280;
  }
  
  .empty-state i {
    font-size: 2.5rem;
    color: #d1d5db;
    margin-bottom: 1rem;
  }
  
  .empty-state h5 {
    color: #4b5563;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  
  .empty-state p {
    margin: 0;
    font-size: 0.875rem;
  }
  
  /* Styles spéciaux pour accès rapide */
  .quick-access-item {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border-left-color: #3b82f6;
  }
  
  .quick-access-btn {
    display: flex;
    align-items: center;
  }
  
  .diagnosis-count {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    font-size: 0.75rem;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-weight: 500;
    margin-left: 0.5rem;
  }
  
  /* Badges compteurs */
  .modern-card-header .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
  }
  
  
  /* Responsive */
  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    
    .actions-grid {
      grid-template-columns: 1fr;
    }
    
    .stat-number {
      font-size: 2rem;
    }
    
    .rdv-filters {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

    {{-- Header moderne avec gradient --}}
    <div class="medecin-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1"><i class="bi bi-heart-pulse me-2"></i>Dr. {{ auth()->user()->name }}</h2>
          <p class="mb-0 opacity-75">{{ auth()->user()->specialite ?? 'Médecin généraliste' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <input type="text" id="searchMedecin" class="form-control" placeholder="Rechercher un patient..." style="max-width: 250px;">
          <span class="badge bg-white text-dark px-3 py-2">
            <i class="bi bi-clock me-1"></i>{{ now()->format('H:i') }}
          </span>
        </div>
      </div>
    </div>

    {{-- KPIs Statistiques --}}
    <div class="stats-grid">
      <div class="stat-card rdv-confirme">
        <div class="stat-icon" style="background: #10b981;">
          <i class="bi bi-calendar-check"></i>
        </div>
        <div class="stat-number">{{ $stats['aConsulter'] ?? 0 }}</div>
        <div class="stat-label">À Consulter</div>
      </div>
      
      <div class="stat-card rdv-attente">
        <div class="stat-icon" style="background: #f59e0b;">
          <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-number">{{ $stats['rdvEnAttente'] ?? 0 }}</div>
        <div class="stat-label">En Attente</div>
      </div>
      
      <div class="stat-card patients-traites">
        <div class="stat-icon" style="background: #3b82f6;">
          <i class="bi bi-person-check"></i>
        </div>
        <div class="stat-number">{{ $stats['consultesCeMois'] ?? 0 }}</div>
        <div class="stat-label">Patients traités</div>
      </div>
      
      <div class="stat-card infirmiers">
        <div class="stat-icon" style="background: #8b5cf6;">
          <i class="bi bi-people"></i>
        </div>
        <div class="stat-number">{{ $medecin->nurses->count() }}</div>
        <div class="stat-label">Infirmiers</div>
      </div>
    </div>

    {{-- Actions rapides modernes --}}
    <div class="actions-grid">
      <a href="{{ route('medecin.consultations') }}" class="action-card consultations">
        <div class="action-icon">
          <i class="bi bi-clipboard2-pulse"></i>
        </div>
        <div class="action-content">
          <h5>Consultations</h5>
          <p>Gérer les consultations en cours et à venir</p>
        </div>
      </a>
      
      <a href="{{ route('medecin.ordonnances') }}" class="action-card ordonnances">
        <div class="action-icon">
          <i class="bi bi-prescription2"></i>
        </div>
        <div class="action-content">
          <h5>Ordonnances</h5>
          <p>Rédiger et gérer les prescriptions médicales</p>
        </div>
      </a>
      
      <a href="{{ route('medecin.dossierpatient') }}" class="action-card dossiers">
        <div class="action-icon">
          <i class="bi bi-folder2-open"></i>
        </div>
        <div class="action-content">
          <h5>Dossiers Patients</h5>
          <p>Consulter et mettre à jour les dossiers médicaux</p>
        </div>
      </a>
      
      <a href="{{ route('medecin.analyses.index') }}" class="action-card analyses">
        <div class="action-icon">
          <i class="bi bi-clipboard-data"></i>
        </div>
        <div class="action-content">
          <h5>Analyses</h5>
          <p>Demander et suivre les analyses médicales</p>
        </div>
      </a>
    </div>

    <div class="row g-4">
      {{-- RDV confirmés à venir --}}
      <div class="col-md-6">
        <div class="modern-card rdv-card">
          <div class="modern-card-header">
            <i class="bi bi-calendar-check"></i>
            <span>RDV confirmés à venir</span>
            <span class="badge bg-white text-success ms-auto">{{ ($upcomingRdv ?? collect())->count() }}</span>
          </div>
          <div class="modern-card-body">
            @if(($upcomingRdv ?? collect())->isEmpty())
              <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h5>Aucun RDV programmé</h5>
                <p>Votre agenda est libre pour le moment.</p>
              </div>
            @else
              <div class="rdv-filters">
                <button class="filter-btn" data-rdv-filter="day">
                  <i class="bi bi-calendar-day"></i> Aujourd'hui
                </button>
                <button class="filter-btn" data-rdv-filter="week">
                  <i class="bi bi-calendar-week"></i> Cette semaine
                </button>
                <button class="filter-btn active" data-rdv-filter="all">
                  <i class="bi bi-list-task"></i> Tous
                </button>
              </div>
              
              <ul id="rdv-upcoming-list" class="styled-list">
                @foreach($upcomingRdv as $rdv)
                  <li class="styled-list-item" data-date="{{ \Carbon\Carbon::parse($rdv->date)->toDateString() }}" data-dt="{{ \Carbon\Carbon::parse(($rdv->date ?? '') . ' ' . ($rdv->heure ?? '00:00'))->format('Y-m-d\TH:i') }}">
                    <div class="patient-info">
                      <div class="patient-avatar">
                        {{ strtoupper(substr($rdv->patient->nom ?? 'P', 0, 1)) }}
                      </div>
                      <div class="patient-details">
                        <h6>
                          <a href="{{ route('medecin.patients.show', ['patientId' => optional($rdv->patient)->id]) }}" class="text-decoration-none text-dark">
                            {{ $rdv->patient->nom ?? ($rdv->patient->user->name ?? '—') }} {{ $rdv->patient->prenom ?? '' }}
                          </a>
                        </h6>
                        <p class="patient-meta">
                          <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }} à {{ $rdv->heure }}
                        </p>
                      </div>
                    </div>
                    
                    <div class="action-buttons">
                      <a href="{{ route('medecin.patients.show', ['patientId' => optional($rdv->patient)->id]) }}" class="btn-action btn-primary-action" title="Ouvrir dossier">
                        <i class="bi bi-folder2-open"></i>
                      </a>
                      <a href="{{ route('medecin.consultations', ['patient_id' => optional($rdv->patient)->id, 'date_time' => \Carbon\Carbon::parse(($rdv->date ?? '') . ' ' . ($rdv->heure ?? '00:00'))->format('Y-m-d\TH:i')]) }}" class="btn-action btn-success-action" title="Créer consultation">
                        <i class="bi bi-clipboard-plus"></i>
                      </a>
                      <a href="{{ route('medecin.ordonnances', ['patient_id' => optional($rdv->patient)->id]) }}" class="btn-action btn-warning-action" title="Rédiger ordonnance">
                        <i class="bi bi-prescription2"></i>
                      </a>
                      @php($isConsulted = in_array(strtolower($rdv->statut), ['terminé','termine','terminée','terminee']))
                      @if(!$isConsulted)
                        <form method="POST" action="{{ route('medecin.rdv.markConsulted', $rdv->id) }}" class="d-inline">
                          @csrf
                          <button type="submit" class="btn-action btn-secondary-action" title="Marquer consulté">
                            <i class="bi bi-check2-circle"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>

      {{-- Dossiers récents consultés --}}
      <div class="col-md-6">
        <div class="modern-card dossiers-card">
          <div class="modern-card-header">
            <i class="bi bi-clock-history"></i>
            <span>Dossiers récents consultés</span>
            <span class="badge bg-white text-primary ms-auto">{{ ($recentPatients ?? collect())->count() }}</span>
          </div>
          <div class="modern-card-body">
            @if(($recentPatients ?? collect())->isEmpty())
              <div class="empty-state">
                <i class="bi bi-folder2-open"></i>
                <h5>Aucun dossier consulté</h5>
                <p>Vos consultations récentes apparaîtront ici.</p>
              </div>
            @else
              <ul class="styled-list recent-patients-list">
                @foreach($recentPatients as $patient)
                  <li class="styled-list-item quick-access-item">
                    <div class="patient-info">
                      <div class="patient-avatar">
                        {{ strtoupper(substr($patient->nom ?? 'P', 0, 1)) }}
                      </div>
                      <div class="patient-details">
                        <h6>
                          <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="text-decoration-none text-dark">
                            {{ $patient->nom }} {{ $patient->prenom }}
                          </a>
                        </h6>
                        <p class="patient-meta">
                          <i class="bi bi-eye me-1"></i>Consulté récemment
                        </p>
                      </div>
                    </div>
                    
                    <div class="quick-access-btn">
                      <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn-action btn-primary-action" title="Ouvrir le dossier">
                        <i class="bi bi-arrow-right-circle"></i>
                      </a>
                    </div>
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
            <div class="card text-center shadow-sm h-100">
              <div class="card-body py-3">
                <div class="text-muted small">À consulter (RDV confirmés)</div>
                <div class="display-6">{{ $stats['aConsulter'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
              <div class="card-body py-3">
                <div class="text-muted small">RDV en attente</div>
                <div class="display-6">{{ $stats['rdvEnAttente'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
              <div class="card-body py-3">
                <div class="text-muted small">Patients traités (mois)</div>
                <div class="display-6">{{ $stats['consultesCeMois'] ?? 0 }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-12">
            <div class="card shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span>Infirmiers affectés</span>
                <span class="badge bg-light text-dark border">{{ $medecin->nurses->count() }}</span>
              </div>
              <div class="card-body">
                @if(($medecin->nurses ?? collect())->isEmpty())
                  <div class="text-muted">Aucun infirmier affecté</div>
                @else
                  <div class="row g-2">
                    @foreach($medecin->nurses as $n)
                      <div class="col-lg-4 col-md-6">
                        <div class="d-flex justify-content-between align-items-center border rounded p-2">
                          <div>
                            <div class="fw-semibold">{{ $n->name }}</div>
                            <div class="small text-muted">{{ $n->pro_phone ?? '—' }}</div>
                          </div>
                          <div>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('chat.index', ['partner_id' => $n->id]) }}" title="Envoyer un message"><i class="bi bi-chat"></i></a>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
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

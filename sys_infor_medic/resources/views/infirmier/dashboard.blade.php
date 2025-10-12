@extends('layouts.app')

@section('content')
<style>
  /* Dashboard infirmier moderne */
  body > .container { max-width: 1500px !important; }
  .infirmier-header { 
    position: sticky; 
    top: 0; 
    background: #fff; 
    z-index: 10; 
    padding-top: .25rem; 
    border-bottom: 1px solid rgba(0,0,0,.05); 
  }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  
  /* Onglets modernes */
  .infirmier-tabs { 
    display: flex; 
    flex-wrap: nowrap; 
    justify-content: flex-start; 
    gap: .5rem; 
  }
  .infirmier-tabs .nav-link { 
    flex: 0 0 auto; 
    min-width: 180px; 
    text-align: center; 
    padding: .75rem 1rem; 
    border: 0; 
    border-bottom: 3px solid transparent; 
    white-space: nowrap; 
    color: #27ae60; 
    font-weight: 600; 
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
    margin-bottom: -1px;
    transition: all 0.3s ease;
  }
  .infirmier-tabs .nav-link.active { 
    border-bottom-color: #27ae60; 
    color: #145a32; 
    background: #ffffff;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
  }
  .infirmier-tabs .nav-link:hover:not(.active) {
    background: #e9ecef;
    color: #1e8449;
  }
  
  /* Cards modernes */
  .modern-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
  }
  .modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  
  .modern-card-header {
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 600;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .modern-card-body {
    padding: 1.25rem;
  }
  
  /* Couleurs spécifiques */
  .card-medecins .modern-card-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
  }
  .card-suivis .modern-card-header {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
  }
  .card-rdv .modern-card-header {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
  }
  .card-dossiers .modern-card-header {
    background: linear-gradient(135deg, #ffecd2, #fcb69f);
    color: #8b4513;
  }
  
  /* Liste d'éléments */
  .item-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .item-list li {
    padding: 12px;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 8px;
    border-left: 4px solid #27ae60;
    transition: all 0.2s ease;
  }
  
  .item-list li:hover {
    background: #e9f7ef;
    transform: translateX(4px);
  }
  
  .item-list li:last-child {
    margin-bottom: 0;
  }
  
  .item-header {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
  }
  
  .item-meta {
    font-size: 0.875rem;
    color: #6c757d;
  }
  
  /* Actions rapides */
  .action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
  }
  
  .action-card {
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
  }
  
  .action-card:hover {
    border-color: #27ae60;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(39, 174, 96, 0.15);
    text-decoration: none;
  }
  
  .action-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
    flex-shrink: 0;
  }
  
  .action-content h5 {
    margin: 0 0 0.25rem 0;
    color: #2c3e50;
    font-weight: 600;
  }
  
  .action-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
  }
  
  .action-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #27ae60;
    color: white;
    border-radius: 20px;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
  }
  
  /* Stats Cards */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    text-align: center;
    border-left: 4px solid #27ae60;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #27ae60;
    margin-bottom: 0.5rem;
  }
  
  .stat-label {
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.875rem;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .infirmier-tabs {
      flex-wrap: wrap;
      gap: 0.25rem;
    }
    .infirmier-tabs .nav-link {
      min-width: 120px;
      font-size: 0.875rem;
      padding: 0.5rem 0.75rem;
    }
    .action-grid {
      grid-template-columns: 1fr;
    }
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
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

    <div class="infirmier-header d-flex align-items-center justify-content-between mb-3">
      <h2 class="mb-0 text-success"><i class="bi bi-clipboard2-pulse me-2"></i>Dashboard Infirmier</h2>
      <div class="d-flex align-items-center gap-2">
        <input type="text" id="searchInfirmier" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
        <span class="badge bg-success">{{ Auth::user()->name }}</span>
      </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="stats-grid mb-4">
      <div class="stat-card">
        <div class="stat-number">{{ ($infirmier->doctors ?? collect())->count() }}</div>
        <div class="stat-label">Médecins Affectés</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ $suivis->count() ?? 0 }}</div>
        <div class="stat-label">Suivis en Cours</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ ($upcomingRdv ?? collect())->count() }}</div>
        <div class="stat-label">RDV à Venir</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ $dossiers->count() ?? 0 }}</div>
        <div class="stat-label">Dossiers en Attente</div>
      </div>
    </div>

    {{-- Navigation par onglets --}}
    <ul class="nav nav-tabs infirmier-tabs mb-3" id="infirmierTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="medecins-tab" data-bs-toggle="tab" data-bs-target="#medecins" type="button" role="tab" aria-controls="medecins" aria-selected="true">
          <i class="bi bi-person-badge me-1"></i> Médecins Affectés
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="suivis-tab" data-bs-toggle="tab" data-bs-target="#suivis" type="button" role="tab" aria-controls="suivis" aria-selected="false">
          <i class="bi bi-heart-pulse me-1"></i> Suivis en Cours
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="rdv-tab" data-bs-toggle="tab" data-bs-target="#rdv" type="button" role="tab" aria-controls="rdv" aria-selected="false">
          <i class="bi bi-calendar-check me-1"></i> Rendez-vous
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="dossiers-tab" data-bs-toggle="tab" data-bs-target="#dossiers" type="button" role="tab" aria-controls="dossiers" aria-selected="false">
          <i class="bi bi-folder-open me-1"></i> Dossiers à MAJ
        </button>
      </li>
    </ul>

    <div class="tab-content" id="infirmierTabContent">

      {{-- Onglet Médecins Affectés --}}
      <div class="tab-pane fade show active" id="medecins" role="tabpanel" aria-labelledby="medecins-tab">
        <div class="modern-card card-medecins">
          <div class="modern-card-header">
            <i class="bi bi-person-badge"></i>
            <span>Médecins avec qui vous travaillez</span>
            <span class="badge bg-white text-primary ms-auto">{{ ($infirmier->doctors ?? collect())->count() }}</span>
          </div>
          <div class="modern-card-body">
            @if(($infirmier->doctors ?? collect())->isEmpty())
              <div class="text-center py-4">
                <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Aucun médecin affecté</h5>
                <p class="text-muted">Contactez l'administration pour obtenir des affectations.</p>
              </div>
            @else
              <div class="row g-3">
                @foreach($infirmier->doctors as $doc)
                  <div class="col-lg-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 h-100">
                      <div class="flex-shrink-0 me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                          <i class="bi bi-person-fill"></i>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Dr. {{ $doc->name }}</h6>
                        <p class="text-muted mb-2 small">{{ $doc->specialite ?? 'Spécialité non renseignée' }}</p>
                        <div class="d-flex gap-2">
                          <a href="{{ route('chat.index', ['partner_id' => $doc->id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-chat me-1"></i>Contacter
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Onglet Suivis en Cours --}}
      <div class="tab-pane fade" id="suivis" role="tabpanel" aria-labelledby="suivis-tab">
        <div class="modern-card card-suivis">
          <div class="modern-card-header">
            <i class="bi bi-heart-pulse"></i>
            <span>Patients sous surveillance</span>
            <span class="badge bg-white text-danger ms-auto">{{ $suivis->count() ?? 0 }}</span>
          </div>
          <div class="modern-card-body">
            @if($suivis->isEmpty())
              <div class="text-center py-4">
                <i class="bi bi-heart display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Aucun suivi en cours</h5>
                <p class="text-muted">Tous les patients vont bien !</p>
              </div>
            @else
              <ul class="item-list">
                @foreach($suivis as $suivi)
                  <li>
                    <div class="item-header">
                      {{ $suivi->patient->nom ?? 'Inconnu' }} {{ $suivi->patient->prenom ?? '' }}
                    </div>
                    <div class="item-meta">
                      <span class="me-3"><i class="bi bi-thermometer me-1"></i>Temp : {{ $suivi->temperature ?? 'N/A' }}°C</span>
                      <span><i class="bi bi-heart-pulse me-1"></i>Tension : {{ $suivi->tension ?? 'N/A' }}</span>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>

      {{-- Onglet Rendez-vous --}}
      <div class="tab-pane fade" id="rdv" role="tabpanel" aria-labelledby="rdv-tab">
        <div class="modern-card card-rdv">
          <div class="modern-card-header">
            <i class="bi bi-calendar-check"></i>
            <span>Prochains rendez-vous à suivre</span>
            <span class="badge bg-white text-info ms-auto">{{ ($upcomingRdv ?? collect())->count() }}</span>
          </div>
          <div class="modern-card-body">
            @if(($upcomingRdv ?? collect())->isEmpty())
              <div class="text-center py-4">
                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Aucun RDV programmé</h5>
                <p class="text-muted">Vous n'avez pas de rendez-vous à surveiller.</p>
              </div>
            @else
              <ul class="item-list" id="rdvUpcomingList">
                @foreach($upcomingRdv as $rdv)
                  <li data-date="{{ \Carbon\Carbon::parse($rdv->date)->toDateString() }}" data-dt="{{ \Carbon\Carbon::parse(($rdv->date ?? '') . ' ' . ($rdv->heure ?? '00:00'))->format('Y-m-d\TH:i') }}">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="item-header">
                          {{ optional($rdv->patient)->nom ?? optional(optional($rdv->patient)->user)->name ?? '—' }}
                          {{ optional($rdv->patient)->prenom ?? '' }}
                        </div>
                        <div class="item-meta">
                          <i class="bi bi-person-badge me-1"></i>Avec Dr. {{ optional($rdv->medecin)->name ?? '—' }}
                        </div>
                      </div>
                      <div class="text-end">
                        <div class="badge bg-light text-dark border mb-1">
                          {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }} {{ $rdv->heure }}
                        </div>
                        <br>
                        <span class="badge {{ in_array(strtolower($rdv->statut), ['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array(strtolower($rdv->statut), ['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark') }}">
                          {{ str_replace('_',' ', $rdv->statut ?? '') }}
                        </span>
                      </div>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>

      {{-- Onglet Dossiers à MAJ --}}
      <div class="tab-pane fade" id="dossiers" role="tabpanel" aria-labelledby="dossiers-tab">
        <div class="modern-card card-dossiers">
          <div class="modern-card-header">
            <i class="bi bi-folder-open"></i>
            <span>Dossiers nécessitant une mise à jour</span>
            <span class="badge bg-white text-warning ms-auto">{{ $dossiers->count() ?? 0 }}</span>
          </div>
          <div class="modern-card-body">
            @if($dossiers->isEmpty())
              <div class="text-center py-4">
                <i class="bi bi-folder-check display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Tous les dossiers sont à jour</h5>
                <p class="text-muted">Excellent travail ! Aucune mise à jour en attente.</p>
              </div>
            @else
              <ul class="item-list">
                @foreach($dossiers as $dossier)
                  <li>
                    <div class="item-header">
                      {{ $dossier->patient->nom ?? 'Inconnu' }} {{ $dossier->patient->prenom ?? '' }}
                    </div>
                    <div class="item-meta">
                      <i class="bi bi-clipboard-data me-1"></i>{{ $dossier->observation ?? 'Observation manquante' }}
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Actions rapides modernes --}}
    <div class="mt-5">
      <h4 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Actions Rapides</h4>
      <div class="action-grid">
        <a href="{{ route('suivi.create') }}" class="action-card">
          <div class="action-icon">
            <i class="bi bi-heart-pulse"></i>
          </div>
          <div class="action-content">
            <h5>Saisir un suivi patient</h5>
            <p>Enregistrer les constantes vitales d'un patient</p>
          </div>
          @if(isset($suivis) && $suivis->count() > 0)
            <div class="action-badge">{{ $suivis->count() }}</div>
          @endif
        </a>

        <a href="{{ route('dossier.index') }}" class="action-card">
          <div class="action-icon">
            <i class="bi bi-file-earmark-plus"></i>
          </div>
          <div class="action-content">
            <h5>Nouveau dossier</h5>
            <p>Accéder à la gestion des dossiers patients</p>
          </div>
        </a>

        <a href="{{ route('dossier.index') }}" class="action-card">
          <div class="action-icon">
            <i class="bi bi-folder-open"></i>
          </div>
          <div class="action-content">
            <h5>Mettre à jour dossiers</h5>
            <p>Modifier les informations des dossiers patients</p>
          </div>
          @if(isset($dossiers) && $dossiers->count() > 0)
            <div class="action-badge">{{ $dossiers->count() }}</div>
          @endif
        </a>

        <a href="{{ route('historique.index') }}" class="action-card">
          <div class="action-icon">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="action-content">
            <h5>Historique des soins</h5>
            <p>Consulter l'historique complet des soins</p>
          </div>
        </a>
      </div>
    </div>
        </div>
    </div>
  </div>
</div>
<script>
  (function(){
    // Recherche globale améliorée
    const inp = document.getElementById('searchInfirmier');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      // Filtrer tous les éléments de liste dans tous les onglets
      document.querySelectorAll('.item-list li, .modern-card-body .row .col-lg-6').forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
      });
      
      // Mettre à jour les badges de compteur après filtrage
      updateTabBadges();
    }
    inp?.addEventListener('input', filter);
    
    // Mise à jour des badges d'onglets
    function updateTabBadges() {
      const tabs = [
        { id: 'medecins', selector: '.tab-pane#medecins .col-lg-6:not([style*="display: none"])' },
        { id: 'suivis', selector: '.tab-pane#suivis .item-list li:not([style*="display: none"])' },
        { id: 'rdv', selector: '.tab-pane#rdv .item-list li:not([style*="display: none"])' },
        { id: 'dossiers', selector: '.tab-pane#dossiers .item-list li:not([style*="display: none"])' }
      ];
      
      tabs.forEach(tab => {
        const visible = document.querySelectorAll(tab.selector).length;
        const badge = document.querySelector(`#${tab.id}-tab .badge, #${tab.id} .badge`);
        if (badge && !badge.classList.contains('bg-white')) {
          badge.textContent = visible;
        }
      });
    }
    
    // État de chargement sur les actions rapides
    document.querySelectorAll('.action-card')?.forEach(card => {
      card.addEventListener('click', function(e) {
        // Ajouter un effet visuel de chargement
        const icon = this.querySelector('.action-icon i');
        if (icon) {
          icon.className = 'bi bi-hourglass-split';
          icon.style.animation = 'spin 1s linear infinite';
        }
        
        // Ajouter une classe de chargement
        this.classList.add('loading');
        this.style.pointerEvents = 'none';
      });
    });
    
    // Tri par date/heure croissante pour la liste des RDV
    function sortRdv() {
      const list = document.getElementById('rdvUpcomingList');
      if (!list) return;
      
      const items = Array.from(list.querySelectorAll('li[data-dt]'));
      items.sort((a, b) => {
        const dateA = new Date(a.getAttribute('data-dt'));
        const dateB = new Date(b.getAttribute('data-dt'));
        return dateA - dateB;
      });
      
      items.forEach(li => list.appendChild(li));
    }
    
    // Exécuter le tri au chargement
    sortRdv();
    
    // Ajouter des animations d'entrée pour les onglets
    document.querySelectorAll('#infirmierTab button[data-bs-toggle="tab"]').forEach(tabBtn => {
      tabBtn.addEventListener('shown.bs.tab', function(e) {
        const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
        if (targetPane) {
          targetPane.style.opacity = '0';
          targetPane.style.transform = 'translateY(20px)';
          
          // Animation d'entrée
          setTimeout(() => {
            targetPane.style.transition = 'all 0.3s ease';
            targetPane.style.opacity = '1';
            targetPane.style.transform = 'translateY(0)';
          }, 50);
        }
      });
    });
    
    // Ajout d'un style de rotation pour l'icône de chargement
    const style = document.createElement('style');
    style.textContent = `
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      .action-card.loading {
        opacity: 0.7;
        transform: scale(0.98);
      }
    `;
    document.head.appendChild(style);
    
  })();
</script>
@endsection

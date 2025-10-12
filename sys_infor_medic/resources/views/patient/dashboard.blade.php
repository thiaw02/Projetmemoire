@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  
  <div class="col-lg-9">
    {{-- Header compact pour espace patient --}}
    <div class="patient-compact-header">
      <div class="header-content">
        <div class="page-title">
          <h1>Espace Patient</h1>
          <p>Gérez vos rendez-vous et consultez votre dossier médical</p>
        </div>
        <div class="header-actions">
          <a href="{{ route('patient.payments.index') }}" class="btn-quick-action payment" title="Gérer les paiements">
            <i class="bi bi-credit-card"></i>
            <span class="d-none d-md-inline">Paiements</span>
          </a>
        </div>
      </div>
    </div>

    {{-- Messages de session --}}
    @if(session('success'))
      <div class="alert alert-success alert-modern">
        <i class="bi bi-check-circle"></i>
        {{ session('success') }}
      </div>
    @endif

    @if($preferences['show_statistics'])
    {{-- Statistiques rapides --}}
<div class="patient-stats {{ $preferences['compact_mode'] ? 'compact-mode' : '' }}">
  <div class="stat-card next-appointment">
    <div class="stat-icon">
      <i class="bi bi-calendar-check"></i>
    </div>
    <div class="stat-content">
      @if($nextRdv)
        <div class="stat-value">{{ \Carbon\Carbon::parse($nextRdv->date)->format('d/m') }}</div>
        <div class="stat-label">Prochain RDV</div>
        <div class="stat-detail">{{ $nextRdv->heure }} - Dr. {{ $nextRdv->medecin->name ?? 'TBD' }}</div>
      @else
        <div class="stat-value">—</div>
        <div class="stat-label">Aucun RDV</div>
        <div class="stat-detail">Planifiez votre prochain rendez-vous</div>
      @endif
    </div>
    <div class="stat-action">
      <button class="btn-stat" onclick="switchToTab('rdv')">
        <i class="bi bi-plus"></i>
      </button>
    </div>
  </div>
  
  <div class="stat-card consultations-count">
    <div class="stat-icon">
      <i class="bi bi-file-medical"></i>
    </div>
    <div class="stat-content">
      <div class="stat-value">{{ $stats['totalConsultations'] ?? 0 }}</div>
      <div class="stat-label">Consultations</div>
      <div class="stat-detail">Total depuis votre inscription</div>
    </div>
    <div class="stat-trend positive">
      <i class="bi bi-arrow-up"></i>
    </div>
  </div>
  
  <div class="stat-card pending-rdv">
    <div class="stat-icon">
      <i class="bi bi-clock"></i>
    </div>
    <div class="stat-content">
      <div class="stat-value">{{ $stats['rdvEnAttente'] ?? 0 }}</div>
      <div class="stat-label">En attente</div>
      <div class="stat-detail">Rendez-vous à confirmer</div>
    </div>
    <div class="stat-action">
      <button class="btn-stat" onclick="switchToTab('mesrdv')">
        <i class="bi bi-eye"></i>
      </button>
    </div>
  </div>
</div>
@endif
{{-- Navigation par onglets moderne --}}
<div class="modern-tabs">
  <div class="tabs-container">
    <button class="tab-btn {{ $preferences['default_tab'] === 'rdv' ? 'active' : '' }}" data-tab="rdv" onclick="switchToTab('rdv')">
      <i class="bi bi-calendar-plus"></i>
      <span>Nouveau RDV</span>
    </button>
    <button class="tab-btn {{ $preferences['default_tab'] === 'mesrdv' ? 'active' : '' }}" data-tab="mesrdv" onclick="switchToTab('mesrdv')">
      <i class="bi bi-calendar-event"></i>
      <span>Mes RDV</span>
    </button>
    <button class="tab-btn {{ $preferences['default_tab'] === 'dossier' ? 'active' : '' }}" data-tab="dossier" onclick="switchToTab('dossier')">
      <i class="bi bi-folder-fill"></i>
      <span>Dossier médical</span>
    </button>
    <button class="tab-btn {{ $preferences['default_tab'] === 'historique' ? 'active' : '' }}" data-tab="historique" onclick="switchToTab('historique')">
      <i class="bi bi-clock-history"></i>
      <span>Historique</span>
    </button>
    <button class="tab-btn {{ $preferences['default_tab'] === 'evaluations' ? 'active' : '' }}" data-tab="evaluations" onclick="switchToTab('evaluations')">
      <i class="bi bi-star"></i>
      <span>Évaluations</span>
      @if(isset($evaluationsCount) && $evaluationsCount > 0)
        <span class="notification-badge">{{ $evaluationsCount }}</span>
      @endif
    </button>
  </div>
</div>

{{-- Contenu des onglets --}}
<div class="tabs-content">
  {{-- Onglet Nouveau RDV --}}
  <div id="rdv" class="tab-panel {{ $preferences['default_tab'] === 'rdv' ? 'active' : '' }}">
    <div class="appointment-section">
      <div class="section-header">
        <h2>Prendre un rendez-vous</h2>
        <p>Choisissez votre médecin et votre créneau préféré</p>
      </div>
      
      <div class="appointment-grid">
        <div class="doctors-selection">
          <h3>Nos médecins disponibles</h3>
          <div class="doctors-list">
            @foreach($medecins as $medecin)
              <div class="doctor-card" onclick="selectDoctor({{ $medecin->id }})">
                <div class="doctor-avatar">
                  {{ substr($medecin->name, 0, 1) }}
                </div>
                <div class="doctor-info">
                  <h4>Dr. {{ $medecin->name }}</h4>
                  <p>{{ $medecin->specialite ?? 'Médecin généraliste' }}</p>
                  <div class="doctor-rating">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="bi bi-star-fill"></i>
                    @endfor
                    <span>4.8</span>
                  </div>
                </div>
                <div class="doctor-status available">
                  <i class="bi bi-circle-fill"></i>
                  Disponible
                </div>
              </div>
            @endforeach
          </div>
        </div>
        
        <div class="appointment-form-section">
          <div class="form-card">
            <h3>Détails du rendez-vous</h3>
            <form method="POST" action="{{ route('patient.storeRendez') }}" id="modernRdvForm">
              @csrf
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-calendar-day"></i>
                  Date souhaitée
                </label>
                <input type="date" name="date" class="form-control modern-input" required>
                <small class="form-hint">Sélectionnez une date dans les prochaines semaines</small>
              </div>
              
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-clock"></i>
                  Heure préférée
                </label>
                <div class="time-slots">
                  <input type="radio" name="heure" value="08:00" id="time-08">
                  <label for="time-08" class="time-slot">08:00</label>
                  
                  <input type="radio" name="heure" value="09:00" id="time-09">
                  <label for="time-09" class="time-slot">09:00</label>
                  
                  <input type="radio" name="heure" value="10:00" id="time-10">
                  <label for="time-10" class="time-slot">10:00</label>
                  
                  <input type="radio" name="heure" value="11:00" id="time-11">
                  <label for="time-11" class="time-slot">11:00</label>
                  
                  <input type="radio" name="heure" value="14:00" id="time-14">
                  <label for="time-14" class="time-slot">14:00</label>
                  
                  <input type="radio" name="heure" value="15:00" id="time-15">
                  <label for="time-15" class="time-slot">15:00</label>
                  
                  <input type="radio" name="heure" value="16:00" id="time-16">
                  <label for="time-16" class="time-slot">16:00</label>
                  
                  <input type="radio" name="heure" value="17:00" id="time-17">
                  <label for="time-17" class="time-slot">17:00</label>
                </div>
              </div>
              
              <input type="hidden" name="medecin_id" id="selected-medecin">
              
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-chat-text"></i>
                  Motif de consultation
                </label>
                <textarea name="motif" class="form-control modern-input" rows="4" 
                          placeholder="Décrivez brièvement le motif de votre consultation..." required></textarea>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn-submit-appointment">
                  <i class="bi bi-send"></i>
                  Envoyer la demande
                </button>
                <button type="button" class="btn-draft" onclick="saveDraft()">
                  <i class="bi bi-bookmark"></i>
                  Sauvegarder
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Onglet Dossier médical --}}
  <div id="dossier" class="tab-panel {{ $preferences['default_tab'] === 'dossier' ? 'active' : '' }}">
    <div class="medical-record-section">
      <div class="section-header">
        <h2>Mon dossier médical</h2>
        <button class="btn-download-record">
          <i class="bi bi-download"></i>
          Télécharger complet
        </button>
      </div>
      
      <div class="medical-grid">
        <div class="medical-category prescriptions">
          <div class="category-header">
            <div class="category-icon">
              <i class="bi bi-prescription2"></i>
            </div>
            <h3>Ordonnances</h3>
            <span class="count">{{ count($ordonnances) }}</span>
          </div>
          
          <div class="category-content">
            @forelse($ordonnances as $ordonnance)
              <div class="medical-item">
                <div class="item-date">{{ optional($ordonnance->created_at)->format('d/m/Y') }}</div>
                <div class="item-content">
                  @if($ordonnance->medicaments)
                    @foreach(explode("\n", $ordonnance->medicaments) as $medicament)
                      @if(trim($medicament))
                        <div class="medication">{{ trim($medicament) }}</div>
                      @endif
                    @endforeach
                  @endif
                  @if($ordonnance->dosage)
                    <div class="dosage">Dosage: {{ $ordonnance->dosage }}</div>
                  @endif
                </div>
                <div class="item-actions">
                  <a href="{{ route('patient.ordonnances.download', $ordonnance->id) }}" class="action-link">
                    <i class="bi bi-download"></i>
                  </a>
                  <button class="action-link" onclick="resendPrescription({{ $ordonnance->id }})">
                    <i class="bi bi-envelope"></i>
                  </button>
                </div>
              </div>
            @empty
              <div class="empty-category">
                <i class="bi bi-prescription2"></i>
                <span>Aucune ordonnance</span>
              </div>
            @endforelse
          </div>
        </div>
        
        <div class="medical-category analyses">
          <div class="category-header">
            <div class="category-icon">
              <i class="bi bi-graph-up"></i>
            </div>
            <h3>Analyses</h3>
            <span class="count">{{ count($analyses) }}</span>
          </div>
          
          <div class="category-content">
            @forelse($analyses as $analyse)
              <div class="medical-item">
                <div class="item-date">{{ optional($analyse->date_analyse)->format('d/m/Y') }}</div>
                <div class="item-content">
                  <div class="analysis-type">{{ $analyse->type_analyse ?? $analyse->type }}</div>
                  <div class="analysis-result">{{ $analyse->resultats ?? $analyse->resultat }}</div>
                </div>
                <div class="result-status normal">
                  <i class="bi bi-check-circle"></i>
                </div>
              </div>
            @empty
              <div class="empty-category">
                <i class="bi bi-graph-up"></i>
                <span>Aucune analyse</span>
              </div>
            @endforelse
          </div>
        </div>
        
        <div class="medical-category consultations">
          <div class="category-header">
            <div class="category-icon">
              <i class="bi bi-chat-medical"></i>
            </div>
            <h3>Consultations</h3>
            <span class="count">{{ count($consultations) }}</span>
          </div>
          
          <div class="category-content">
            @forelse($consultations as $consultation)
              <div class="medical-item">
                <div class="item-date">{{ optional($consultation->date_consultation)->format('d/m/Y') }}</div>
                <div class="item-content">
                  <div class="consultation-doctor">Dr. {{ $consultation->medecin->name ?? 'TBD' }}</div>
                  <div class="consultation-diagnosis">{{ $consultation->diagnostic }}</div>
                </div>
                <div class="item-actions">
                  <button class="action-link" onclick="viewConsultationDetails({{ $consultation->id }})">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            @empty
              <div class="empty-category">
                <i class="bi bi-chat-medical"></i>
                <span>Aucune consultation</span>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Onglet Mes RDV --}}
  <div id="mesrdv" class="tab-panel {{ $preferences['default_tab'] === 'mesrdv' ? 'active' : '' }}">
    <div class="appointments-list-section">
      <div class="section-header">
        <h2>Mes rendez-vous</h2>
        <div class="header-filters">
          <button class="filter-btn active" data-filter="all" onclick="filterAppointments('all')">
            <i class="bi bi-list-ul"></i>
            <span>Tous</span>
          </button>
          <button class="filter-btn" data-filter="en_attente" onclick="filterAppointments('en_attente')">
            <i class="bi bi-clock"></i>
            <span>En attente</span>
          </button>
          <button class="filter-btn" data-filter="confirme" onclick="filterAppointments('confirme')">
            <i class="bi bi-check-circle"></i>
            <span>Confirmés</span>
          </button>
          <button class="filter-btn" data-filter="termine" onclick="filterAppointments('termine')">
            <i class="bi bi-check-square"></i>
            <span>Terminés</span>
          </button>
        </div>
      </div>
      
      <div class="appointments-grid">
        @forelse($rendezVous as $rdv)
          <div class="appointment-card {{ strtolower($rdv->statut) }}" data-status="{{ strtolower($rdv->statut) }}">
            <div class="appointment-date">
              <div class="date-day">{{ \Carbon\Carbon::parse($rdv->date)->format('d') }}</div>
              <div class="date-month">{{ \Carbon\Carbon::parse($rdv->date)->format('M') }}</div>
              <div class="date-year">{{ \Carbon\Carbon::parse($rdv->date)->format('Y') }}</div>
            </div>
            
            <div class="appointment-details">
              <div class="appointment-time">
                <i class="bi bi-clock"></i>
                {{ $rdv->heure }}
              </div>
              <div class="appointment-doctor">
                <i class="bi bi-person-badge"></i>
                Dr. {{ $rdv->medecin->name ?? 'TBD' }}
              </div>
              <div class="appointment-reason">
                {{ Str::limit($rdv->motif, 50) }}
              </div>
            </div>
            
            <div class="appointment-status">
              <span class="status-badge {{ strtolower($rdv->statut) }}">
                @switch(strtolower($rdv->statut))
                  @case('confirme')
                  @case('confirmé')
                    <i class="bi bi-check-circle"></i>
                    Confirmé
                    @break
                  @case('annule')
                  @case('annulé')
                    <i class="bi bi-x-circle"></i>
                    Annulé
                    @break
                  @default
                    <i class="bi bi-clock"></i>
                    En attente
                @endswitch
              </span>
            </div>
            
            <div class="appointment-actions">
              @if(strtolower($rdv->statut) === 'en_attente')
                <button class="action-btn cancel" onclick="cancelAppointment({{ $rdv->id }})">
                  <i class="bi bi-x"></i>
                </button>
                <button class="action-btn modify" onclick="modifyAppointment({{ $rdv->id }})">
                  <i class="bi bi-pencil"></i>
                </button>
              @endif
              <button class="action-btn details" onclick="viewAppointmentDetails({{ $rdv->id }})">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <h3>Aucun rendez-vous</h3>
            <p>Vous n'avez pas encore de rendez-vous planifié</p>
            <button class="btn-primary" onclick="switchToTab('rdv')">
              Prendre un rendez-vous
            </button>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Onglet Historique --}}
  <div id="historique" class="tab-panel {{ $preferences['default_tab'] === 'historique' ? 'active' : '' }}">
    <div class="history-section">
      <div class="section-header">
        <h2>Historique médical</h2>
        <div class="timeline-controls">
          <button class="timeline-btn active" data-period="all">Tout</button>
          <button class="timeline-btn" data-period="year">Cette année</button>
          <button class="timeline-btn" data-period="month">Ce mois</button>
        </div>
      </div>
      
      <div class="medical-timeline">
        @forelse($consultations->sortByDesc('date_consultation') as $consultation)
          <div class="timeline-item">
            <div class="timeline-marker">
              <i class="bi bi-circle-fill"></i>
            </div>
            <div class="timeline-content">
              <div class="timeline-date">{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d F Y') }}</div>
              <div class="timeline-title">Consultation avec Dr. {{ $consultation->medecin->name ?? 'TBD' }}</div>
              <div class="timeline-description">{{ $consultation->diagnostic }}</div>
              @if($consultation->ordonnances)
                <div class="timeline-actions">
                  <span class="timeline-tag">
                    <i class="bi bi-prescription2"></i>
                    Ordonnance prescrite
                  </span>
                </div>
              @endif
            </div>
          </div>
        @empty
          <div class="empty-timeline">
            <i class="bi bi-clock-history"></i>
            <h3>Aucun historique</h3>
            <p>Votre historique médical apparaîtra ici après vos consultations</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
</div>

<style>
  /* Variables CSS dynamiques basées sur les préférences */
  :root {
    @php
      $themeColors = [
        'blue' => ['primary' => '#3b82f6', 'secondary' => '#1e40af'],
        'purple' => ['primary' => '#8b5cf6', 'secondary' => '#7c3aed'],
        'green' => ['primary' => '#10b981', 'secondary' => '#059669'],
        'orange' => ['primary' => '#f59e0b', 'secondary' => '#d97706'],
        'red' => ['primary' => '#ef4444', 'secondary' => '#dc2626'],
        'pink' => ['primary' => '#ec4899', 'secondary' => '#db2777']
      ];
      $currentTheme = $themeColors[$preferences['theme_color']] ?? $themeColors['blue'];
    @endphp
    
    --patient-primary: {{ $currentTheme['primary'] }};
    --patient-secondary: {{ $currentTheme['secondary'] }};
    --patient-success: #059669;
    --patient-warning: #f59e0b;
    --patient-danger: #dc2626;
    --patient-bg: #f8fafc;
    --patient-card: #ffffff;
    --patient-text: #1f2937;
    --patient-text-light: #6b7280;
    
    /* Vitesses d'animation */
    --animation-speed: {{ $preferences['animation_speed'] === 'slow' ? '0.5s' : ($preferences['animation_speed'] === 'fast' ? '0.15s' : '0.3s') }};
  }
  
  body { background: var(--patient-bg); }
  body > .container { max-width: 1400px !important; }
  
  /* Header patient compact */
  .patient-compact-header {
    background: linear-gradient(135deg, var(--patient-primary) 0%, var(--patient-secondary) 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
  }
  
  .patient-compact-header .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
  }
  
  .page-title h1 {
    margin: 0 0 0.25rem 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
  }
  
  .page-title p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.95rem;
    color: white;
  }
  
  .patient-compact-header .header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }
  
  .btn-quick-action {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.9rem;
  }
  
  .btn-quick-action:hover {
    background: white;
    color: var(--patient-primary);
    transform: translateY(-2px);
  }
  
  .btn-quick-action.payment:hover { color: var(--patient-success); }
  
  /* Alertes modernes */
  .alert-modern {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .alert-modern i {
    font-size: 1.2rem;
  }
  
  /* Statistiques patient */
  .patient-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .patient-stats.compact-mode {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .patient-stats.compact-mode .stat-card {
    padding: 1rem;
  }
  
  .patient-stats.compact-mode .stat-icon {
    width: 45px;
    height: 45px;
    font-size: 18px;
  }
  
  .patient-stats.compact-mode .stat-value {
    font-size: 1.5rem;
  }
  
  .stat-card {
    background: var(--patient-card);
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all var(--animation-speed, 0.3s) ease;
    position: relative;
    overflow: hidden;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--patient-primary);
  }
  
  .next-appointment::before { background: var(--patient-primary); }
  .consultations-count::before { background: var(--patient-success); }
  .pending-rdv::before { background: var(--patient-warning); }
  .health-score::before { background: #8b5cf6; }
  
  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .next-appointment .stat-icon { background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary)); }
  .consultations-count .stat-icon { background: linear-gradient(135deg, var(--patient-success), #047857); }
  .pending-rdv .stat-icon { background: linear-gradient(135deg, var(--patient-warning), #d97706); }
  .health-score .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .stat-content {
    flex: 1;
  }
  
  .stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--patient-text);
    line-height: 1;
    margin-bottom: 0.25rem;
  }
  
  .stat-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--patient-text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
  }
  
  .stat-detail {
    font-size: 0.8rem;
    color: var(--patient-text-light);
  }
  
  .stat-action, .stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
  }
  
  .btn-stat {
    width: 32px;
    height: 32px;
    background: rgba(0, 0, 0, 0.05);
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }
  
  .btn-stat:hover {
    background: var(--patient-primary);
    color: white;
  }
  
  .stat-trend {
    color: var(--patient-success);
    font-size: 1.2rem;
  }
  
  /* Onglets modernes */
  .modern-tabs {
    margin-bottom: 2rem;
  }
  
  .tabs-container {
    background: var(--patient-card);
    padding: 0.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
  }
  
  .tab-btn {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    color: var(--patient-text-light);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all var(--animation-speed, 0.3s) ease;
    white-space: nowrap;
    flex-shrink: 0;
  }
  
  .tab-btn:hover {
    background: rgba(59, 130, 246, 0.1);
    color: var(--patient-primary);
  }
  
  .tab-btn.active {
    background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary));
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  /* Contenu des onglets */
  .tabs-content {
    background: var(--patient-card);
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
  }
  
  .tab-panel {
    display: none;
    padding: 2rem;
  }
  
  .tab-panel.active {
    display: block;
  }
  
  .section-header {
    margin-bottom: 2rem;
  }
  
  .section-header h2 {
    margin: 0 0 0.5rem 0;
    color: var(--patient-text);
    font-weight: 700;
    font-size: 1.5rem;
  }
  
  .section-header p {
    margin: 0;
    color: var(--patient-text-light);
  }
  
  /* Section rendez-vous */
  .appointment-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
  
  .doctors-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .doctor-card {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 16px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .doctor-card:hover, .doctor-card.selected {
    border-color: var(--patient-primary);
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.1);
  }
  
  .doctor-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
  }
  
  .doctor-info {
    flex: 1;
  }
  
  .doctor-info h4 {
    margin: 0 0 0.25rem 0;
    color: var(--patient-text);
    font-weight: 600;
  }
  
  .doctor-info p {
    margin: 0 0 0.5rem 0;
    color: var(--patient-text-light);
    font-size: 0.9rem;
  }
  
  .doctor-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
  }
  
  .doctor-rating i {
    color: #f59e0b;
  }
  
  .doctor-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .doctor-status.available {
    background: #dcfce7;
    color: var(--patient-success);
  }
  
  /* Formulaire de rendez-vous */
  .form-card {
    background: #f8fafc;
    padding: 2rem;
    border-radius: 16px;
  }
  
  .form-card h3 {
    margin: 0 0 1.5rem 0;
    color: var(--patient-text);
    font-weight: 600;
  }
  
  .form-group {
    margin-bottom: 1.5rem;
  }
  
  .form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--patient-text);
    margin-bottom: 0.5rem;
  }
  
  .modern-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s ease;
  }
  
  .modern-input:focus {
    border-color: var(--patient-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
  }
  
  .form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.8rem;
    color: var(--patient-text-light);
  }
  
  .time-slots {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
  }
  
  .time-slots input[type="radio"] {
    display: none;
  }
  
  .time-slot {
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
  }
  
  .time-slot:hover {
    border-color: var(--patient-primary);
    background: rgba(59, 130, 246, 0.05);
  }
  
  .time-slots input[type="radio"]:checked + .time-slot {
    background: var(--patient-primary);
    border-color: var(--patient-primary);
    color: white;
  }
  
  .form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .btn-submit-appointment {
    flex: 1;
    background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary));
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-submit-appointment:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  .btn-draft {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    color: var(--patient-text-light);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-draft:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
  }
  
  /* Liste des rendez-vous */
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .header-filters {
    display: flex;
    gap: 0.5rem;
  }
  
  .filter-btn {
    background: #f8fafc;
    border: 2px solid #e5e7eb;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    font-weight: 500;
    color: var(--patient-text-light);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
    cursor: pointer;
  }
  
  .filter-btn:hover {
    background: rgba(59, 130, 246, 0.05);
    border-color: var(--patient-primary);
    color: var(--patient-primary);
    transform: translateY(-1px);
  }
  
  .filter-btn.active {
    background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary));
    border-color: var(--patient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  }
  
  .filter-btn i {
    font-size: 0.9rem;
  }
  
  .appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  
  .appointment-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    align-items: start;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: relative;
  }
  
  .appointment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border-color: var(--patient-primary);
  }
  
  .appointment-card.confirme, .appointment-card.confirmé {
    border-color: var(--patient-success);
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.02), rgba(5, 150, 105, 0.01));
  }
  
  .appointment-card.en_attente {
    border-color: var(--patient-warning);
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.02), rgba(245, 158, 11, 0.01));
  }
  
  .appointment-card.termine {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.02), rgba(139, 92, 246, 0.01));
  }
  
  .appointment-card.annule, .appointment-card.annulé {
    border-color: var(--patient-text-light);
    opacity: 0.7;
    background: #f9fafb;
  }
  
  /* Indicateur de statut sur le côté */
  .appointment-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 16px 0 0 16px;
    background: #e5e7eb;
  }
  
  .appointment-card.confirme::before,
  .appointment-card.confirmé::before {
    background: var(--patient-success);
  }
  
  .appointment-card.en_attente::before {
    background: var(--patient-warning);
  }
  
  .appointment-card.termine::before {
    background: #8b5cf6;
  }
  
  .appointment-date {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 12px;
  }
  
  .date-day {
    font-size: 2rem;
    font-weight: 800;
    color: var(--patient-primary);
    line-height: 1;
  }
  
  .date-month {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--patient-text-light);
    text-transform: uppercase;
  }
  
  .date-year {
    font-size: 0.8rem;
    color: var(--patient-text-light);
  }
  
  .appointment-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .appointment-time, .appointment-doctor {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--patient-text);
  }
  
  .appointment-reason {
    color: var(--patient-text-light);
    font-size: 0.9rem;
  }
  
  .status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .status-badge.confirme, .status-badge.confirmé {
    background: #dcfce7;
    color: var(--patient-success);
  }
  
  .status-badge.annule, .status-badge.annulé {
    background: #f3f4f6;
    color: var(--patient-text-light);
  }
  
  .status-badge.en_attente {
    background: #fef3c7;
    color: var(--patient-warning);
  }
  
  .appointment-actions {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .action-btn.cancel {
    background: #fee2e2;
    color: var(--patient-danger);
  }
  
  .action-btn.cancel:hover {
    background: var(--patient-danger);
    color: white;
  }
  
  .action-btn.modify {
    background: #fef3c7;
    color: var(--patient-warning);
  }
  
  .action-btn.modify:hover {
    background: var(--patient-warning);
    color: white;
  }
  
  .action-btn.details {
    background: #eff6ff;
    color: var(--patient-primary);
  }
  
  .action-btn.details:hover {
    background: var(--patient-primary);
    color: white;
  }
  
  /* Dossier médical */
  .btn-download-record {
    background: var(--patient-primary);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-download-record:hover {
    background: var(--patient-secondary);
    transform: translateY(-1px);
  }
  
  .medical-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
  }
  
  .medical-category {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .medical-category:hover {
    border-color: var(--patient-primary);
    transform: translateY(-2px);
  }
  
  .category-header {
    background: white;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .category-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--patient-primary), var(--patient-secondary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
  }
  
  .category-header h3 {
    flex: 1;
    margin: 0;
    color: var(--patient-text);
    font-weight: 600;
  }
  
  .count {
    background: var(--patient-primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  .category-content {
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
  }
  
  .medical-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
  }
  
  .medical-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  
  .item-date {
    font-size: 0.8rem;
    color: var(--patient-text-light);
    font-weight: 600;
    white-space: nowrap;
  }
  
  .item-content {
    flex: 1;
    font-size: 0.9rem;
  }
  
  .medication {
    color: var(--patient-text);
    margin-bottom: 0.25rem;
  }
  
  .dosage {
    color: var(--patient-text-light);
    font-size: 0.8rem;
  }
  
  .item-actions {
    display: flex;
    gap: 0.25rem;
  }
  
  .action-link {
    width: 28px;
    height: 28px;
    background: #f3f4f6;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--patient-text-light);
    transition: all 0.2s ease;
    text-decoration: none;
  }
  
  .action-link:hover {
    background: var(--patient-primary);
    color: white;
  }
  
  .empty-category {
    text-align: center;
    padding: 2rem;
    color: var(--patient-text-light);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  .empty-category i {
    font-size: 2rem;
    opacity: 0.5;
  }
  
  /* Timeline historique */
  .timeline-controls {
    display: flex;
    gap: 0.5rem;
  }
  
  .timeline-btn {
    background: #f3f4f6;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    color: var(--patient-text-light);
    transition: all 0.2s ease;
  }
  
  .timeline-btn.active, .timeline-btn:hover {
    background: var(--patient-primary);
    color: white;
  }
  
  .medical-timeline {
    position: relative;
    padding-left: 2rem;
  }
  
  .medical-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
  }
  
  .timeline-item {
    position: relative;
    margin-bottom: 2rem;
  }
  
  .timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    color: var(--patient-primary);
    font-size: 0.6rem;
  }
  
  .timeline-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .timeline-date {
    color: var(--patient-primary);
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .timeline-title {
    font-weight: 600;
    color: var(--patient-text);
    margin-bottom: 0.5rem;
  }
  
  .timeline-description {
    color: var(--patient-text-light);
    margin-bottom: 1rem;
  }
  
  .timeline-tag {
    background: #dcfce7;
    color: var(--patient-success);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  /* États vides */
  .empty-state, .empty-timeline {
    text-align: center;
    padding: 3rem;
    color: var(--patient-text-light);
  }
  
  .empty-state i, .empty-timeline i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
  }
  
  .empty-state h3, .empty-timeline h3 {
    margin: 0 0 0.5rem 0;
    color: var(--patient-text);
  }
  
  .empty-state p, .empty-timeline p {
    margin: 0 0 1.5rem 0;
  }
  
  .btn-primary {
    background: var(--patient-primary);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  
  .btn-primary:hover {
    background: var(--patient-secondary);
    transform: translateY(-1px);
  }
  
  /* Animations pour les filtres */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .appointments-empty-filter {
    padding: 3rem 2rem;
    color: var(--patient-text-light);
  }
  
  .appointments-empty-filter i {
    color: var(--patient-text-light);
    opacity: 0.3;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .appointment-grid {
      grid-template-columns: 1fr;
    }
  }
  
  @media (max-width: 768px) {
    .patient-compact-header .header-content {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }
    
    .patient-compact-header {
      padding: 1rem 1.5rem;
    }
    
    .patient-stats {
      grid-template-columns: 1fr;
    }
    
    .tabs-container {
      flex-direction: column;
    }
    
    .medical-grid {
      grid-template-columns: 1fr;
    }
    
    .time-slots {
      grid-template-columns: repeat(2, 1fr);
    }
    
    .section-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
  }
</style>

<script>
  // Variables globales
  let selectedDoctorId = null;
  
  // Gestion des onglets
  function switchToTab(tabName) {
    // Désactiver tous les onglets
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
    
    // Activer l'onglet sélectionné
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(tabName).classList.add('active');
  }
  
  // Sélection de médecin
  function selectDoctor(doctorId) {
    // Désélectionner tous les médecins
    document.querySelectorAll('.doctor-card').forEach(card => card.classList.remove('selected'));
    
    // Sélectionner le médecin cliqué
    event.currentTarget.classList.add('selected');
    selectedDoctorId = doctorId;
    
    // Mettre à jour le champ caché
    document.getElementById('selected-medecin').value = doctorId;
  }
  
  // Actions sur les rendez-vous
  
  function cancelAppointment(appointmentId) {
    if(confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
      // Ici on ferait l'appel AJAX
      alert(`Rendez-vous ${appointmentId} annulé`);
    }
  }
  
  function modifyAppointment(appointmentId) {
    alert(`Modification du rendez-vous ${appointmentId}`);
  }
  
  function viewAppointmentDetails(appointmentId) {
    alert(`Détails du rendez-vous ${appointmentId}`);
  }
  
  // Dossier médical
  function resendPrescription(prescriptionId) {
    if(confirm('Renvoyer cette ordonnance par e-mail ?')) {
      alert(`Ordonnance ${prescriptionId} renvoyée`);
    }
  }
  
  function viewConsultationDetails(consultationId) {
    alert(`Détails de la consultation ${consultationId}`);
  }
  
  // Filtres améliorés
  function filterAppointments(status) {
    // Mettre à jour les boutons de filtre
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-filter="${status}"]`).classList.add('active');
    
    const appointments = document.querySelectorAll('.appointment-card');
    let visibleCount = 0;
    
    appointments.forEach(card => {
      const cardStatus = card.dataset.status.toLowerCase();
      let shouldShow = false;
      
      if (status === 'all') {
        shouldShow = true;
      } else if (status === 'en_attente') {
        shouldShow = cardStatus === 'en_attente' || cardStatus === 'en attente' || cardStatus === 'pending';
      } else if (status === 'confirme') {
        shouldShow = cardStatus === 'confirme' || cardStatus === 'confirmé' || cardStatus === 'confirmed';
      } else if (status === 'termine') {
        shouldShow = cardStatus === 'termine' || cardStatus === 'terminé' || cardStatus === 'completed';
      } else {
        shouldShow = cardStatus.includes(status.toLowerCase());
      }
      
      if (shouldShow) {
        card.style.display = 'block';
        card.style.animation = 'fadeInUp 0.3s ease';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });
    
    // Afficher un message si aucun résultat
    const emptyState = document.querySelector('.appointments-empty-filter');
    if (visibleCount === 0 && !emptyState) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'appointments-empty-filter col-12 text-center py-4';
      emptyDiv.innerHTML = `
        <div class="text-muted">
          <i class="bi bi-filter-circle display-4 opacity-25"></i>
          <h5 class="mt-2">Aucun rendez-vous</h5>
          <p>Aucun rendez-vous ne correspond au filtre "${getFilterLabel(status)}"</p>
        </div>
      `;
      document.querySelector('.appointments-grid').appendChild(emptyDiv);
    } else if (visibleCount > 0 && emptyState) {
      emptyState.remove();
    }
  }
  
  // Obtenir le libellé du filtre
  function getFilterLabel(status) {
    const labels = {
      'all': 'Tous',
      'en_attente': 'En attente',
      'confirme': 'Confirmés', 
      'termine': 'Terminés'
    };
    return labels[status] || status;
  }
  
  // Sauvegarde brouillon
  function saveDraft() {
    const formData = new FormData(document.getElementById('modernRdvForm'));
    localStorage.setItem('rdv_draft', JSON.stringify(Object.fromEntries(formData)));
    alert('Brouillon sauvegardé');
  }
  
  // Chargement brouillon
  function loadDraft() {
    const draft = localStorage.getItem('rdv_draft');
    if(draft) {
      const data = JSON.parse(draft);
      Object.keys(data).forEach(key => {
        const input = document.querySelector(`[name="${key}"]`);
        if(input) input.value = data[key];
      });
    }
  }
  
  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    loadDraft();
    
    // Initialiser l'onglet par défaut selon les préférences
    const defaultTab = '{{ $preferences['default_tab'] }}';
    if (defaultTab && defaultTab !== 'rdv') {
      // Désactiver tous les onglets
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
      
      // Activer l'onglet par défaut
      const defaultTabBtn = document.querySelector(`[data-tab="${defaultTab}"]`);
      const defaultTabPanel = document.getElementById(defaultTab);
      
      if (defaultTabBtn && defaultTabPanel) {
        defaultTabBtn.classList.add('active');
        defaultTabPanel.classList.add('active');
      }
    }
    
    // Gestion des filtres de rendez-vous
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        filterAppointments(this.dataset.filter);
      });
    });
    
    // Validation du formulaire
    const form = document.getElementById('modernRdvForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        if(!selectedDoctorId) {
          e.preventDefault();
          alert('Veuillez sélectionner un médecin');
          return;
        }
        
        const timeSelected = document.querySelector('input[name="heure"]:checked');
        if(!timeSelected) {
          e.preventDefault();
          alert('Veuillez sélectionner un créneau horaire');
          return;
        }
      });
    }
    
    // Message de bienvenue pour les nouveaux utilisateurs avec préférences
    const isFirstVisit = !localStorage.getItem('patient_dashboard_visited');
    if (isFirstVisit) {
      setTimeout(() => {
        const settingsBtn = document.querySelector('.profile-settings-btn');
        if (settingsBtn) {
          settingsBtn.style.animation = 'pulse 2s infinite';
          setTimeout(() => {
            settingsBtn.style.animation = '';
          }, 6000);
        }
      }, 2000);
      localStorage.setItem('patient_dashboard_visited', 'true');
    }
  });
</script>
@endsection

@section('scripts')
<script>
  // Scripts additionnels si nécessaires
</script>
@endsection

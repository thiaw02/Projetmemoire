@extends('layouts.app')

@section('content')
<style>
  /* Styles modernes pour le dossier patient */
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  
  .patient-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    position: relative;
    overflow: hidden;
  }
  
  .patient-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
      45deg,
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0.05) 1px,
      transparent 1px,
      transparent 10px
    );
    opacity: 0.3;
  }
  
  .patient-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .patient-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
  }
  
  .patient-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.8rem;
    border-radius: 16px;
    font-size: 1.5rem;
  }
  
  .patient-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }
  
  .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .action-btn:hover {
    background: white;
    color: #10b981;
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }
  
  .patient-info-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
  }
  
  .patient-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
  }
  
  .patient-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
    margin-right: 1.5rem;
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
  }
  
  .patient-identity h4 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 700;
    font-size: 1.5rem;
  }
  
  .patient-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
  }
  
  .patient-badge {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8eeff 100%);
    color: #4b5563;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid rgba(16, 185, 129, 0.2);
  }
  
  .info-section {
    background: #f8f9ff;
    padding: 1rem;
    border-radius: 12px;
    border-left: 4px solid #10b981;
  }
  
  .info-section h6 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.4rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }
  
  .info-item:last-child {
    border-bottom: none;
  }
  
  .info-label {
    color: #6b7280;
    font-weight: 500;
    font-size: 0.9rem;
  }
  
  .info-value {
    color: #1f2937;
    font-weight: 600;
  }
  
  .medical-section {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
  }
  
  .medical-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
  }
  
  .section-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: between;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
  }
  
  .constants-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
  }
  
  .history-header {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
  }
  
  .consultations-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
  }
  
  .ordonnances-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
  }
  
  .analyses-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
  }
  
  .section-body {
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
  }
  
  .section-body::-webkit-scrollbar {
    width: 6px;
  }
  
  .section-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  
  .section-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
  }
  
  .constants-display {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }
  
  .constant-item {
    background: #f0f9ff;
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    border: 2px solid #e0f2fe;
  }
  
  .constant-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0369a1;
    margin-bottom: 0.25rem;
  }
  
  .constant-label {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
  }
  
  .modern-table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
  }
  
  .modern-table th {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8eeff 100%);
    color: #374151;
    font-weight: 600;
    padding: 0.8rem;
    border: none;
    font-size: 0.9rem;
  }
  
  .modern-table td {
    padding: 0.8rem;
    border: none;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
  }
  
  .modern-table tbody tr:hover {
    background: #f8f9ff;
  }
  
  .consultation-item {
    background: #faf5ff;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #8b5cf6;
    transition: all 0.3s ease;
  }
  
  .consultation-item:hover {
    background: #f3e8ff;
    transform: translateX(4px);
  }
  
  .consultation-date {
    font-weight: 600;
    color: #6b46c1;
    margin-bottom: 0.5rem;
  }
  
  .ordonnance-item {
    background: #fef9e2;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #f59e0b;
    transition: all 0.3s ease;
  }
  
  .ordonnance-item:hover {
    background: #fef3c7;
    transform: translateX(4px);
  }
  
  .medicament-list ul {
    margin: 0.5rem 0;
    padding-left: 1.2rem;
  }
  
  .medicament-list li {
    color: #92400e;
    font-weight: 500;
    margin-bottom: 0.25rem;
  }
  
  .empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
  }
  
  .empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.6;
  }
  
  .empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
</style>

<div class="patient-header">
  <div class="patient-header-content">
    <h2 class="patient-title">
      <i class="bi bi-person-lines-fill"></i>
      Dossier Patient
    </h2>
    <div class="patient-actions">
      <a href="{{ route('medecin.consultations', ['patient_id' => $patient->id, 'date_time' => \Carbon\Carbon::now()->format('Y-m-d\TH:i')]) }}" class="action-btn" title="Créer consultation">
        <i class="bi bi-clipboard-plus"></i>
        Consultation
      </a>
      <a href="{{ route('medecin.ordonnances', ['patient_id' => $patient->id]) }}" class="action-btn" title="Rédiger ordonnance">
        <i class="bi bi-prescription2"></i>
        Ordonnance
      </a>
      <a href="{{ route('medecin.dossierpatient') }}" class="action-btn" title="Retour à la liste">
        <i class="bi bi-arrow-left"></i>
        Retour
      </a>
    </div>
  </div>
</div>

<div class="patient-info-card">
  <div class="p-2rem">
    <div class="row g-4 align-items-center">
      <div class="col-auto">
        <div class="patient-avatar">
          {{ strtoupper(substr($patient->nom ?? 'P', 0, 1)) }}
        </div>
      </div>
      <div class="col">
        <div class="patient-identity">
          <h4>{{ $patient->nom }} {{ $patient->prenom }}</h4>
          <div class="patient-badges">
            <span class="patient-badge">
              <i class="bi bi-gender-ambiguous me-1"></i>{{ $patient->sexe ?? 'Non spécifié' }}
            </span>
            <span class="patient-badge">
              <i class="bi bi-calendar3 me-1"></i>{{ optional($patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance) : null)->format('d/m/Y') ?? 'Non spécifié' }}
            </span>
            @if($patient->groupe_sanguin)
              <span class="patient-badge">
                <i class="bi bi-droplet-fill me-1"></i>{{ $patient->groupe_sanguin }}
              </span>
            @endif
          </div>
        </div>
      </div>
    </div>
    
    <div class="row g-3 mt-3">
      <div class="col-md-6">
        <div class="info-section">
          <h6><i class="bi bi-person-vcard"></i>Informations personnelles</h6>
          <div class="info-item">
            <span class="info-label">Numéro dossier</span>
            <span class="info-value">{{ $patient->numero_dossier ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Date de naissance</span>
            <span class="info-value">{{ optional($patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance) : null)->format('d/m/Y') ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Sexe</span>
            <span class="info-value">{{ $patient->sexe ?? '—' }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="info-section">
          <h6><i class="bi bi-telephone"></i>Informations de contact</h6>
          <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value">{{ $patient->email ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Téléphone</span>
            <span class="info-value">{{ $patient->telephone ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Adresse</span>
            <span class="info-value">{{ $patient->adresse ?? '—' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="medical-section">
      <div class="section-header constants-header">
        <i class="bi bi-thermometer"></i>
        <span>Constantes actuelles</span>
      </div>
      <div class="section-body">
        @if($lastSuivi)
          <div class="constants-display">
            <div class="constant-item">
              <div class="constant-value">{{ $lastSuivi->temperature ?? '—' }}</div>
              <div class="constant-label">°C Température</div>
            </div>
            <div class="constant-item">
              <div class="constant-value">{{ $lastSuivi->tension ?? '—' }}</div>
              <div class="constant-label">Tension</div>
            </div>
          </div>
          <div class="text-center text-muted small">
            <i class="bi bi-clock me-1"></i>Enregistré le {{ optional($lastSuivi->created_at)->format('d/m/Y à H:i') }}
          </div>
        @else
          <div class="empty-state">
            <i class="bi bi-thermometer text-info"></i>
            <h5>Aucune constante</h5>
            <p>Pas de mesures enregistrées pour le moment.</p>
          </div>
        @endif
      </div>
    </div>

    <div class="medical-section">
      <div class="section-header history-header">
        <i class="bi bi-graph-up"></i>
        <span>Historique des constantes</span>
      </div>
      <div class="section-body">
        @if($patient->suivis->isEmpty())
          <div class="empty-state">
            <i class="bi bi-graph-up text-secondary"></i>
            <h5>Aucun historique</h5>
            <p>Pas de suivi médical enregistré.</p>
          </div>
        @else
          <table class="table modern-table">
            <thead>
              <tr>
                <th><i class="bi bi-calendar3 me-1"></i>Date</th>
                <th><i class="bi bi-thermometer me-1"></i>Temp.</th>
                <th><i class="bi bi-heart-pulse me-1"></i>Tension</th>
              </tr>
            </thead>
            <tbody>
              @foreach($patient->suivis as $sv)
                <tr>
                  <td>{{ optional($sv->created_at)->format('d/m/Y H:i') }}</td>
                  <td><span class="fw-semibold text-info">{{ $sv->temperature ?? '—' }}</span></td>
                  <td><span class="fw-semibold text-danger">{{ $sv->tension ?? '—' }}</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-primary text-white">Consultations (vous)</div>
      <div class="card-body scrollable">
        @php $consults = $patient->consultations ?? collect(); @endphp
        @if($consults->isEmpty())
          <div class="text-muted">Aucune consultation enregistrée par vous pour ce patient.</div>
        @else
          <div class="list-group list-group-flush">
            @foreach($consults as $c)
              <div class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-semibold">{{ optional($c->date_consultation ? \Carbon\Carbon::parse($c->date_consultation) : null)->format('d/m/Y') }}</div>
                    <div class="small text-muted">Statut: {{ $c->statut ?? '—' }}</div>
                  </div>
                  <div class="text-end">
                    <div class="small text-muted">Diagnostic</div>
                    <div>{{ $c->diagnostic ?? '—' }}</div>
                  </div>
                </div>
                @if($c->symptomes)
                <div class="mt-2 small"><span class="text-muted">Symptômes:</span> {{ $c->symptomes }}</div>
                @endif
                @if($c->traitement)
                <div class="mt-1 small"><span class="text-muted">Traitement:</span> {{ $c->traitement }}</div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="card shadow-sm mb-3">
      <div class="card-header bg-warning">Ordonnances</div>
      <div class="card-body scrollable">
        @php $ords = $patient->ordonnances ?? collect(); @endphp
        @if($ords->isEmpty())
          <div class="text-muted">Aucune ordonnance.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($ords as $o)
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div class="me-3">
                    <div class="fw-semibold">{{ optional($o->created_at)->format('d/m/Y H:i') }}</div>
                    @php 
                      $text = $o->medicaments ?: $o->contenu;
                    @endphp
                    @if(!empty($text))
                      @php 
                        $lines = preg_split("/(\r\n|\r|\n)/", $text);
                      @endphp
                      <ul class="mb-1 mt-1">
                        @foreach($lines as $ln)
                          @if(trim($ln) !== '')
                            <li>{{ $ln }}</li>
                          @endif
                        @endforeach
                      </ul>
                    @else
                      <div class="text-muted small">—</div>
                    @endif
                    @if(!empty($o->dosage))
                      <div class="small text-muted">Dosage: {{ $o->dosage }}</div>
                    @endif
                  </div>
                  <div class="text-muted small">Par Dr. {{ optional($o->medecin)->name }}</div>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">Analyses</div>
      <div class="card-body scrollable">
        @php $analyses = $patient->analyses ?? collect(); @endphp
        @if($analyses->isEmpty())
          <div class="text-muted">Aucune analyse.</div>
        @else
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Résultats</th>
              </tr>
            </thead>
            <tbody>
              @foreach($analyses as $a)
                <tr>
                  <td>{{ optional($a->date_analyse ? \Carbon\Carbon::parse($a->date_analyse) : null)->format('d/m/Y') }}</td>
                  <td>{{ $a->type_analyse ?? '—' }}</td>
                  <td>{{ $a->resultats ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Fonction pour actualiser les données du patient en temps réel
function refreshPatientData() {
    const patientId = {{ $patient->id }};
    
    fetch(`/medecin/patients/${patientId}/refresh`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour les constantes
            updateConstants(data.lastSuivi);
            
            // Mettre à jour l'historique des constantes
            updateConstantsHistory(data.suivis);
            
            // Mettre à jour les consultations
            updateConsultations(data.consultations);
            
            // Mettre à jour les ordonnances
            updateOrdonnances(data.ordonnances);
            
            // Mettre à jour les analyses
            updateAnalyses(data.analyses);
            
            // Afficher l'indicateur de mise à jour
            showUpdateIndicator();
        }
    })
    .catch(error => {
        console.error('Erreur lors de la mise à jour:', error);
    });
}

// Fonction pour mettre à jour les constantes
function updateConstants(lastSuivi) {
    const constantsSection = document.querySelector('.card.border-info .card-body');
    if (lastSuivi) {
        constantsSection.innerHTML = `
            <div class="d-flex flex-column gap-1">
                <div>Température: <strong>${lastSuivi.temperature || '—'} °C</strong></div>
                <div>Tension: <strong>${lastSuivi.tension || '—'}</strong></div>
                <div class="text-muted small">Enregistré le ${formatDate(lastSuivi.created_at)}</div>
            </div>
        `;
    } else {
        constantsSection.innerHTML = '<div class="text-muted">Aucune constante enregistrée.</div>';
    }
}

// Fonction pour mettre à jour l'historique des constantes
function updateConstantsHistory(suivis) {
    const historySection = document.querySelector('.card.border-secondary .card-body.scrollable');
    
    if (suivis && suivis.length > 0) {
        let tableHTML = `
            <table class="table table-sm table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Temp.</th>
                        <th>Tension</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        suivis.forEach(sv => {
            tableHTML += `
                <tr>
                    <td>${formatDate(sv.created_at)}</td>
                    <td>${sv.temperature || '—'}</td>
                    <td>${sv.tension || '—'}</td>
                </tr>
            `;
        });
        
        tableHTML += '</tbody></table>';
        historySection.innerHTML = tableHTML;
    } else {
        historySection.innerHTML = '<div class="text-muted">Aucun suivi.</div>';
    }
}

// Fonction pour mettre à jour les consultations
function updateConsultations(consultations) {
    const consultSection = document.querySelector('.card.shadow-sm .card-body.scrollable');
    
    if (consultations && consultations.length > 0) {
        let consultHTML = '<div class="list-group list-group-flush">';
        
        consultations.forEach(c => {
            consultHTML += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-semibold">${formatDate(c.date_consultation)}</div>
                            <div class="small text-muted">Statut: ${c.statut || '—'}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Diagnostic</div>
                            <div>${c.diagnostic || '—'}</div>
                        </div>
                    </div>
            `;
            
            if (c.symptomes) {
                consultHTML += `<div class="mt-2 small"><span class="text-muted">Symptômes:</span> ${c.symptomes}</div>`;
            }
            
            if (c.traitement) {
                consultHTML += `<div class="mt-1 small"><span class="text-muted">Traitement:</span> ${c.traitement}</div>`;
            }
            
            consultHTML += '</div>';
        });
        
        consultHTML += '</div>';
        consultSection.innerHTML = consultHTML;
    } else {
        consultSection.innerHTML = '<div class="text-muted">Aucune consultation enregistrée par vous pour ce patient.</div>';
    }
}

// Fonction pour mettre à jour les ordonnances
function updateOrdonnances(ordonnances) {
    const ordSection = document.querySelectorAll('.card.shadow-sm')[1].querySelector('.card-body.scrollable');
    
    if (ordonnances && ordonnances.length > 0) {
        let ordHTML = '<ul class="list-group list-group-flush">';
        
        ordonnances.forEach(o => {
            const text = o.medicaments || o.contenu || '';
            let medicamentsHTML = '';
            
            if (text) {
                const lines = text.split(/\r\n|\r|\n/);
                medicamentsHTML = '<ul class="mb-1 mt-1">';
                lines.forEach(line => {
                    if (line.trim() !== '') {
                        medicamentsHTML += `<li>${line}</li>`;
                    }
                });
                medicamentsHTML += '</ul>';
            } else {
                medicamentsHTML = '<div class="text-muted small">—</div>';
            }
            
            ordHTML += `
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div class="me-3">
                            <div class="fw-semibold">${formatDate(o.created_at)}</div>
                            ${medicamentsHTML}
                            ${o.dosage ? `<div class="small text-muted">Dosage: ${o.dosage}</div>` : ''}
                        </div>
                        <div class="text-muted small">Par Dr. ${o.medecin ? o.medecin.name || '—' : '—'}</div>
                    </div>
                </li>
            `;
        });
        
        ordHTML += '</ul>';
        ordSection.innerHTML = ordHTML;
    } else {
        ordSection.innerHTML = '<div class="text-muted">Aucune ordonnance.</div>';
    }
}

// Fonction pour mettre à jour les analyses
function updateAnalyses(analyses) {
    const analysesSection = document.querySelectorAll('.card.shadow-sm')[2].querySelector('.card-body.scrollable');
    
    if (analyses && analyses.length > 0) {
        let analysesHTML = `
            <table class="table table-sm table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Résultats</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        analyses.forEach(a => {
            analysesHTML += `
                <tr>
                    <td>${formatDate(a.date_analyse)}</td>
                    <td>${a.type_analyse || '—'}</td>
                    <td>${a.resultats || '—'}</td>
                </tr>
            `;
        });
        
        analysesHTML += '</tbody></table>';
        analysesSection.innerHTML = analysesHTML;
    } else {
        analysesSection.innerHTML = '<div class="text-muted">Aucune analyse.</div>';
    }
}

// Fonction pour formater les dates
function formatDate(dateString) {
    if (!dateString) return '—';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Fonction pour afficher l'indicateur de mise à jour
function showUpdateIndicator() {
    // Créer un indicateur temporaire
    const indicator = document.createElement('div');
    indicator.innerHTML = '🔄 Données mises à jour';
    indicator.className = 'alert alert-success alert-dismissible fade show position-fixed';
    indicator.style.cssText = 'top: 20px; right: 20px; z-index: 1050; max-width: 300px;';
    
    document.body.appendChild(indicator);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        indicator.remove();
    }, 3000);
}

// Actualiser les données toutes les 30 secondes
setInterval(refreshPatientData, 30000);

// Actualiser une première fois après chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Actualiser après 5 secondes pour laisser le temps à la page de se charger complètement
    setTimeout(refreshPatientData, 5000);
    
    // Ajouter un bouton de actualisation manuelle
    const header = document.querySelector('h3.mb-0');
    const refreshBtn = document.createElement('button');
    refreshBtn.innerHTML = '🔄';
    refreshBtn.className = 'btn btn-outline-primary btn-sm ms-2';
    refreshBtn.title = 'Actualiser les données';
    refreshBtn.onclick = refreshPatientData;
    
    header.appendChild(refreshBtn);
});
</script>
@endpush

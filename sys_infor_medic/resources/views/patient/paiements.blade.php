@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  
  <div class="col-lg-9">
    <div class="payment-main-content">
        
        <!-- Header moderne -->
        <div class="payment-header">
          <div class="header-content">
            <div class="header-left">
              <div class="header-icon">
                <i class="bi bi-credit-card-2-front"></i>
              </div>
              <div class="header-text">
                <h1>Centre de Paiement</h1>
                <p>Gérez vos transactions médicales en toute sécurité</p>
                <nav class="breadcrumb-nav">
                  <span><i class="bi bi-house"></i> Dashboard</span>
                  <i class="bi bi-chevron-right"></i>
                  <span class="active">Paiements</span>
                </nav>
              </div>
            </div>
            
            <div class="header-right">
              <a href="{{ route('patient.dashboard') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                <span>Retour</span>
              </a>
              <div class="stats-cards">
                <div class="stat-card success">
                  <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                  <div class="stat-info">
                    <span class="stat-number">{{ $orders->where('status', 'paid')->count() }}</span>
                    <span class="stat-label">Payés</span>
                  </div>
                </div>
                <div class="stat-card pending">
                  <div class="stat-icon"><i class="bi bi-clock"></i></div>
                  <div class="stat-info">
                    <span class="stat-number">{{ $orders->where('status', 'pending')->count() }}</span>
                    <span class="stat-label">En attente</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
          <div class="alert-card success">
            <div class="alert-icon"><i class="bi bi-check-circle"></i></div>
            <div class="alert-content">
              <h6>Succès</h6>
              <p>{{ session('success') }}</p>
            </div>
            <button class="alert-close" onclick="this.parentElement.remove()">
              <i class="bi bi-x"></i>
            </button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert-card error">
            <div class="alert-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="alert-content">
              <h6>Erreur</h6>
              <p>{{ $errors->first() }}</p>
            </div>
            <button class="alert-close" onclick="this.parentElement.remove()">
              <i class="bi bi-x"></i>
            </button>
          </div>
        @endif

        <!-- Formulaire de paiement -->
        <div class="payment-form-card">
          <div class="form-header">
            <div class="form-icon">
              <i class="bi bi-plus-circle"></i>
            </div>
            <div class="form-title">
              <h3>Nouveau Paiement</h3>
              <p>Sélectionnez votre service et procédez au paiement sécurisé</p>
            </div>
            <div class="security-badge">
              <i class="bi bi-shield-check"></i>
              <span>Sécurisé</span>
            </div>
          </div>

          <form method="POST" action="{{ route('patient.payments.checkout') }}" class="payment-form">
            @csrf
            
            <!-- Services -->
            <div class="form-section">
              <div class="section-title">
                <i class="bi bi-card-checklist"></i>
                <h4>Choisissez votre service</h4>
              </div>
              
            <div class="services-grid">
                <input type="radio" name="kind" value="consultation" id="consultation" checked required>
                <label for="consultation" class="service-option">
                  <div class="service-icon consultation">
                    <i class="bi bi-person-heart"></i>
                  </div>
                  <div class="service-info">
                    <h5>Consultation</h5>
                    <p>Consultation médicale complète</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Diagnostic</span>
                      <span><i class="bi bi-check2"></i>Prescription</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </label>
                
                <input type="radio" name="kind" value="analyse" id="analyse" required>
                <label for="analyse" class="service-option">
                  <div class="service-icon analyse">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                  <div class="service-info">
                    <h5>Analyse</h5>
                    <p>Examens de laboratoire</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Analyses sanguines</span>
                      <span><i class="bi bi-check2"></i>Résultats détaillés</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price">{{ number_format($priceAnalyse ?? 10000, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </label>
                
                <input type="radio" name="kind" value="acte" id="acte" required>
                <label for="acte" class="service-option">
                  <div class="service-icon acte">
                    <i class="bi bi-bandaid"></i>
                  </div>
                  <div class="service-info">
                    <h5>Acte médical</h5>
                    <p>Interventions spécialisées</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Procédures</span>
                      <span><i class="bi bi-check2"></i>Suivi</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price">{{ number_format($priceActe ?? 7000, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </label>
                
                <input type="radio" name="kind" value="rendezvous" id="rendezvous" required>
                <label for="rendezvous" class="service-option">
                  <div class="service-icon rendezvous">
                    <i class="bi bi-calendar-check"></i>
                  </div>
                  <div class="service-info">
                    <h5>Rendez-vous confirmé</h5>
                    <p>Paiement pour consultation confirmée</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>RDV confirmé</span>
                      <span><i class="bi bi-check2"></i>Paiement avancé</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </label>
              </div>
            </div>
            
            <!-- Section Rendez-vous confirmés -->
            @if(isset($confirmedAppointments) && $confirmedAppointments->count() > 0)
            <div class="form-section" id="rdv-section" style="display: none;">
              <div class="section-title">
                <i class="bi bi-calendar-week"></i>
                <h4>Vos rendez-vous confirmés</h4>
              </div>
              
              <div class="appointments-grid">
                @foreach($confirmedAppointments as $appointment)
                <div class="appointment-option" data-rdv-id="{{ $appointment->id }}" data-price="{{ $priceConsult }}">
                  <input type="radio" name="appointment_id" value="{{ $appointment->id }}" id="rdv-{{ $appointment->id }}">
                  <label for="rdv-{{ $appointment->id }}" class="appointment-card">
                    <div class="appointment-info">
                      <div class="appointment-date">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('d M Y') }}</span>
                      </div>
                      <div class="appointment-time">
                        <i class="bi bi-clock"></i>
                        <span>{{ $appointment->heure }}</span>
                      </div>
                      <div class="appointment-doctor">
                        <i class="bi bi-person-badge"></i>
                        <span>{{ $appointment->medecin->name ?? 'Médecin non assigné' }}</span>
                      </div>
                      @if($appointment->motif)
                      <div class="appointment-reason">
                        <i class="bi bi-chat-text"></i>
                        <span>{{ $appointment->motif }}</span>
                      </div>
                      @endif
                    </div>
                    <div class="appointment-status">
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i>
                        Confirmé
                      </span>
                    </div>
                  </label>
                </div>
                @endforeach
              </div>
            </div>
            @endif

            <!-- Détails -->
            <div class="form-section">
              <div class="section-title">
                <i class="bi bi-gear"></i>
                <h4>Détails du paiement</h4>
              </div>
              
              <div class="details-grid">
                <div class="detail-field">
                  <label for="ref_id">
                    <i class="bi bi-link"></i>
                    Référence (optionnel)
                  </label>
                  <select name="ref_id" id="ref_id" class="form-select">
                    <option value="">— Aucune référence —</option>
                    @if(!empty($consultationsList))
                      @foreach($consultationsList as $c)
                        <option value="consultation:{{ $c->id }}">
                          Consultation #{{ $c->id }} — {{ optional($c->date_consultation)->format('d/m/Y') }}
                        </option>
                      @endforeach
                    @endif
                    @if(!empty($analysesList))
                      @foreach($analysesList as $a)
                        <option value="analyse:{{ $a->id }}">
                          Analyse #{{ $a->id }} — {{ $a->type_analyse ?? $a->type ?? '—' }}
                        </option>
                      @endforeach
                    @endif
                    @if(isset($confirmedAppointments) && $confirmedAppointments->count() > 0)
                      @foreach($confirmedAppointments as $rdv)
                        <option value="rendezvous:{{ $rdv->id }}">
                          RDV #{{ $rdv->id }} — {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }} à {{ $rdv->heure }} ({{ $rdv->medecin->name ?? 'Médecin' }})
                        </option>
                      @endforeach
                    @endif
                  </select>
                </div>
                
                <div class="detail-field">
                  <label for="label">
                    <i class="bi bi-tag"></i>
                    Libellé
                  </label>
                  <input type="text" name="label" id="label" class="form-control" value="Ticket de consultation" placeholder="Description">
                </div>
              </div>
            </div>

            <!-- Méthode de paiement -->
            <div class="form-section">
              <div class="section-title">
                <i class="bi bi-credit-card"></i>
                <h4>Méthode de paiement</h4>
              </div>
              
              <div class="payment-methods">
                <input type="radio" name="provider" value="wave" id="wave" checked required>
                <label for="wave" class="payment-method">
                  <div class="method-icon wave">
                    <i class="bi bi-wallet2"></i>
                  </div>
                  <div class="method-info">
                    <h6>Wave Money</h6>
                    <p>Paiement mobile rapide</p>
                  </div>
                  <span class="method-badge">Recommandé</span>
                </label>
                
                <input type="radio" name="provider" value="orangemoney" id="orange" required>
                <label for="orange" class="payment-method">
                  <div class="method-icon orange">
                    <i class="bi bi-phone"></i>
                  </div>
                  <div class="method-info">
                    <h6>Orange Money</h6>
                    <p>Portefeuille électronique</p>
                  </div>
                  <span class="method-badge">Rapide</span>
                </label>
              </div>
            </div>

            <!-- Récapitulatif -->
            <div class="summary-section">
              <div class="summary-header">
                <i class="bi bi-receipt"></i>
                <h5>Récapitulatif</h5>
              </div>
              
              <input type="hidden" name="amount" id="amount" value="{{ $priceConsult }}">
              
              <div class="summary-lines">
                <div class="summary-line">
                  <span class="label">Service</span>
                  <span class="value" id="summary-type">Consultation</span>
                </div>
                <div class="summary-line">
                  <span class="label">Méthode</span>
                  <span class="value" id="summary-provider">Wave Money</span>
                </div>
              </div>
              
              <div class="summary-total">
                <span class="label">Total à payer</span>
                <div class="total-amount">
                  <span class="amount" id="summary-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                  <span class="currency">XOF</span>
                </div>
              </div>
            </div>

            <!-- Bouton de paiement -->
            <div class="payment-action">
              <button type="submit" class="btn-pay" id="payButton">
                <div class="btn-content">
                  <i class="bi bi-shield-check"></i>
                  <span>Procéder au paiement</span>
                </div>
                <div class="btn-amount" id="button-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }} XOF</div>
              </button>
              
              <div class="security-info">
                <div class="security-item">
                  <i class="bi bi-lock"></i>
                  <span>Cryptage SSL</span>
                </div>
                <div class="security-item">
                  <i class="bi bi-shield-check"></i>
                  <span>100% Sécurisé</span>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Historique -->
        <div class="history-card">
          <div class="history-header">
            <div class="history-title">
              <i class="bi bi-clock-history"></i>
              <h3>Historique des transactions</h3>
              <p>Consultez vos paiements et téléchargez vos reçus</p>
            </div>
            
            <div class="history-filters">
              <button class="filter-btn active" data-filter="all">
                Tous <span class="count">{{ $orders->count() }}</span>
              </button>
              <button class="filter-btn" data-filter="paid">
                Payés <span class="count">{{ $orders->where('status', 'paid')->count() }}</span>
              </button>
              <button class="filter-btn" data-filter="pending">
                En attente <span class="count">{{ $orders->where('status', 'pending')->count() }}</span>
              </button>
            </div>
          </div>
          
          <div class="transactions-list">
            @forelse($orders as $order)
              @php($item = $order->items->first())
              <div class="transaction-item" data-status="{{ $order->status }}">
                <div class="transaction-status {{ $order->status }}">
                  @if($order->status === 'paid')
                    <i class="bi bi-check-circle"></i>
                  @elseif($order->status === 'pending')
                    <i class="bi bi-clock"></i>
                  @else
                    <i class="bi bi-x-circle"></i>
                  @endif
                </div>
                
                <div class="transaction-details">
                  <div class="transaction-main">
                    <h6>{{ $item->label ?? 'Paiement médical' }}</h6>
                    <div class="transaction-meta">
                      <span class="date">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                      <span class="provider">{{ ucfirst($order->provider ?? 'N/A') }}</span>
                    </div>
                  </div>
                  
                  <div class="transaction-amount">
                    <span class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </div>
                
                <div class="transaction-actions">
                  @if($order->status === 'pending' && $order->payment_url)
                    <a href="{{ $order->payment_url }}" class="btn-action continue">
                      <i class="bi bi-play"></i> Continuer
                    </a>
                  @elseif($order->status === 'paid')
                    <a href="{{ route('payments.receipt', $order->id) }}" class="btn-action receipt">
                      <i class="bi bi-download"></i> Reçu
                    </a>
                  @endif
                </div>
              </div>
            @empty
              <div class="empty-state">
                <i class="bi bi-credit-card"></i>
                <h4>Aucune transaction</h4>
                <p>Vos paiements apparaîtront ici</p>
              </div>
            @endforelse
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<!-- CSS optimisé pour structure harmonisée -->
<style>
/* Variables CSS */
:root {
  --primary: #10b981;
  --primary-dark: #047857;
  --secondary: #3b82f6;
  --warning: #f59e0b;
  --danger: #ef4444;
  --success: #10b981;
  --gray-50: #f8fafc;
  --gray-100: #f1f5f9;
  --gray-200: #e2e8f0;
  --gray-600: #475569;
  --gray-700: #334155;
  --gray-900: #0f172a;
  --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --radius: 0.75rem;
  --transition: all 0.2s ease-in-out;
}

/* Layout principal harmonisé avec dashboard */
.payment-main-content {
  padding: 0;
  max-width: none;
  margin: 0;
}

/* Header */
.payment-header {
  background: linear-gradient(135deg, var(--gray-900), var(--gray-700));
  color: white;
  border-radius: var(--radius);
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
}

.header-icon {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.header-text h1 {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.header-text p {
  opacity: 0.9;
  margin-bottom: 0.75rem;
}

.breadcrumb-nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  opacity: 0.8;
}

.header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1rem;
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius);
  color: white;
  text-decoration: none;
  transition: var(--transition);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.stats-cards {
  display: flex;
  gap: 1rem;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius);
  min-width: 120px;
}

.stat-icon {
  font-size: 1.25rem;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
}

.stat-label {
  font-size: 0.75rem;
  opacity: 0.9;
  text-transform: uppercase;
}

/* Alertes */
.alert-card {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border-radius: var(--radius);
  margin-bottom: 1.5rem;
  position: relative;
}

.alert-card.success {
  background: #d1fae5;
  border-left: 4px solid var(--success);
  color: #065f46;
}

.alert-card.error {
  background: #fee2e2;
  border-left: 4px solid var(--danger);
  color: #991b1b;
}

.alert-icon {
  font-size: 1.25rem;
}

.alert-close {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  opacity: 0.6;
  transition: var(--transition);
}

.alert-close:hover {
  opacity: 1;
}

/* Formulaire de paiement */
.payment-form-card {
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  margin-bottom: 2rem;
  overflow: hidden;
}

.form-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 2rem;
  border-bottom: 1px solid var(--gray-200);
  background: var(--gray-50);
}

.form-icon {
  width: 48px;
  height: 48px;
  background: var(--primary);
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.form-title h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.form-title p {
  color: var(--gray-600);
  margin: 0;
}

.security-badge {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(16, 185, 129, 0.1);
  color: var(--primary);
  border-radius: var(--radius);
  font-size: 0.875rem;
  font-weight: 600;
}

.payment-form {
  padding: 2rem;
}

.form-section {
  margin-bottom: 2rem;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.section-title h4 {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
}

.section-title i {
  color: var(--primary);
}

/* Services */
.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
}

input[type="radio"] {
  display: none;
}

.service-option {
  display: block;
  padding: 1.5rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius);
  background: white;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
}

.service-option:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

input[type="radio"]:checked + .service-option {
  border-color: var(--primary);
  background: rgba(16, 185, 129, 0.02);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.service-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.service-icon.consultation { background: linear-gradient(135deg, #10b981, #047857); }
.service-icon.analyse { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.service-icon.acte { background: linear-gradient(135deg, #f59e0b, #d97706); }

.service-info h5 {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.service-info p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.service-features {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  margin-bottom: 1rem;
}

.service-features span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: var(--gray-600);
}

.service-features i {
  color: var(--success);
}

.service-price {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
  justify-content: center;
  padding-top: 1rem;
  border-top: 1px solid var(--gray-200);
}

.service-price .price {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.service-price .currency {
  color: var(--gray-600);
  font-size: 0.875rem;
}

/* Détails et méthodes */
.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.detail-field label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--gray-700);
}

.detail-field i {
  color: var(--primary);
}

.form-select, .form-control {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius);
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.payment-methods {
  display: grid;
  gap: 1rem;
}

.payment-method {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius);
  background: white;
  cursor: pointer;
  transition: var(--transition);
}

.payment-method:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

input[type="radio"]:checked + .payment-method {
  border-color: var(--primary);
  background: rgba(16, 185, 129, 0.02);
}

.method-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
}

.method-icon.wave { background: var(--primary); }
.method-icon.orange { background: #f97316; }

.method-info h6 {
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.method-info p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin: 0;
}

.method-badge {
  margin-left: auto;
  padding: 0.25rem 0.75rem;
  background: rgba(245, 158, 11, 0.1);
  color: var(--warning);
  border-radius: var(--radius);
  font-size: 0.75rem;
  font-weight: 600;
}

/* Récapitulatif */
.summary-section {
  background: var(--gray-50);
  border: 2px solid var(--gray-200);
  border-radius: var(--radius);
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.summary-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  color: var(--gray-700);
}

.summary-header h5 {
  font-weight: 600;
  margin: 0;
}

.summary-lines {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--gray-200);
}

.summary-line:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: white;
  border-radius: var(--radius);
  border: 1px solid var(--gray-200);
}

.summary-total .label {
  font-weight: 700;
  color: var(--gray-700);
}

.total-amount {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.total-amount .amount {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.total-amount .currency {
  color: var(--gray-600);
}

/* Bouton de paiement */
.payment-action {
  text-align: center;
}

.btn-pay {
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 400px;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border: none;
  border-radius: var(--radius);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  margin-bottom: 1.5rem;
}

.btn-pay:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-amount {
  font-weight: 700;
}

.security-info {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.security-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.875rem;
}

.security-item i {
  color: var(--primary);
}

/* Historique */
.history-card {
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
  padding: 2rem;
  border-bottom: 1px solid var(--gray-200);
  background: var(--gray-50);
}

.history-title {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.history-title i {
  color: var(--primary);
  font-size: 1.5rem;
  margin-top: 0.25rem;
}

.history-title h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.history-title p {
  color: var(--gray-600);
  margin: 0;
}

.history-filters {
  display: flex;
  gap: 0.5rem;
  background: var(--gray-100);
  padding: 0.25rem;
  border-radius: var(--radius);
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: transparent;
  border: none;
  border-radius: calc(var(--radius) - 0.25rem);
  color: var(--gray-600);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  font-size: 0.875rem;
}

.filter-btn.active {
  background: var(--primary);
  color: white;
}

.filter-btn .count {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.125rem 0.5rem;
  border-radius: calc(var(--radius) - 0.5rem);
  font-size: 0.75rem;
}

/* Transactions */
.transactions-list {
  padding: 2rem;
}

.transaction-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius);
  margin-bottom: 1rem;
  transition: var(--transition);
}

.transaction-item:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

.transaction-status {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
}

.transaction-status.paid { background: var(--success); }
.transaction-status.pending { background: var(--warning); }
.transaction-status.failed { background: var(--danger); }

.transaction-details {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.transaction-main h6 {
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.transaction-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--gray-600);
}

.transaction-amount {
  text-align: right;
}

.transaction-amount .amount {
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary);
}

.transaction-amount .currency {
  color: var(--gray-600);
  font-size: 0.875rem;
}

.transaction-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: var(--radius);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: var(--transition);
}

.btn-action.continue {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
}

.btn-action.receipt {
  background: rgba(59, 130, 246, 0.1);
  color: var(--secondary);
}

.btn-action:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow);
}

/* État vide */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--gray-600);
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h4 {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--gray-700);
}

/* Responsive harmonisé avec dashboard */
@media (max-width: 992px) {
  .sidebar-sticky {
    position: static !important;
  }
  
  .col-lg-3,
  .col-lg-9 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .header-left {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .header-right {
    align-items: center;
    flex-direction: row;
    width: 100%;
    justify-content: space-between;
  }
  
  .stats-cards {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .services-grid {
    grid-template-columns: 1fr;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
  }
  
  .history-header {
    flex-direction: column;
    gap: 1rem;
  }
  
  .history-filters {
    align-self: stretch;
  }
  
  .filter-btn {
    flex: 1;
    justify-content: center;
  }
  
  .transaction-details {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .transaction-meta {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .security-info {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Prix des services
  const prices = {
    consultation: {{ (int)($priceConsult ?? 5000) }},
    analyse: {{ (int)($priceAnalyse ?? 10000) }},
    acte: {{ (int)($priceActe ?? 7000) }}
  };
  
  // Réactiver le bouton si on revient sur la page (par exemple après une erreur)
  const payButton = document.getElementById('payButton');
  if (payButton && payButton.disabled) {
    payButton.disabled = false;
    const originalText = 'Procéder au paiement';
    const amount = document.getElementById('button-amount')?.textContent || '';
    payButton.innerHTML = `
      <div class="btn-content">
        <i class="bi bi-shield-check"></i>
        <span>${originalText}</span>
      </div>
      <div class="btn-amount" id="button-amount">${amount}</div>
    `;
  }
  
  // Libellés des services
  const labels = {
    consultation: 'Ticket de consultation',
    analyse: 'Analyse médicale',
    acte: 'Acte médical'
  };

  // Fonction de mise à jour du paiement
  function updatePayment() {
    const selectedService = document.querySelector('input[name="kind"]:checked')?.value || 'consultation';
    const selectedProvider = document.querySelector('input[name="provider"]:checked')?.value || 'wave';
    
    const price = prices[selectedService];
    const formattedPrice = new Intl.NumberFormat('fr-FR').format(price);
    
    // Mise à jour des éléments
    document.getElementById('amount').value = price;
    document.getElementById('label').value = labels[selectedService];
    document.getElementById('summary-type').textContent = selectedService.charAt(0).toUpperCase() + selectedService.slice(1);
    document.getElementById('summary-provider').textContent = selectedProvider === 'wave' ? 'Wave Money' : 'Orange Money';
    document.getElementById('summary-amount').textContent = formattedPrice;
    document.getElementById('button-amount').textContent = formattedPrice + ' XOF';
    
    // Filtrer les références selon le type sélectionné
    const refSelect = document.getElementById('ref_id');
    if (refSelect) {
      Array.from(refSelect.options).forEach(option => {
        if (option.value && !option.value.startsWith(selectedService + ':')) {
          option.hidden = true;
        } else {
          option.hidden = false;
        }
      });
      
      // Réinitialiser la sélection si elle n'est plus valide
      if (refSelect.options[refSelect.selectedIndex]?.hidden) {
        refSelect.value = '';
      }
    }
  }

  // Événements pour les changements de service et de provider
  document.querySelectorAll('input[name="kind"]').forEach(radio => {
    radio.addEventListener('change', updatePayment);
  });
  
  document.querySelectorAll('input[name="provider"]').forEach(radio => {
    radio.addEventListener('change', updatePayment);
  });

  // Filtres d'historique
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Retirer la classe active de tous les boutons
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      
      // Ajouter la classe active au bouton cliqué
      this.classList.add('active');
      
      const filter = this.dataset.filter;
      
      // Filtrer les transactions
      document.querySelectorAll('.transaction-item').forEach(item => {
        const status = item.dataset.status;
        
        if (filter === 'all' || status === filter) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  // Animation du bouton de paiement lors de la soumission
  const payButton = document.getElementById('payButton');
  if (payButton) {
    const form = payButton.closest('form');
    let originalContent = payButton.innerHTML;
    
    payButton.addEventListener('click', function(e) {
      // Vérifier d'abord si le formulaire est valide
      if (form && !form.checkValidity()) {
        return; // Laisser la validation HTML5 se faire
      }
      
      // Désactiver le bouton seulement si la validation passe
      this.innerHTML = `
        <div class="btn-content">
          <i class="bi bi-hourglass-split"></i>
          <span>Traitement en cours...</span>
        </div>
        <div class="btn-amount" id="button-amount"></div>
      `;
      this.disabled = true;
      
      // Réactiver le bouton après un délai si la page ne se recharge pas
      setTimeout(() => {
        if (!this.disabled) return;
        this.innerHTML = originalContent;
        this.disabled = false;
      }, 10000); // 10 secondes max
    });
    
    // Réactiver le bouton si le formulaire retourne une erreur
    form.addEventListener('invalid', function() {
      if (payButton.disabled) {
        payButton.innerHTML = originalContent;
        payButton.disabled = false;
      }
    });
  }

  // Initialiser l'affichage
  updatePayment();
});
</script>
@endsection

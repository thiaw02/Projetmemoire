@extends('layouts.app')

@section('content')
<div class="balanced-payment-wrapper">
  <div class="container-fluid">
    <div class="row balanced-layout">
      
      <!-- Sidebar optimisée -->
      <div class="col-xl-2 col-lg-3 col-md-4">
        <div class="compact-sidebar">
          @include('layouts.partials.profile_sidebar')
        </div>
      </div>
      
      <!-- Contenu principal centré -->
      <div class="col-xl-10 col-lg-9 col-md-8">
        <div class="main-content-wrapper">
          
          <!-- Header équilibré -->
          <div class="balanced-header">
            <div class="header-container">
              <div class="header-left">
                <div class="header-icon-wrapper">
                  <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="header-content">
                  <h1>Centre de Paiement</h1>
                  <p>Gérez vos transactions médicales en toute sécurité</p>
                  <nav class="breadcrumb-modern">
                    <span>Accueil</span>
                    <i class="bi bi-chevron-right"></i>
                    <span>Paiements</span>
                  </nav>
                </div>
              </div>
              
              <div class="header-right">
                <a href="{{ route('patient.dashboard') }}" class="btn-dashboard-return">
                  <i class="bi bi-house"></i>
                  <span>Dashboard</span>
                </a>
                <div class="stats-display">
                  <div class="stat-box">
                    <span class="stat-number">{{ $orders->where('status', 'paid')->count() }}</span>
                    <span class="stat-text">Payé</span>
                  </div>
                  <div class="stat-separator"></div>
                  <div class="stat-box">
                    <span class="stat-number">{{ $orders->where('status', 'pending')->count() }}</span>
                    <span class="stat-text">En attente</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Messages avec espacement correct -->
          @if(session('success'))
            <div class="balanced-alert success-alert">
              <div class="alert-indicator">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <div class="alert-message">
                <h6>Succès</h6>
                <p>{{ session('success') }}</p>
              </div>
              <button class="alert-dismiss" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
              </button>
            </div>
          @endif

          @if($errors->any())
            <div class="balanced-alert error-alert">
              <div class="alert-indicator">
                <i class="bi bi-exclamation-triangle-fill"></i>
              </div>
              <div class="alert-message">
                <h6>Erreur</h6>
                <p>{{ $errors->first() }}</p>
              </div>
              <button class="alert-dismiss" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
              </button>
            </div>
          @endif

          <!-- Formulaire de paiement équilibré -->
          <div class="balanced-payment-card">
            <div class="payment-card-header">
              <div class="header-icon-badge">
                <i class="bi bi-plus-circle-fill"></i>
              </div>
              <div class="header-info">
                <h3>Nouveau Paiement</h3>
                <p>Sélectionnez votre service et procédez au paiement sécurisé</p>
              </div>
              <div class="security-indicator">
                <i class="bi bi-shield-check"></i>
                <span>Sécurisé</span>
              </div>
            </div>

            <form method="POST" action="{{ route('patient.payments.checkout') }}" class="balanced-payment-form">
              @csrf
              
              <!-- Section services avec espacement optimisé -->
              <div class="form-section">
                <div class="section-title">
                  <h4><i class="bi bi-card-checklist"></i>Choisissez votre service</h4>
                  <p>Sélectionnez le type de service médical à payer</p>
                </div>
                
                <div class="services-balanced-grid">
                  <input type="radio" name="kind" value="consultation" id="consultation" checked>
                  <label for="consultation" class="service-option-balanced">
                    <div class="service-top">
                      <div class="service-icon consultation">
                        <i class="bi bi-person-heart"></i>
                      </div>
                      <span class="service-tag popular">Populaire</span>
                    </div>
                    <div class="service-middle">
                      <h5>Consultation</h5>
                      <p>Consultation médicale complète</p>
                      <div class="service-benefits">
                        <span><i class="bi bi-check2"></i>Diagnostic complet</span>
                        <span><i class="bi bi-check2"></i>Prescription médicale</span>
                      </div>
                    </div>
                    <div class="service-bottom">
                      <span class="price-value">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                      <span class="price-unit">XOF</span>
                    </div>
                  </label>
                  
                  <input type="radio" name="kind" value="analyse" id="analyse">
                  <label for="analyse" class="service-option-balanced">
                    <div class="service-top">
                      <div class="service-icon analyse">
                        <i class="bi bi-graph-up-arrow"></i>
                      </div>
                      <span class="service-tag premium">Premium</span>
                    </div>
                    <div class="service-middle">
                      <h5>Analyse médicale</h5>
                      <p>Examens de laboratoire complets</p>
                      <div class="service-benefits">
                        <span><i class="bi bi-check2"></i>Analyses sanguines</span>
                        <span><i class="bi bi-check2"></i>Résultats détaillés</span>
                      </div>
                    </div>
                    <div class="service-bottom">
                      <span class="price-value">{{ number_format($priceAnalyse ?? 10000, 0, ',', ' ') }}</span>
                      <span class="price-unit">XOF</span>
                    </div>
                  </label>
                  
                  <input type="radio" name="kind" value="acte" id="acte">
                  <label for="acte" class="service-option-balanced">
                    <div class="service-top">
                      <div class="service-icon acte">
                        <i class="bi bi-bandaid"></i>
                      </div>
                      <span class="service-tag special">Spécialisé</span>
                    </div>
                    <div class="service-middle">
                      <h5>Acte médical</h5>
                      <p>Interventions spécialisées</p>
                      <div class="service-benefits">
                        <span><i class="bi bi-check2"></i>Procédures techniques</span>
                        <span><i class="bi bi-check2"></i>Suivi médical</span>
                      </div>
                    </div>
                    <div class="service-bottom">
                      <span class="price-value">{{ number_format($priceActe ?? 7000, 0, ',', ' ') }}</span>
                      <span class="price-unit">XOF</span>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Détails du paiement -->
              <div class="form-section">
                <div class="section-title">
                  <h4><i class="bi bi-gear"></i>Détails du paiement</h4>
                  <p>Configurez les options avancées de votre paiement</p>
                </div>
                
                <div class="details-balanced-grid">
                  <div class="field-wrapper">
                    <label class="field-title">
                      <i class="bi bi-link-45deg"></i>
                      <span>Référence associée</span>
                      <small>Lier à un rendez-vous existant (optionnel)</small>
                    </label>
                    <div class="select-container">
                      <select name="ref_id" id="ref_id" class="balanced-select">
                        <option value="">— Aucune référence —</option>
                        @if(!empty($consultationsList))
                          @foreach($consultationsList as $c)
                            <option data-type="consultation" value="consultation:{{ $c->id }}">
                              Consultation #{{ $c->id }} — {{ optional($c->date_consultation)->format('d/m/Y') ?? '' }}
                            </option>
                          @endforeach
                        @endif
                        @if(!empty($analysesList))
                          @foreach($analysesList as $a)
                            <option data-type="analyse" value="analyse:{{ $a->id }}">
                              Analyse #{{ $a->id }} — {{ $a->type_analyse ?? $a->type ?? '—' }}
                            </option>
                          @endforeach
                        @endif
                      </select>
                      <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                  </div>
                  
                  <div class="field-wrapper">
                    <label class="field-title">
                      <i class="bi bi-tag"></i>
                      <span>Libellé du paiement</span>
                      <small>Description qui apparaîtra sur votre reçu</small>
                    </label>
                    <input type="text" name="label" id="label" class="balanced-input" value="Ticket de consultation" placeholder="Description du paiement">
                  </div>
                </div>
              </div>

              <!-- Méthode de paiement -->
              <div class="form-section">
                <div class="section-title">
                  <h4><i class="bi bi-credit-card-2-back"></i>Méthode de paiement</h4>
                  <p>Choisissez votre mode de paiement préféré</p>
                </div>
                
                <div class="payment-methods-balanced">
                  <input type="radio" name="provider" value="wave" id="wave" checked>
                  <label for="wave" class="payment-method-balanced">
                    <div class="method-icon wave">
                      <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="method-details">
                      <h6>Wave Money</h6>
                      <p>Paiement mobile rapide et sécurisé</p>
                    </div>
                    <span class="method-tag recommended">Recommandé</span>
                  </label>
                  
                  <input type="radio" name="provider" value="orangemoney" id="orange">
                  <label for="orange" class="payment-method-balanced">
                    <div class="method-icon orange">
                      <i class="bi bi-phone"></i>
                    </div>
                    <div class="method-details">
                      <h6>Orange Money</h6>
                      <p>Portefeuille électronique sécurisé</p>
                    </div>
                    <span class="method-tag fast">Rapide</span>
                  </label>
                </div>
              </div>

              <!-- Récapitulatif équilibré -->
              <div class="balanced-summary">
                <div class="summary-header">
                  <h5><i class="bi bi-receipt"></i>Récapitulatif</h5>
                  <span class="ready-indicator">Prêt à payer</span>
                </div>
                
                <div class="summary-body">
                  <input type="hidden" name="amount" id="amount" value="{{ $priceConsult }}">
                  
                  <div class="summary-row">
                    <span class="row-label"><i class="bi bi-tag-fill"></i>Service</span>
                    <span class="row-value" id="summary-type">Consultation</span>
                  </div>
                  
                  <div class="summary-row">
                    <span class="row-label"><i class="bi bi-wallet2"></i>Méthode</span>
                    <span class="row-value" id="summary-provider">Wave Money</span>
                  </div>
                  
                  <div class="summary-divider"></div>
                  
                  <div class="summary-total">
                    <span class="total-label">Total à payer</span>
                    <div class="total-amount">
                      <span class="amount-number" id="summary-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                      <span class="amount-currency">XOF</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bouton de paiement centré -->
              <div class="payment-action-center">
                <button type="submit" class="balanced-pay-button" id="payButton">
                  <div class="button-main">
                    <i class="bi bi-shield-check"></i>
                    <span>Procéder au paiement sécurisé</span>
                  </div>
                  <div class="button-amount" id="button-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }} XOF</div>
                </button>
                
                <div class="security-badges">
                  <div class="security-badge">
                    <i class="bi bi-lock-fill"></i>
                    <span>Cryptage SSL 256-bit</span>
                  </div>
                  <div class="security-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>Paiement 100% sécurisé</span>
                  </div>
                  <div class="security-badge">
                    <i class="bi bi-lightning-charge"></i>
                    <span>Traitement instantané</span>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Historique avec espacement correct -->
          <div class="balanced-history-card">
            <div class="history-card-header">
              <div class="header-left">
                <div class="history-icon">
                  <i class="bi bi-clock-history"></i>
                </div>
                <div>
                  <h3>Historique des transactions</h3>
                  <p>Consultez vos paiements et téléchargez vos reçus</p>
                </div>
              </div>
              
              <div class="history-filters">
                <button class="filter-button active" data-filter="all">
                  <i class="bi bi-list"></i>
                  <span>Tous</span>
                  <span class="filter-count">{{ $orders->count() }}</span>
                </button>
                <button class="filter-button" data-filter="paid">
                  <i class="bi bi-check-circle"></i>
                  <span>Payés</span>
                  <span class="filter-count">{{ $orders->where('status', 'paid')->count() }}</span>
                </button>
                <button class="filter-button" data-filter="pending">
                  <i class="bi bi-clock"></i>
                  <span>En attente</span>
                  <span class="filter-count">{{ $orders->where('status', 'pending')->count() }}</span>
                </button>
              </div>
            </div>
            
            <div class="transactions-balanced-list">
              @forelse($orders as $order)
                @php($item = $order->items->first())
                <div class="transaction-balanced-item" data-status="{{ $order->status }}">
                  <div class="transaction-header">
                    <div class="status-circle {{ $order->status }}">
                      @if($order->status === 'paid')
                        <i class="bi bi-check-circle-fill"></i>
                      @elseif($order->status === 'pending')
                        <i class="bi bi-clock-fill"></i>
                      @else
                        <i class="bi bi-x-circle-fill"></i>
                      @endif
                    </div>
                    <div class="transaction-info">
                      <h6>{{ $item->label ?? 'Paiement médical' }}</h6>
                      <small>#{{ $item->ticket_number ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                    <span class="status-tag {{ $order->status }}">
                      {{ $order->status === 'paid' ? 'Payé' : ($order->status === 'pending' ? 'En attente' : ucfirst($order->status)) }}
                    </span>
                  </div>
                  
                  <div class="transaction-details">
                    <div class="detail-group">
                      <i class="bi bi-calendar3"></i>
                      <span>{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="detail-group">
                      <i class="bi bi-wallet2"></i>
                      <span>{{ ucfirst($order->provider ?? 'Non spécifié') }}</span>
                    </div>
                    <div class="transaction-total">
                      <span class="total-value">{{ number_format($order->total_amount, 0, ',', ' ') }}</span>
                      <span class="total-currency">XOF</span>
                    </div>
                  </div>
                  
                  <div class="transaction-actions">
                    @if($order->status === 'pending' && $order->payment_url)
                      <a href="{{ $order->payment_url }}" class="action-button continue">
                        <i class="bi bi-play-circle"></i>
                        <span>Continuer</span>
                      </a>
                    @elseif($order->status === 'paid')
                      <a href="{{ route('payments.receipt', $order->id) }}" class="action-button receipt">
                        <i class="bi bi-download"></i>
                        <span>Télécharger reçu</span>
                      </a>
                    @endif
                  </div>
                </div>
              @empty
                <div class="empty-transactions">
                  <div class="empty-icon">
                    <i class="bi bi-credit-card-2-front"></i>
                  </div>
                  <h4>Aucune transaction</h4>
                  <p>Vous n'avez effectué aucun paiement pour le moment.</p>
                  <p>Vos transactions apparaîtront ici après votre premier paiement.</p>
                </div>
              @endforelse
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* =============== VARIABLES CSS ÉQUILIBRÉES =============== */
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
  --gray-300: #cbd5e1;
  --gray-400: #94a3b8;
  --gray-500: #64748b;
  --gray-600: #475569;
  --gray-700: #334155;
  --gray-800: #1e293b;
  --gray-900: #0f172a;
  
  --gradient-primary: linear-gradient(135deg, #10b981 0%, #047857 100%);
  --gradient-secondary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #475569 100%);
  
  --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.06);
  --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.08);
  --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 16px 32px rgba(0, 0, 0, 0.12);
  
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
  --radius-2xl: 24px;
  
  --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* =============== LAYOUT ÉQUILIBRÉ =============== */
.balanced-payment-wrapper {
  min-height: 100vh;
  background: var(--gray-50);
  padding: 1.5rem 0;
  font-family: 'Inter', sans-serif;
}

.balanced-layout {
  margin: 0;
  max-width: 1400px;
  margin: 0 auto;
}

.compact-sidebar {
  position: sticky;
  top: 1.5rem;
}

.main-content-wrapper {
  padding-left: 1.5rem;
}

/* =============== HEADER ÉQUILIBRÉ =============== */
.balanced-header {
  background: var(--gradient-hero);
  border-radius: var(--radius-2xl);
  padding: 2.5rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-xl);
  position: relative;
  overflow: hidden;
}

.balanced-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: conic-gradient(from 0deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1), rgba(245, 158, 11, 0.1), rgba(16, 185, 129, 0.1));
  animation: rotate 20s linear infinite;
  opacity: 0.7;
}

@keyframes rotate {
  to { transform: rotate(360deg); }
}

.header-container {
  position: relative;
  z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  color: white;
}

.header-icon-wrapper {
  width: 70px;
  height: 70px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  flex-shrink: 0;
}

.header-content h1 {
  font-size: 2.25rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  letter-spacing: -0.025em;
}

.header-content p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 0.75rem;
}

.breadcrumb-modern {
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

.btn-dashboard-return {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md);
  color: white;
  text-decoration: none;
  font-weight: 600;
  transition: all var(--transition);
}

.btn-dashboard-return:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-2px);
  color: white;
}

.stats-display {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md);
  padding: 1rem 1.5rem;
}

.stat-box {
  text-align: center;
  color: white;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 800;
  line-height: 1;
}

.stat-text {
  font-size: 0.75rem;
  opacity: 0.8;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.stat-separator {
  width: 1px;
  height: 32px;
  background: rgba(255, 255, 255, 0.2);
}

/* =============== ALERTS ÉQUILIBRÉES =============== */
.balanced-alert {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.25rem;
  border-radius: var(--radius-lg);
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-md);
  position: relative;
  animation: slideInAlert 0.3s ease;
}

@keyframes slideInAlert {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.success-alert {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  border-left: 4px solid var(--success);
}

.error-alert {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  border-left: 4px solid var(--danger);
}

.alert-indicator {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.125rem;
  flex-shrink: 0;
}

.success-alert .alert-indicator { background: var(--success); }
.error-alert .alert-indicator { background: var(--danger); }

.alert-message h6 {
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.alert-dismiss {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: none;
  border: none;
  color: var(--gray-500);
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 50%;
  transition: all var(--transition);
}

.alert-dismiss:hover {
  background: rgba(0, 0, 0, 0.1);
}

/* =============== CARTE DE PAIEMENT ÉQUILIBRÉE =============== */
.balanced-payment-card {
  background: white;
  border-radius: var(--radius-2xl);
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
}

.payment-card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--gray-200);
}

.header-icon-badge {
  width: 56px;
  height: 56px;
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  flex-shrink: 0;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.25);
}

.header-info h3 {
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  color: var(--gray-900);
}

.header-info p {
  color: var(--gray-600);
  margin: 0;
}

.security-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: var(--radius-md);
  color: var(--primary);
  font-size: 0.875rem;
  font-weight: 600;
}

/* =============== SECTIONS DE FORMULAIRE =============== */
.form-section {
  margin-bottom: 2.5rem;
}

.section-title {
  margin-bottom: 1.5rem;
}

.section-title h4 {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-title h4 i {
  color: var(--primary);
}

.section-title p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin: 0;
}

/* =============== GRILLE DE SERVICES ÉQUILIBRÉE =============== */
.services-balanced-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
}

.service-option-balanced {
  display: block;
  background: white;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  cursor: pointer;
  transition: all var(--transition);
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  min-height: 280px;
  display: flex;
  flex-direction: column;
}

.service-option-balanced:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

input[type="radio"]:checked + .service-option-balanced {
  border-color: var(--primary);
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.03), rgba(16, 185, 129, 0.01));
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), var(--shadow-lg);
  transform: translateY(-2px);
}

input[type="radio"] {
  display: none;
}

.service-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.service-icon {
  width: 56px;
  height: 56px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  box-shadow: var(--shadow-md);
}

.service-icon.consultation { background: var(--gradient-primary); }
.service-icon.analyse { background: var(--gradient-secondary); }
.service-icon.acte { background: linear-gradient(135deg, #f59e0b, #d97706); }

.service-tag {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.service-tag.popular {
  background: linear-gradient(135deg, #fef3c7, #fed7aa);
  color: #92400e;
}

.service-tag.premium {
  background: linear-gradient(135deg, #ddd6fe, #c7d2fe);
  color: #5b21b6;
}

.service-tag.special {
  background: linear-gradient(135deg, #fce7f3, #fbcfe8);
  color: #be185d;
}

.service-middle {
  flex-grow: 1;
  margin-bottom: 1rem;
}

.service-middle h5 {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.service-middle p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.service-benefits {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.service-benefits span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.75rem;
}

.service-benefits i {
  color: var(--success);
  font-size: 0.875rem;
}

.service-bottom {
  display: flex;
  align-items: baseline;
  justify-content: center;
  padding-top: 1rem;
  border-top: 1px solid var(--gray-200);
  margin-top: auto;
}

.price-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: var(--primary);
  line-height: 1;
}

.price-unit {
  font-size: 0.875rem;
  color: var(--gray-500);
  margin-left: 0.5rem;
}

/* =============== DÉTAILS ÉQUILIBRÉS =============== */
.details-balanced-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .details-balanced-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.field-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.field-title {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.field-title span {
  font-weight: 600;
  color: var(--gray-900);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.field-title i {
  color: var(--primary);
}

.field-title small {
  color: var(--gray-500);
  font-size: 0.75rem;
}

.select-container {
  position: relative;
}

.balanced-select, .balanced-input {
  width: 100%;
  padding: 1rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-md);
  background: white;
  font-size: 0.9rem;
  transition: all var(--transition);
}

.balanced-select:focus, .balanced-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.select-arrow {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  pointer-events: none;
}

/* =============== MÉTHODES DE PAIEMENT ÉQUILIBRÉES =============== */
.payment-methods-balanced {
  display: grid;
  gap: 1rem;
}

.payment-method-balanced {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition);
  background: white;
}

.payment-method-balanced:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow-md);
}

input[type="radio"]:checked + .payment-method-balanced {
  border-color: var(--primary);
  background: rgba(16, 185, 129, 0.03);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.method-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.method-icon.wave { background: var(--gradient-primary); }
.method-icon.orange { background: linear-gradient(135deg, #f97316, #ea580c); }

.method-details h6 {
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.method-details p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin: 0;
}

.method-tag {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  margin-left: auto;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.method-tag.recommended {
  background: linear-gradient(135deg, #fef3c7, #fed7aa);
  color: #92400e;
}

.method-tag.fast {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
}

/* =============== RÉCAPITULATIF ÉQUILIBRÉ =============== */
.balanced-summary {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(16, 185, 129, 0.1);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-md);
  position: relative;
}

.balanced-summary::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  border-radius: var(--radius-xl) var(--radius-xl) 0 0;
}

.summary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
}

.summary-header h5 {
  font-weight: 700;
  color: var(--gray-900);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

.summary-header i {
  color: var(--primary);
}

.ready-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(16, 185, 129, 0.1);
  color: var(--primary);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 600;
}

.ready-indicator::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--gray-200);
}

.summary-row:last-of-type {
  border-bottom: none;
}

.row-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-weight: 500;
}

.row-label i {
  color: var(--primary);
}

.summary-divider {
  height: 1px;
  background: var(--gray-200);
  margin: 1rem 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: rgba(16, 185, 129, 0.05);
  border-radius: var(--radius-md);
  margin-top: 1rem;
}

.total-label {
  font-weight: 700;
  font-size: 1.125rem;
  color: var(--gray-900);
}

.total-amount {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.amount-number {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--primary);
}

.amount-currency {
  color: var(--gray-500);
}

/* =============== BOUTON DE PAIEMENT CENTRÉ =============== */
.payment-action-center {
  text-align: center;
}

.balanced-pay-button {
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 400px;
  padding: 1.25rem 2rem;
  background: var(--gradient-primary);
  border: none;
  border-radius: var(--radius-lg);
  color: white;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all var(--transition);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.25);
  position: relative;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.balanced-pay-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.balanced-pay-button:hover::before {
  left: 100%;
}

.balanced-pay-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(16, 185, 129, 0.35);
}

.button-main {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.button-amount {
  font-size: 1.125rem;
  font-weight: 800;
}

.security-badges {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.security-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.875rem;
}

.security-badge i {
  color: var(--primary);
}

/* =============== HISTORIQUE ÉQUILIBRÉ =============== */
.balanced-history-card {
  background: white;
  border-radius: var(--radius-2xl);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
}

.history-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  gap: 2rem;
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
}

.history-icon {
  width: 56px;
  height: 56px;
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  flex-shrink: 0;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.25);
}

.header-left h3 {
  font-size: 1.375rem;
  font-weight: 800;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.header-left p {
  color: var(--gray-600);
  margin: 0;
}

.history-filters {
  display: flex;
  gap: 0.5rem;
  background: var(--gray-100);
  padding: 0.25rem;
  border-radius: var(--radius-md);
}

.filter-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: transparent;
  border: none;
  border-radius: var(--radius-sm);
  color: var(--gray-600);
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition);
  font-size: 0.875rem;
}

.filter-button.active {
  background: var(--primary);
  color: white;
  box-shadow: var(--shadow-sm);
}

.filter-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.125rem 0.5rem;
  border-radius: var(--radius-sm);
  font-size: 0.75rem;
  margin-left: 0.25rem;
}

.filter-button.active .filter-count {
  background: rgba(255, 255, 255, 0.3);
}

/* =============== TRANSACTIONS ÉQUILIBRÉES =============== */
.transactions-balanced-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.transaction-balanced-item {
  background: white;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  transition: all var(--transition);
  position: relative;
  overflow: hidden;
}

.transaction-balanced-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.03), transparent);
  transition: left 0.6s ease;
}

.transaction-balanced-item:hover::before {
  left: 100%;
}

.transaction-balanced-item:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

.transaction-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}

.status-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  flex-shrink: 0;
}

.status-circle.paid { background: var(--success); }
.status-circle.pending { background: var(--warning); }
.status-circle.failed { background: var(--danger); }

.transaction-info h6 {
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.transaction-info small {
  color: var(--gray-500);
}

.status-tag {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  margin-left: auto;
}

.status-tag.paid {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
}

.status-tag.pending {
  background: rgba(245, 158, 11, 0.1);
  color: var(--warning);
}

.status-tag.failed {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
}

.transaction-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.detail-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.875rem;
}

.detail-group i {
  color: var(--primary);
}

.transaction-total {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.total-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.total-currency {
  color: var(--gray-500);
  font-size: 0.875rem;
}

.transaction-actions {
  display: flex;
  gap: 0.75rem;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all var(--transition);
}

.action-button.continue {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
}

.action-button.receipt {
  background: rgba(59, 130, 246, 0.1);
  color: var(--secondary);
}

.action-button:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* =============== ÉTAT VIDE =============== */
.empty-transactions {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--gray-500);
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-transactions h4 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
  color: var(--gray-700);
}

.empty-transactions p {
  margin-bottom: 0.5rem;
}

/* =============== RESPONSIVE ÉQUILIBRÉ =============== */
@media (max-width: 1200px) {
  .main-content-wrapper {
    padding-left: 1rem;
  }
}

@media (max-width: 992px) {
  .main-content-wrapper {
    padding-left: 0;
    margin-top: 1.5rem;
  }
  
  .balanced-header {
    padding: 2rem;
  }
  
  .header-container {
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
}

@media (max-width: 768px) {
  .balanced-payment-wrapper {
    padding: 1rem 0;
  }
  
  .balanced-payment-card, .balanced-history-card {
    padding: 1.5rem;
  }
  
  .services-balanced-grid {
    grid-template-columns: 1fr;
  }
  
  .details-balanced-grid {
    grid-template-columns: 1fr;
  }
  
  .payment-methods-balanced {
    grid-template-columns: 1fr;
  }
  
  .history-card-header {
    flex-direction: column;
    gap: 1rem;
  }
  
  .header-left {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .history-filters {
    align-self: stretch;
  }
  
  .filter-button {
    flex: 1;
    justify-content: center;
  }
  
  .transaction-details {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .transaction-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .security-badges {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .balanced-pay-button {
    flex-direction: column;
    gap: 0.75rem;
    text-align: center;
  }
  
  .payment-card-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .security-indicator {
    margin: 0;
    align-self: center;
  }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Prix des services
  const prices = {
    consultation: {{ (int)($priceConsult ?? 5000) }},
    analyse: {{ (int)($priceAnalyse ?? 10000) }},
    acte: {{ (int)($priceActe ?? 7000) }}
  };
  
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
  document.querySelectorAll('.filter-button').forEach(btn => {
    btn.addEventListener('click', function() {
      // Retirer la classe active de tous les boutons
      document.querySelectorAll('.filter-button').forEach(b => b.classList.remove('active'));
      
      // Ajouter la classe active au bouton cliqué
      this.classList.add('active');
      
      const filter = this.dataset.filter;
      
      // Filtrer les transactions
      document.querySelectorAll('.transaction-balanced-item').forEach(item => {
        const status = item.dataset.status;
        
        if (filter === 'all' || status === filter) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  // Animation du bouton de paiement lors de la soumission
  const payButton = document.getElementById('payButton');
  if (payButton) {
    payButton.addEventListener('click', function() {
      this.innerHTML = `
        <div class="button-main">
          <i class="bi bi-hourglass-split"></i>
          <span>Traitement en cours...</span>
        </div>
      `;
      this.disabled = true;
    });
  }

  // Animations d'entrée pour les cartes
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.balanced-payment-card, .balanced-history-card, .service-option-balanced').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(15px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });

  // Initialiser l'affichage
  updatePayment();
});
</script>
@endpush
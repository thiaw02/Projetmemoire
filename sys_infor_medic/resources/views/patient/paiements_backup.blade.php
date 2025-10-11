@extends('layouts.app')

@section('content')
<div class="optimized-payment-layout">
  <div class="container-fluid">
    <div class="row payment-row">
      <div class="col-xl-2 col-lg-3 col-md-4">
        <div class="compact-sidebar-wrapper">
          @include('layouts.partials.profile_sidebar')
        </div>
      </div>
      
      <div class="col-xl-10 col-lg-9 col-md-8">
        <div class="main-payment-content">
    
    {{-- Header moderne avec glassmorphisme --}}
    <div class="modern-header">
      <div class="header-background"></div>
      <div class="header-content">
        <div class="header-left">
          <div class="header-icon">
            <i class="bi bi-credit-card-2-front"></i>
          </div>
          <div class="header-text">
            <h1>Centre de Paiement</h1>
            <p>Gérez vos transactions médicales en toute sécurité</p>
            <div class="header-breadcrumb">
              <span>Accueil</span>
              <i class="bi bi-chevron-right"></i>
              <span>Paiements</span>
            </div>
          </div>
        </div>
        <div class="header-actions">
          <a href="{{ route('patient.dashboard') }}" class="btn-back-modern">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
          </a>
          <div class="header-stats">
            <div class="stat-item">
              <span class="stat-value">{{ $orders->where('status', 'paid')->count() }}</span>
              <span class="stat-label">Payé</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <span class="stat-value">{{ $orders->where('status', 'pending')->count() }}</span>
              <span class="stat-label">En attente</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Messages modernes avec animations --}}
    @if(session('success'))
      <div class="modern-alert alert-success">
        <div class="alert-icon">
          <i class="bi bi-check-circle-fill"></i>
        </div>
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
      <div class="modern-alert alert-danger">
        <div class="alert-icon">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="alert-content">
          <h6>Erreur</h6>
          <p>{{ $errors->first() }}</p>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove()">
          <i class="bi bi-x"></i>
        </button>
      </div>
    @endif

    {{-- Formulaire de paiement moderne --}}
    <div class="modern-payment-card">
      <div class="card-header">
        <div class="card-icon">
          <i class="bi bi-plus-circle-fill"></i>
        </div>
        <div class="card-title">
          <h3>Nouveau Paiement</h3>
          <p>Sélectionnez le type de service et procédez au paiement</p>
        </div>
        <div class="card-status">
          <div class="status-indicator secure">
            <i class="bi bi-shield-check"></i>
            <span>Paiement Sécurisé</span>
          </div>
        </div>
      </div>
      
      <form method="POST" action="{{ route('patient.payments.checkout') }}" class="modern-payment-form">
        @csrf
        
        {{-- Sélection du service premium --}}
        <div class="service-selection-section">
          <div class="section-header">
            <h4><i class="bi bi-card-checklist me-2"></i>Sélectionnez votre service</h4>
            <p>Choisissez le type de service médical que vous souhaitez payer</p>
          </div>
          
          <div class="services-grid">
            <input type="radio" name="kind" value="consultation" id="service-consultation" checked>
            <label for="service-consultation" class="service-card consultation">
              <div class="service-header">
                <div class="service-icon">
                  <i class="bi bi-person-heart"></i>
                </div>
                <div class="service-badge">Populaire</div>
              </div>
              <div class="service-content">
                <h5>Consultation</h5>
                <p>Consultation médicale avec un professionnel de santé</p>
                <div class="service-features">
                  <span><i class="bi bi-check2"></i>Diagnostic complet</span>
                  <span><i class="bi bi-check2"></i>Prescription si nécessaire</span>
                </div>
              </div>
              <div class="service-price">
                <span class="price-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                <span class="price-currency">XOF</span>
              </div>
              <div class="service-selected">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </label>
            
            <input type="radio" name="kind" value="analyse" id="service-analyse">
            <label for="service-analyse" class="service-card analyse">
              <div class="service-header">
                <div class="service-icon">
                  <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="service-badge premium">Premium</div>
              </div>
              <div class="service-content">
                <h5>Analyse médicale</h5>
                <p>Examens et analyses complètes en laboratoire</p>
                <div class="service-features">
                  <span><i class="bi bi-check2"></i>Analyses de sang</span>
                  <span><i class="bi bi-check2"></i>Résultats détaillés</span>
                </div>
              </div>
              <div class="service-price">
                <span class="price-amount">{{ number_format($priceAnalyse ?? 10000, 0, ',', ' ') }}</span>
                <span class="price-currency">XOF</span>
              </div>
              <div class="service-selected">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </label>
            
            <input type="radio" name="kind" value="acte" id="service-acte">
            <label for="service-acte" class="service-card acte">
              <div class="service-header">
                <div class="service-icon">
                  <i class="bi bi-bandaid"></i>
                </div>
                <div class="service-badge special">Étude spéciale</div>
              </div>
              <div class="service-content">
                <h5>Acte médical</h5>
                <p>Interventions et procédures médicales spécialisées</p>
                <div class="service-features">
                  <span><i class="bi bi-check2"></i>Procédures techniques</span>
                  <span><i class="bi bi-check2"></i>Suivi post-intervention</span>
                </div>
              </div>
              <div class="service-price">
                <span class="price-amount">{{ number_format($priceActe ?? 7000, 0, ',', ' ') }}</span>
                <span class="price-currency">XOF</span>
              </div>
              <div class="service-selected">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </label>
          </div>
        </div>
        
        {{-- Section des détails premium --}}
        <div class="payment-details-section">
          <div class="section-header">
            <h4><i class="bi bi-gear me-2"></i>Détails du paiement</h4>
            <p>Personnalisez votre paiement avec les options avancées</p>
          </div>
          
          <div class="details-grid">
            <div class="detail-group">
              <label class="modern-label">
                <i class="bi bi-link-45deg"></i>
                <span>Référence associée</span>
                <small>Optionnel - Lier à un rendez-vous existant</small>
              </label>
              <div class="modern-select-wrapper">
                <select name="ref_id" id="ref_id" class="modern-select">
                  <option value="">— Aucune référence —</option>
                  @if(!empty($consultationsList))
                    @foreach($consultationsList as $c)
                      <option data-type="consultation" value="consultation:{{ $c->id }}">
                        <i class="bi bi-calendar-check"></i> Consultation #{{ $c->id }} — {{ optional($c->date_consultation)->format('d/m/Y') ?? '' }}
                      </option>
                    @endforeach
                  @endif
                  @if(!empty($analysesList))
                    @foreach($analysesList as $a)
                      <option data-type="analyse" value="analyse:{{ $a->id }}">
                        <i class="bi bi-clipboard-data"></i> Analyse #{{ $a->id }} — {{ $a->type_analyse ?? $a->type ?? '—' }}
                      </option>
                    @endforeach
                  @endif
                </select>
                <i class="bi bi-chevron-down select-arrow"></i>
              </div>
            </div>
            
            <div class="detail-group">
              <label class="modern-label">
                <i class="bi bi-credit-card-2-back"></i>
                <span>Méthode de paiement</span>
                <small>Choisissez votre portefeuille électronique</small>
              </label>
              <div class="payment-methods">
                <input type="radio" name="provider" value="wave" id="method-wave" checked>
                <label for="method-wave" class="payment-method wave">
                  <div class="method-icon">
                    <i class="bi bi-wallet2"></i>
                  </div>
                  <div class="method-info">
                    <h6>Wave Money</h6>
                    <p>Paiement rapide et sécurisé</p>
                  </div>
                  <div class="method-badge">Recommandé</div>
                </label>
                
                <input type="radio" name="provider" value="orangemoney" id="method-orange">
                <label for="method-orange" class="payment-method orange">
                  <div class="method-icon">
                    <i class="bi bi-phone"></i>
                  </div>
                  <div class="method-info">
                    <h6>Orange Money</h6>
                    <p>Paiement mobile sécurisé</p>
                  </div>
                  <div class="method-features">
                    <span class="feature">Rapide</span>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>
        
        {{-- Section montant et libellé --}}
        <div class="amount-section">
          <div class="amount-display-wrapper">
            <label class="modern-label">
              <i class="bi bi-currency-exchange"></i>
              <span>Montant à payer</span>
            </label>
            <input type="hidden" name="amount" id="amount" value="{{ $priceConsult }}">
            <div class="premium-amount-display" id="amount-display">
              <span class="amount-value">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
              <span class="amount-currency">XOF</span>
            </div>
          </div>
          
          <div class="label-group">
            <label class="modern-label">
              <i class="bi bi-tag"></i>
              <span>Libellé du paiement</span>
              <small>Description qui apparaîtra sur votre reçu</small>
            </label>
            <input type="text" name="label" id="label" class="modern-input" value="Ticket de consultation" placeholder="Entrez une description...">
          </div>
        </div>
        
        {{-- Récapitulatif premium --}}
        <div class="premium-summary">
          <div class="summary-header">
            <h5><i class="bi bi-receipt me-2"></i>Récapitulatif de la commande</h5>
            <div class="summary-indicator">
              <span class="indicator-dot"></span>
              <span>Prêt à payer</span>
            </div>
          </div>
          
          <div class="summary-content">
            <div class="summary-line">
              <span class="line-label">
                <i class="bi bi-tag-fill"></i>
                Service sélectionné
              </span>
              <span class="line-value" id="summary-type">Consultation</span>
            </div>
            
            <div class="summary-line">
              <span class="line-label">
                <i class="bi bi-wallet2"></i>
                Méthode de paiement
              </span>
              <span class="line-value" id="summary-provider">Wave Money</span>
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-total">
              <span class="total-label">Montant total</span>
              <span class="total-value" id="summary-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }} XOF</span>
            </div>
          </div>
        </div>
        
        {{-- Bouton de paiement premium --}}
        <div class="payment-action">
          <button type="submit" class="premium-pay-button" id="payButton">
            <div class="button-content">
              <i class="bi bi-shield-check"></i>
              <span class="button-text">Procéder au paiement sécurisé</span>
            </div>
            <div class="button-amount" id="button-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }} XOF</div>
          </button>
          
          <div class="security-badges">
            <div class="security-badge">
              <i class="bi bi-lock-fill"></i>
              <span>Cryptage SSL</span>
            </div>
            <div class="security-badge">
              <i class="bi bi-shield-check"></i>
              <span>Paiement sécurisé</span>
            </div>
            <div class="security-badge">
              <i class="bi bi-hourglass-split"></i>
              <span>Traitement instantané</span>
            </div>
          </div>
        </div>
      </form>
    </div>

    {{-- Section historique premium --}}
    <div class="modern-history-card">
      <div class="history-header">
        <div class="header-left">
          <div class="history-icon">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="header-content">
            <h3>Historique des transactions</h3>
            <p>Consultez toutes vos transactions et téléchargez vos reçus</p>
          </div>
        </div>
        
        <div class="history-filters">
          <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">
              <span class="tab-icon"><i class="bi bi-list"></i></span>
              <span class="tab-text">Tous</span>
              <span class="tab-count">{{ $orders->count() }}</span>
            </button>
            <button class="filter-tab" data-filter="paid">
              <span class="tab-icon"><i class="bi bi-check-circle"></i></span>
              <span class="tab-text">Payés</span>
              <span class="tab-count">{{ $orders->where('status', 'paid')->count() }}</span>
            </button>
            <button class="filter-tab" data-filter="pending">
              <span class="tab-icon"><i class="bi bi-clock"></i></span>
              <span class="tab-text">En attente</span>
              <span class="tab-count">{{ $orders->where('status', 'pending')->count() }}</span>
            </button>
          </div>
        </div>
      </div>
      
      <div class="transactions-container">
        @forelse($orders as $o)
          @php($item = $o->items->first())
          <div class="premium-transaction-card" data-status="{{ $o->status }}">
            <div class="transaction-header">
              <div class="transaction-status">
                <div class="status-indicator {{ $o->status }}">
                  @if($o->status === 'paid')
                    <i class="bi bi-check-circle-fill"></i>
                  @elseif($o->status === 'pending')
                    <i class="bi bi-clock-fill"></i>
                  @else
                    <i class="bi bi-x-circle-fill"></i>
                  @endif
                </div>
                <div class="status-text">
                  <h6>{{ $item->label ?? 'Paiement médical' }}</h6>
                  <small>#{{ $item->ticket_number ?? str_pad($o->id, 6, '0', STR_PAD_LEFT) }}</small>
                </div>
              </div>
              
              <div class="transaction-badge">
                <span class="badge-{{ $o->status }}">
                  {{ $o->status === 'paid' ? 'Payé' : ($o->status === 'pending' ? 'En attente' : ucfirst($o->status)) }}
                </span>
              </div>
            </div>
            
            <div class="transaction-content">
              <div class="transaction-details">
                <div class="detail-item">
                  <i class="bi bi-calendar3"></i>
                  <span>{{ $o->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="detail-item">
                  <i class="bi bi-wallet2"></i>
                  <span>{{ ucfirst($o->provider ?? 'Non spécifié') }}</span>
                </div>
              </div>
              
              <div class="transaction-amount">
                <span class="amount">{{ number_format($o->total_amount, 0, ',', ' ') }}</span>
                <span class="currency">XOF</span>
              </div>
            </div>
            
            <div class="transaction-actions">
              @if($o->status === 'pending' && $o->payment_url)
                <a href="{{ $o->payment_url }}" class="action-btn continue">
                  <i class="bi bi-play-circle"></i>
                  <span>Continuer</span>
                </a>
              @elseif($o->status === 'paid')
                <a href="{{ route('payments.receipt', $o->id) }}" class="action-btn receipt">
                  <i class="bi bi-download"></i>
                  <span>Télécharger reçu</span>
                </a>
              @endif
              
              <button class="action-btn details" onclick="toggleTransactionDetails({{ $o->id }})">
                <i class="bi bi-info-circle"></i>
                <span>Détails</span>
              </button>
            </div>
            
            <div class="transaction-details-panel" id="details-{{ $o->id }}" style="display: none;">
              <div class="details-grid">
                <div class="detail-group">
                  <label>ID Transaction</label>
                  <span>{{ $o->id }}</span>
                </div>
                <div class="detail-group">
                  <label>Montant</label>
                  <span>{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</span>
                </div>
                <div class="detail-group">
                  <label>Statut</label>
                  <span class="status-{{ $o->status }}">{{ ucfirst($o->status) }}</span>
                </div>
                <div class="detail-group">
                  <label>Date</label>
                  <span>{{ $o->created_at->format('d/m/Y à H:i:s') }}</span>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="empty-state">
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
@endsection

@section('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

  /* Variables CSS ultra-modernes */
  :root {
    /* Couleurs primaires */
    --primary: #10b981;
    --primary-dark: #047857;
    --primary-light: #34d399;
    --primary-ultra-light: #d1fae5;
    
    /* Couleurs secondaires */
    --secondary: #3b82f6;
    --secondary-dark: #1d4ed8;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --success: #10b981;
    
    /* Nuances de gris */
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
    
    /* Gradients ultra-modernes */
    --gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-secondary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --gradient-warm: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --gradient-cool: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    --gradient-card: linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.8) 100%);
    --gradient-glass: linear-gradient(145deg, rgba(255,255,255,0.25), rgba(255,255,255,0.05));
    --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #475569 100%);
    
    /* Ombres multiples */
    --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.4);
    --shadow-glow-lg: 0 0 40px rgba(16, 185, 129, 0.3);
    
    /* Transitions fluides */
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-bounce: 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    
    /* Rayons de bordure */
    --radius-xs: 4px;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --radius-2xl: 24px;
    --radius-3xl: 32px;
    
    /* Espacements */
    --space-xs: 0.5rem;
    --space-sm: 1rem;
    --space-md: 1.5rem;
    --space-lg: 2rem;
    --space-xl: 3rem;
    --space-2xl: 4rem;
  }

  /* =============== STYLES DE BASE =============== */
  
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--gray-50) !important;
    color: var(--gray-900);
    line-height: 1.6;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
  
  .modern-payment-container {
    min-height: 100vh;
    padding: var(--space-lg) 0;
  }
  
  .sidebar-sticky {
    position: sticky;
    top: var(--space-lg);
  }
  
  /* =============== HEADER MODERNE =============== */
  
  .modern-header {
    position: relative;
    background: var(--gradient-hero);
    border-radius: var(--radius-3xl);
    padding: var(--space-xl);
    margin-bottom: var(--space-lg);
    overflow: hidden;
    box-shadow: var(--shadow-2xl);
  }
  
  .header-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: conic-gradient(from 0deg at 50% 50%, 
                rgba(16, 185, 129, 0.1) 0deg,
                rgba(59, 130, 246, 0.1) 120deg,
                rgba(245, 158, 11, 0.1) 240deg,
                rgba(16, 185, 129, 0.1) 360deg);
    animation: rotate 20s linear infinite;
    opacity: 0.7;
  }
  
  @keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .header-content {
    position: relative;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-lg);
  }
  
  .header-left {
    display: flex;
    align-items: flex-start;
    gap: var(--space-md);
  }
  
  .header-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-glass);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: var(--shadow-xl);
    flex-shrink: 0;
  }
  
  .header-text h1 {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: var(--space-xs);
    letter-spacing: -0.025em;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .header-text p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.125rem;
    margin-bottom: var(--space-sm);
    font-weight: 400;
  }
  
  .header-breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .header-breadcrumb i {
    font-size: 0.75rem;
  }
  
  .header-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-md);
  }
  
  .btn-back-modern {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    padding: var(--space-sm) var(--space-md);
    background: var(--gradient-glass);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-lg);
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-md);
  }
  
  .btn-back-modern:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
  }
  
  .header-stats {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    background: var(--gradient-glass);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-lg);
    padding: var(--space-sm) var(--space-md);
    box-shadow: var(--shadow-md);
  }
  
  .stat-item {
    text-align: center;
  }
  
  .stat-value {
    display: block;
    color: white;
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
  }
  
  .stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }
  
  .stat-divider {
    width: 1px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
  }
  
  /* Container principal */
  .container-lg {
    padding-top: 1.5rem;
    padding-bottom: 3rem;
  }
  
  /* Header stylisé avec animation */
  .pay-header {
    background: var(--pay-gradient-primary);
    color: white;
    padding: 2.5rem;
    border-radius: 24px;
    margin-bottom: 2rem;
    box-shadow: var(--pay-shadow-lg);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .pay-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: rotate 8s linear infinite;
  }
  
  @keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .pay-header h1 { 
    font-size: 2.25rem; 
    font-weight: 900; 
    margin: 0;
    letter-spacing: -0.025em;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .pay-header p { 
    opacity: 0.95; 
    font-size: 1.125rem;
    margin-top: 0.75rem;
    position: relative;
    z-index: 2;
    font-weight: 500;
  }
  
  .btn-back {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    z-index: 2;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .btn-back:hover { 
    background: rgba(255, 255, 255, 0.95);
    color: var(--pay-success);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: rgba(255, 255, 255, 0.5);
  }
  
  /* Cards modernes avec glassmorphisme */
  .pay-card {
    background: var(--pay-gradient-card);
    border-radius: 24px;
    padding: 3rem;
    margin-bottom: 2rem;
    box-shadow: var(--pay-shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    backdrop-filter: blur(10px);
    overflow: hidden;
  }
  
  .pay-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(16, 185, 129, 0.03) 0%, transparent 50%, rgba(16, 185, 129, 0.03) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .pay-card:hover {
    box-shadow: var(--pay-shadow-xl);
    transform: translateY(-4px) scale(1.02);
    border-color: rgba(16, 185, 129, 0.2);
  }
  
  .pay-card:hover::before {
    opacity: 1;
  }
  
  .pay-card h3 { 
    color: var(--pay-text-dark); 
    font-weight: 800; 
    margin-bottom: 2.5rem; 
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    z-index: 1;
  }
  
  .pay-card h3 i {
    background: var(--pay-gradient-primary);
    color: white;
    padding: 0.75rem;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  }
  
  /* Formulaire moderne */
  .pay-form {
    position: relative;
    z-index: 1;
  }
  
  .form-label {
    font-weight: 700;
    color: var(--pay-text-dark);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
  }
  
  .form-label i {
    color: var(--pay-success);
    font-size: 1.25rem;
  }
  
  .form-control, .form-select {
    border: 2px solid var(--pay-border);
    border-radius: 16px;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .form-control:focus, .form-select:focus {
    border-color: var(--pay-success);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), 0 4px 20px rgba(16, 185, 129, 0.15);
    outline: none;
    background: white;
    transform: translateY(-1px);
  }
  
  /* Types de paiement - grille moderne avec glassmorphisme */
  .type-grid { 
    display: grid; 
    gap: 2rem; 
    grid-template-columns: 1fr;
    margin-bottom: 3rem;
  }
  
  .type-option {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem;
    border: 2px solid var(--pay-border);
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(15px);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .type-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
      90deg, 
      transparent, 
      rgba(16, 185, 129, 0.1), 
      transparent
    );
    transition: left 0.6s ease;
  }
  
  .type-option::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(
      circle at var(--x, 50%) var(--y, 50%),
      rgba(16, 185, 129, 0.1) 0%,
      transparent 50%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .type-option:hover::before {
    left: 100%;
  }
  
  .type-option:hover::after {
    opacity: 1;
  }
  
  .type-option:hover {
    border-color: var(--pay-success);
    background: rgba(16, 185, 129, 0.05);
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.2);
  }
  
  input[type="radio"]:checked + .type-option {
    border-color: var(--pay-success);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05));
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2), 0 8px 30px rgba(16, 185, 129, 0.25);
    transform: translateY(-4px) scale(1.02);
  }
  
  input[type="radio"]:checked + .type-option::after {
    content: '';
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: var(--pay-success);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    z-index: 10;
    animation: checkmarkPop 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  
  input[type="radio"]:checked + .type-option::after {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-1.708-1.708a.5.5 0 0 0-.708 0L5.5 7.877l-1.938-1.938a.5.5 0 0 0-.708.708l2.5 2.5a.5.5 0 0 0 .708 0l7-7a.5.5 0 0 0 0-.708z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 16px;
  }
  
  @keyframes checkmarkPop {
    0% { transform: scale(0) rotate(180deg); }
    100% { transform: scale(1) rotate(0deg); }
  }
  
  input[type="radio"] { display: none; }
  
  .type-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    font-size: 1.75rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .type-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.05));
    transition: opacity 0.3s ease;
  }
  
  .type-option:hover .type-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
  }
  
  /* Responsive grid amélioré */
  @media (min-width: 768px) {
    .type-grid { grid-template-columns: 1fr; }
  }
  
  @media (min-width: 992px) {
    .type-grid { grid-template-columns: repeat(2, 1fr); }
  }
  
  @media (min-width: 1200px) {
    .type-grid { grid-template-columns: repeat(3, 1fr); }
  }
  
  /* Montant - design premium avec animation */
  .amount-box {
    background: var(--pay-gradient-primary);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    font-size: 1.75rem;
    font-weight: 900;
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.3);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
  }
  
  .amount-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: conic-gradient(
      from 0deg,
      rgba(255, 255, 255, 0.1),
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0.1)
    );
    animation: shimmer 3s ease-in-out infinite;
  }
  
  @keyframes shimmer {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 0.8; }
  }
  
  .amount-box:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
  }
  
  /* Récapitulatif moderne avec glassmorphisme */
  .summary-box {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(16, 185, 129, 0.1);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
  }
  
  .summary-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: var(--pay-gradient-primary);
    border-radius: 20px 20px 0 0;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.3);
  }
  
  .summary-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.2);
  }
  
  .summary-box h6 { 
    margin-bottom: 2rem; 
    color: var(--pay-text-dark); 
    font-weight: 800;
    font-size: 1.3rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }
  
  .summary-box h6::before,
  .summary-box h6::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--pay-success), transparent);
  }
  
  .summary-box > div { 
    padding: 1rem 0; 
    border-bottom: 1px solid rgba(16, 185, 129, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
  }
  
  .summary-box > div:hover {
    background: rgba(16, 185, 129, 0.03);
    margin: 0 -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
    border-radius: 10px;
  }
  
  .summary-box > div:last-child {
    border-bottom: none;
    padding-top: 1.5rem;
    font-size: 1.4rem;
    font-weight: 900;
    border-top: 2px solid var(--pay-success);
    margin-top: 1rem;
    background: rgba(16, 185, 129, 0.05);
    border-radius: 12px;
  }
  
  .summary-box > div:last-child:hover {
    background: rgba(16, 185, 129, 0.1);
  }
  
  /* Historique - cartes premium */
  .history-card {
    background: var(--pay-card-bg);
    border: 2px solid var(--pay-border);
    border-radius: 16px;
    padding: 1.75rem;
    height: 100%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--pay-shadow);
    position: relative;
    overflow: hidden;
  }
  
  .history-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(5,150,105,0.05), transparent);
    transition: left 0.6s;
  }
  
  .history-card:hover::before {
    left: 100%;
  }
  
  .history-card:hover { 
    transform: translateY(-6px) scale(1.02); 
    box-shadow: 0 12px 35px rgba(5,150,105,0.15);
    border-color: var(--pay-success);
  }
  
  .history-card h6 {
    font-weight: 700;
    color: var(--pay-text-dark);
    margin-bottom: 0.5rem;
  }
  
  /* Badges modernes */
  .badge {
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }
  
  /* Boutons de filtre sophistiqués */
  .btn-group {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--pay-shadow);
  }
  
  .btn-group .btn {
    border-radius: 0;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border: none;
    background: var(--pay-card-bg);
    color: var(--pay-text-light);
    position: relative;
  }
  
  .btn-group .btn::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background: var(--pay-success);
    transition: all 0.3s ease;
    transform: translateX(-50%);
  }
  
  .btn-group .btn:hover {
    background: rgba(5,150,105,0.05);
    color: var(--pay-success);
  }
  
  .btn-group .btn.active {
    background: var(--pay-success) !important;
    color: white !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .btn-group .btn.active::before {
    width: 100%;
  }
  
  /* Boutons d'action modernes avec animation */
  .btn-success {
    background: var(--pay-gradient-primary);
    border: none;
    border-radius: 16px;
    padding: 1rem 3rem;
    font-size: 1.1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
    backdrop-filter: blur(10px);
  }
  
  .btn-success::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
    border-radius: 50%;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translate(-50%, -50%);
  }
  
  .btn-success::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.8s ease;
  }
  
  .btn-success:hover::before {
    width: 400px;
    height: 400px;
  }
  
  .btn-success:hover::after {
    left: 100%;
  }
  
  .btn-success:hover {
    background: linear-gradient(135deg, var(--pay-success-light), var(--pay-success));
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
  }
  
  .btn-success:active {
    transform: translateY(-2px) scale(1.02);
    transition: all 0.1s ease;
  }
  
  .btn-success:disabled {
    background: var(--pay-secondary);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }
  
  /* Responsive amélioré */
  @media (max-width: 767px) {
    .pay-header {
      padding: 1.5rem;
    }
    
    .pay-header .d-flex { 
      flex-direction: column; 
      gap: 1.5rem; 
      text-align: center; 
    }
    
    .pay-header h1 {
      font-size: 1.75rem;
    }
    
    .sidebar-sticky { 
      position: static !important; 
      margin-bottom: 2rem;
    }
    
    .pay-card {
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border-radius: 16px;
    }
    
    .type-option {
      padding: 1.25rem;
      gap: 1rem;
    }
    
    .type-icon {
      width: 48px;
      height: 48px;
      font-size: 1.25rem;
    }
    
    .type-grid {
      gap: 1rem;
    }
    
    .amount-box {
      font-size: 1.25rem;
      padding: 1.25rem;
    }
    
    .summary-box {
      padding: 1.5rem;
    }
    
    .history-card {
      padding: 1.25rem;
    }
  }
  
  @media (max-width: 576px) {
    .container-lg {
      padding-left: 1rem;
      padding-right: 1rem;
    }
    
    .type-option {
      flex-direction: column;
      text-align: center;
      padding: 1.5rem 1rem;
    }
    
    .btn-group {
      width: 100%;
    }
    
    .btn-group .btn {
      flex: 1;
      padding: 0.625rem 1rem;
    }
  }
  
  /* Animations d'entrée */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .pay-card {
    animation: fadeInUp 0.6s ease-out;
  }
  
  .pay-card:nth-child(2) {
    animation-delay: 0.1s;
  }
  
  .pay-card:nth-child(3) {
    animation-delay: 0.2s;
  }
  
  /* Messages d'alerte modernes */
  .alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 500;
  }
  
  .alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border-left: 4px solid var(--pay-success);
  }
  
  .alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border-left: 4px solid #ef4444;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const prices = {
    consultation: {{ (int)($priceConsult ?? 5000) }},
    analyse: {{ (int)($priceAnalyse ?? 10000) }},
    acte: {{ (int)($priceActe ?? 7000) }}
  };
  
  const labels = {
    consultation: 'Ticket de consultation',
    analyse: 'Analyse médicale',
    acte: 'Acte médical'
  };
  
  // Effet de ripple sur les options de type
  document.querySelectorAll('.type-option').forEach(option => {
    option.addEventListener('mousemove', (e) => {
      const rect = option.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      option.style.setProperty('--x', x + '%');
      option.style.setProperty('--y', y + '%');
    });
    
    option.addEventListener('click', () => {
      // Animation de sélection
      option.style.transform = 'scale(0.98)';
      setTimeout(() => {
        option.style.transform = '';
      }, 150);
    });
  });
  
  // Animation de focus pour les inputs
  document.querySelectorAll('.form-control, .form-select').forEach(input => {
    input.addEventListener('focus', () => {
      input.parentNode.classList.add('focused');
    });
    
    input.addEventListener('blur', () => {
      input.parentNode.classList.remove('focused');
    });
  });
  
  function updatePayment() {
    const type = document.querySelector('input[name="kind"]:checked')?.value || 'consultation';
    const provider = document.querySelector('select[name="provider"]')?.value || 'wave';
    const price = prices[type];
    const formatted = new Intl.NumberFormat('fr-FR').format(price);
    
    // Animation du changement de montant
    const amountDisplay = document.getElementById('amount-display');
    amountDisplay.style.transform = 'scale(1.1)';
    amountDisplay.style.color = 'var(--pay-success)';
    
    setTimeout(() => {
      amountDisplay.style.transform = 'scale(1)';
      amountDisplay.style.color = '';
    }, 200);
    
    // Mise à jour des éléments
    document.getElementById('amount').value = price;
    amountDisplay.textContent = formatted + ' XOF';
    document.getElementById('label').value = labels[type];
    document.getElementById('summary-type').textContent = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('summary-provider').textContent = provider === 'wave' ? 'Wave Money' : 'Orange Money';
    document.getElementById('summary-amount').textContent = formatted + ' XOF';
    
    // Filtrer les références
    const select = document.getElementById('ref_id');
    Array.from(select.options).forEach(opt => {
      if (!opt.value) return;
      opt.hidden = !opt.value.startsWith(type + ':');
    });
    if (select.options[select.selectedIndex]?.hidden) select.value = '';
  }
  
  // Événements
  document.querySelectorAll('input[name="kind"]').forEach(r => r.addEventListener('change', updatePayment));
  document.querySelector('select[name="provider"]')?.addEventListener('change', updatePayment);
  
  // Filtres historique
  document.querySelectorAll('[data-filter]').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      
      const filter = this.dataset.filter;
      document.querySelectorAll('[data-status]').forEach(card => {
        card.style.display = filter === 'all' || card.dataset.status === filter ? 'block' : 'none';
      });
    });
  });
  
  // Animation bouton
  document.getElementById('payButton').addEventListener('click', function() {
    this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Traitement...';
    this.disabled = true;
  });
  
  updatePayment();
});
</script>
@endsection
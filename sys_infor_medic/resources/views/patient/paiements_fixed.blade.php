@extends('layouts.app')

@section('content')
<style>
/* Remplacer complètement les styles de la sidebar pour un design compact */
.modern-sidebar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border-radius: 16px !important;
  padding: 1.5rem 1rem !important;
  color: white !important;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15) !important;
  width: 100% !important;
  max-width: 220px !important;
}

.modern-sidebar .sidebar-body {
  padding: 0 !important;
}

.modern-sidebar .profile-avatar img {
  width: 60px !important;
  height: 60px !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
}

.modern-sidebar .profile-name {
  font-size: 1rem !important;
  margin-bottom: 0.5rem !important;
}

.modern-sidebar .profile-role {
  font-size: 0.7rem !important;
  padding: 0.3rem 0.8rem !important;
  margin-bottom: 1rem !important;
}

.modern-sidebar .profile-settings-btn {
  font-size: 0.75rem !important;
  padding: 0.5rem 1rem !important;
  margin-bottom: 1rem !important;
}

.modern-sidebar .profile-info-item {
  padding: 0.5rem 0.8rem !important;
  margin-bottom: 0.5rem !important;
}

.modern-sidebar .info-label,
.modern-sidebar .info-value {
  font-size: 0.7rem !important;
}

/* Layout principal équilibré */
.balanced-payment-layout {
  min-height: 100vh;
  background: #f8fafc;
  padding: 1.5rem 0;
}

.payment-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

.payment-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
  gap: 2rem;
  align-items: start;
}

.sidebar-column {
  position: sticky;
  top: 1.5rem;
}

.main-column {
  min-width: 0; /* Important pour éviter le débordement */
}

@media (max-width: 1200px) {
  .payment-grid {
    grid-template-columns: 200px 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 992px) {
  .payment-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .sidebar-column {
    position: static;
  }
  
  .modern-sidebar {
    max-width: 100% !important;
  }
}

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
  --gray-300: #cbd5e1;
  --gray-400: #94a3b8;
  --gray-500: #64748b;
  --gray-600: #475569;
  --gray-700: #334155;
  --gray-800: #1e293b;
  --gray-900: #0f172a;
  
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

/* Header équilibré */
.balanced-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #475569 100%);
  border-radius: var(--radius-2xl);
  padding: 2.5rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-xl);
  position: relative;
  overflow: hidden;
  color: white;
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
}

.header-icon {
  width: 64px;
  height: 64px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
}

.header-content h1 {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  letter-spacing: -0.025em;
}

.header-content p {
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
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md);
  color: white;
  text-decoration: none;
  font-weight: 600;
  transition: all var(--transition);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-2px);
  color: white;
}

.stats-widget {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md);
  padding: 1rem 1.5rem;
}

.stat {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1.25rem;
  font-weight: 800;
  line-height: 1;
}

.stat-label {
  font-size: 0.75rem;
  opacity: 0.8;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.stat-divider {
  width: 1px;
  height: 28px;
  background: rgba(255, 255, 255, 0.2);
}

/* Alertes modernes */
.alert-modern {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.25rem;
  border-radius: var(--radius-lg);
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-md);
  position: relative;
}

.alert-modern.success {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  border-left: 4px solid var(--success);
}

.alert-modern.error {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  border-left: 4px solid var(--danger);
}

.alert-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  flex-shrink: 0;
}

.success .alert-icon { background: var(--success); }
.error .alert-icon { background: var(--danger); }

.alert-content h6 {
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.alert-close {
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

.alert-close:hover {
  background: rgba(0, 0, 0, 0.1);
}

/* Carte de paiement */
.payment-card {
  background: white;
  border-radius: var(--radius-2xl);
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
}

.card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--gray-200);
}

.card-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #10b981 0%, #047857 100%);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.card-title h3 {
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  color: var(--gray-900);
}

.card-title p {
  color: var(--gray-600);
  margin: 0;
}

.card-status {
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

/* Sections */
.section {
  margin-bottom: 2.5rem;
}

.section-header h4 {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-header h4 i {
  color: var(--primary);
}

.section-header p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin: 0 0 1.5rem 0;
}

/* Services */
.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

.service-card {
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
  min-height: 240px;
}

.service-card:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

input[type="radio"]:checked + .service-card {
  border-color: var(--primary);
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.03), rgba(16, 185, 129, 0.01));
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), var(--shadow-lg);
  transform: translateY(-2px);
}

input[type="radio"] {
  display: none;
}

.service-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.service-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: var(--shadow-md);
}

.service-icon.consultation { background: linear-gradient(135deg, #10b981, #047857); }
.service-icon.analyse { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.service-icon.acte { background: linear-gradient(135deg, #f59e0b, #d97706); }

.service-badge {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.service-badge.popular {
  background: linear-gradient(135deg, #fef3c7, #fed7aa);
  color: #92400e;
}

.service-badge.premium {
  background: linear-gradient(135deg, #ddd6fe, #c7d2fe);
  color: #5b21b6;
}

.service-content h5 {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.service-content p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.service-features {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.service-features span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.75rem;
}

.service-features i {
  color: var(--success);
  font-size: 0.875rem;
}

.service-price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--gray-200);
}

.price-amount {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--primary);
  line-height: 1;
}

.price-currency {
  font-size: 0.875rem;
  color: var(--gray-500);
  margin-left: 0.5rem;
}

/* Détails */
.details-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .details-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.detail-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.modern-label {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.modern-label span {
  font-weight: 600;
  color: var(--gray-900);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.modern-label i {
  color: var(--primary);
}

.modern-label small {
  color: var(--gray-500);
  font-size: 0.75rem;
}

.select-wrapper {
  position: relative;
}

.modern-select, .modern-input {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-md);
  background: white;
  font-size: 0.9rem;
  transition: all var(--transition);
}

.modern-select:focus, .modern-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.select-wrapper i {
  position: absolute;
  right: 0.875rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  pointer-events: none;
}

/* Méthodes de paiement */
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
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition);
  background: white;
}

.payment-method:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow-md);
}

input[type="radio"]:checked + .payment-method {
  border-color: var(--primary);
  background: rgba(16, 185, 129, 0.03);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.method-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  flex-shrink: 0;
}

.method-icon.wave { background: linear-gradient(135deg, #10b981, #047857); }
.method-icon.orange { background: linear-gradient(135deg, #f97316, #ea580c); }

.method-info h6 {
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.method-info p {
  color: var(--gray-600);
  font-size: 0.875rem;
  margin: 0;
}

.method-badge {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  margin-left: auto;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.method-badge.recommended {
  background: linear-gradient(135deg, #fef3c7, #fed7aa);
  color: #92400e;
}

/* Récapitulatif */
.summary-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 2px solid rgba(16, 185, 129, 0.1);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-md);
  position: relative;
}

.summary-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #047857);
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

.status-indicator {
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

.summary-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--gray-200);
}

.summary-line:last-of-type {
  border-bottom: none;
}

.summary-line .label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-weight: 500;
}

.summary-line i {
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

.summary-total .label {
  font-weight: 700;
  font-size: 1rem;
  color: var(--gray-900);
}

.total-amount {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.total-amount .amount {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--primary);
}

.total-amount .currency {
  color: var(--gray-500);
}

/* Bouton de paiement */
.payment-actions {
  text-align: center;
}

.btn-pay {
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 350px;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #10b981, #047857);
  border: none;
  border-radius: var(--radius-lg);
  color: white;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all var(--transition);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
  position: relative;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.btn-pay:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-amount {
  font-weight: 800;
}

.security-info {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.security-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.8rem;
}

.security-item i {
  color: var(--primary);
}

/* Historique */
.history-card {
  background: white;
  border-radius: var(--radius-2xl);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  gap: 2rem;
}

.history-header .header-left {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  color: var(--gray-900);
}

.history-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #10b981, #047857);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.history-header h3 {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.history-header p {
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

.filter-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  background: transparent;
  border: none;
  border-radius: var(--radius-sm);
  color: var(--gray-600);
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition);
  font-size: 0.8rem;
}

.filter-btn.active {
  background: var(--primary);
  color: white;
  box-shadow: var(--shadow-sm);
}

.filter-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.125rem 0.5rem;
  border-radius: var(--radius-sm);
  font-size: 0.7rem;
  margin-left: 0.25rem;
}

.filter-btn.active .filter-count {
  background: rgba(255, 255, 255, 0.3);
}

/* Transactions */
.transactions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.transaction-item {
  background: white;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-lg);
  padding: 1.25rem;
  transition: all var(--transition);
  position: relative;
}

.transaction-item:hover {
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

.transaction-status {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
  flex-shrink: 0;
}

.transaction-status.paid { background: var(--success); }
.transaction-status.pending { background: var(--warning); }
.transaction-status.failed { background: var(--danger); }

.transaction-info h6 {
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.transaction-info small {
  color: var(--gray-500);
}

.transaction-badge {
  margin-left: auto;
}

.badge-paid {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-pending {
  background: rgba(245, 158, 11, 0.1);
  color: var(--warning);
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.transaction-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.transaction-details {
  display: flex;
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-600);
  font-size: 0.8rem;
}

.detail-item i {
  color: var(--primary);
}

.transaction-amount {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.transaction-amount .amount {
  font-size: 1rem;
  font-weight: 700;
  color: var(--primary);
}

.transaction-amount .currency {
  color: var(--gray-500);
  font-size: 0.8rem;
}

.transaction-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all var(--transition);
}

.action-btn.continue {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
}

.action-btn.receipt {
  background: rgba(59, 130, 246, 0.1);
  color: var(--secondary);
}

.action-btn:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* État vide */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--gray-500);
}

.empty-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h4 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
  color: var(--gray-700);
}

.empty-state p {
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .balanced-header {
    padding: 2rem 1.5rem;
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
  
  .payment-card, .history-card {
    padding: 1.5rem;
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
  
  .transaction-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .transaction-actions {
    justify-content: center;
  }
  
  .security-info {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>

<div class="balanced-payment-layout">
  <div class="payment-container">
    <div class="payment-grid">
      <!-- Sidebar compacte -->
      <div class="sidebar-column">
        @include('layouts.partials.profile_sidebar')
      </div>
      
      <!-- Contenu principal -->
      <div class="main-column">
        
        <!-- Header équilibré -->
        <div class="balanced-header">
          <div class="header-container">
            <div class="header-left">
              <div class="header-icon">
                <i class="bi bi-credit-card-2-front"></i>
              </div>
              <div class="header-content">
                <h1>Centre de Paiement</h1>
                <p>Gérez vos transactions médicales en toute sécurité</p>
                <nav class="breadcrumb-nav">
                  <span>Accueil</span>
                  <i class="bi bi-chevron-right"></i>
                  <span>Paiements</span>
                </nav>
              </div>
            </div>
            
            <div class="header-right">
              <a href="{{ route('patient.dashboard') }}" class="btn-back">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
              </a>
              <div class="stats-widget">
                <div class="stat">
                  <span class="stat-value">{{ $orders->where('status', 'paid')->count() }}</span>
                  <span class="stat-label">Payé</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                  <span class="stat-value">{{ $orders->where('status', 'pending')->count() }}</span>
                  <span class="stat-label">En attente</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
          <div class="alert-modern success">
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
          <div class="alert-modern error">
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

        <!-- Formulaire de paiement -->
        <div class="payment-card">
          <div class="card-header">
            <div class="card-icon">
              <i class="bi bi-plus-circle-fill"></i>
            </div>
            <div class="card-title">
              <h3>Nouveau Paiement</h3>
              <p>Sélectionnez votre service et procédez au paiement sécurisé</p>
            </div>
            <div class="card-status">
              <i class="bi bi-shield-check"></i>
              <span>Sécurisé</span>
            </div>
          </div>

          <form method="POST" action="{{ route('patient.payments.checkout') }}" class="payment-form">
            @csrf
            
            <!-- Sélection des services -->
            <div class="section">
              <div class="section-header">
                <h4><i class="bi bi-card-checklist"></i>Choisissez votre service</h4>
                <p>Sélectionnez le type de service médical à payer</p>
              </div>
              
              <div class="services-grid">
                <input type="radio" name="kind" value="consultation" id="consultation" checked>
                <label for="consultation" class="service-card consultation">
                  <div class="service-header">
                    <div class="service-icon consultation">
                      <i class="bi bi-person-heart"></i>
                    </div>
                    <div class="service-badge popular">Populaire</div>
                  </div>
                  <div class="service-content">
                    <h5>Consultation</h5>
                    <p>Consultation médicale complète</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Diagnostic complet</span>
                      <span><i class="bi bi-check2"></i>Prescription médicale</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                    <span class="price-currency">XOF</span>
                  </div>
                </label>
                
                <input type="radio" name="kind" value="analyse" id="analyse">
                <label for="analyse" class="service-card analyse">
                  <div class="service-header">
                    <div class="service-icon analyse">
                      <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="service-badge premium">Premium</div>
                  </div>
                  <div class="service-content">
                    <h5>Analyse médicale</h5>
                    <p>Examens de laboratoire complets</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Analyses sanguines</span>
                      <span><i class="bi bi-check2"></i>Résultats détaillés</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price-amount">{{ number_format($priceAnalyse ?? 10000, 0, ',', ' ') }}</span>
                    <span class="price-currency">XOF</span>
                  </div>
                </label>
                
                <input type="radio" name="kind" value="acte" id="acte">
                <label for="acte" class="service-card acte">
                  <div class="service-header">
                    <div class="service-icon acte">
                      <i class="bi bi-bandaid"></i>
                    </div>
                    <div class="service-badge special">Spécialisé</div>
                  </div>
                  <div class="service-content">
                    <h5>Acte médical</h5>
                    <p>Interventions spécialisées</p>
                    <div class="service-features">
                      <span><i class="bi bi-check2"></i>Procédures techniques</span>
                      <span><i class="bi bi-check2"></i>Suivi médical</span>
                    </div>
                  </div>
                  <div class="service-price">
                    <span class="price-amount">{{ number_format($priceActe ?? 7000, 0, ',', ' ') }}</span>
                    <span class="price-currency">XOF</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Détails du paiement -->
            <div class="section">
              <div class="section-header">
                <h4><i class="bi bi-gear"></i>Détails du paiement</h4>
                <p>Configurez les options avancées de votre paiement</p>
              </div>
              
              <div class="details-grid">
                <div class="detail-group">
                  <label class="modern-label">
                    <span><i class="bi bi-link-45deg"></i>Référence associée</span>
                    <small>Lier à un rendez-vous existant (optionnel)</small>
                  </label>
                  <div class="select-wrapper">
                    <select name="ref_id" id="ref_id" class="modern-select">
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
                    <i class="bi bi-chevron-down"></i>
                  </div>
                </div>
                
                <div class="detail-group">
                  <label class="modern-label">
                    <span><i class="bi bi-tag"></i>Libellé du paiement</span>
                    <small>Description qui apparaîtra sur votre reçu</small>
                  </label>
                  <input type="text" name="label" id="label" class="modern-input" value="Ticket de consultation" placeholder="Description du paiement">
                </div>
              </div>
            </div>

            <!-- Méthode de paiement -->
            <div class="section">
              <div class="section-header">
                <h4><i class="bi bi-credit-card-2-back"></i>Méthode de paiement</h4>
                <p>Choisissez votre mode de paiement préféré</p>
              </div>
              
              <div class="payment-methods">
                <input type="radio" name="provider" value="wave" id="wave" checked>
                <label for="wave" class="payment-method">
                  <div class="method-icon wave">
                    <i class="bi bi-wallet2"></i>
                  </div>
                  <div class="method-info">
                    <h6>Wave Money</h6>
                    <p>Paiement mobile rapide et sécurisé</p>
                  </div>
                  <span class="method-badge recommended">Recommandé</span>
                </label>
                
                <input type="radio" name="provider" value="orangemoney" id="orange">
                <label for="orange" class="payment-method">
                  <div class="method-icon orange">
                    <i class="bi bi-phone"></i>
                  </div>
                  <div class="method-info">
                    <h6>Orange Money</h6>
                    <p>Portefeuille électronique sécurisé</p>
                  </div>
                  <span class="method-badge fast">Rapide</span>
                </label>
              </div>
            </div>

            <!-- Récapitulatif -->
            <div class="summary-card">
              <div class="summary-header">
                <h5><i class="bi bi-receipt"></i>Récapitulatif</h5>
                <span class="status-indicator">Prêt à payer</span>
              </div>
              
              <div class="summary-body">
                <input type="hidden" name="amount" id="amount" value="{{ $priceConsult }}">
                
                <div class="summary-line">
                  <span class="label"><i class="bi bi-tag-fill"></i>Service</span>
                  <span class="value" id="summary-type">Consultation</span>
                </div>
                
                <div class="summary-line">
                  <span class="label"><i class="bi bi-wallet2"></i>Méthode</span>
                  <span class="value" id="summary-provider">Wave Money</span>
                </div>
                
                <div class="summary-divider"></div>
                
                <div class="summary-total">
                  <span class="label">Total à payer</span>
                  <div class="total-amount">
                    <span class="amount" id="summary-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bouton de paiement -->
            <div class="payment-actions">
              <button type="submit" class="btn-pay" id="payButton">
                <div class="btn-content">
                  <i class="bi bi-shield-check"></i>
                  <span>Procéder au paiement sécurisé</span>
                </div>
                <div class="btn-amount" id="button-amount">{{ number_format($priceConsult ?? 5000, 0, ',', ' ') }} XOF</div>
              </button>
              
              <div class="security-info">
                <div class="security-item">
                  <i class="bi bi-lock-fill"></i>
                  <span>Cryptage SSL 256-bit</span>
                </div>
                <div class="security-item">
                  <i class="bi bi-shield-check"></i>
                  <span>Paiement 100% sécurisé</span>
                </div>
                <div class="security-item">
                  <i class="bi bi-lightning-charge"></i>
                  <span>Traitement instantané</span>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Historique des transactions -->
        <div class="history-card">
          <div class="history-header">
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
              <button class="filter-btn active" data-filter="all">
                <i class="bi bi-list"></i>
                <span>Tous</span>
                <span class="filter-count">{{ $orders->count() }}</span>
              </button>
              <button class="filter-btn" data-filter="paid">
                <i class="bi bi-check-circle"></i>
                <span>Payés</span>
                <span class="filter-count">{{ $orders->where('status', 'paid')->count() }}</span>
              </button>
              <button class="filter-btn" data-filter="pending">
                <i class="bi bi-clock"></i>
                <span>En attente</span>
                <span class="filter-count">{{ $orders->where('status', 'pending')->count() }}</span>
              </button>
            </div>
          </div>
          
          <div class="transactions-list">
            @forelse($orders as $order)
              @php($item = $order->items->first())
              <div class="transaction-item" data-status="{{ $order->status }}">
                <div class="transaction-header">
                  <div class="transaction-status {{ $order->status }}">
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
                  <div class="transaction-badge">
                    <span class="badge-{{ $order->status }}">
                      {{ $order->status === 'paid' ? 'Payé' : ($order->status === 'pending' ? 'En attente' : ucfirst($order->status)) }}
                    </span>
                  </div>
                </div>
                
                <div class="transaction-content">
                  <div class="transaction-details">
                    <div class="detail-item">
                      <i class="bi bi-calendar3"></i>
                      <span>{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="detail-item">
                      <i class="bi bi-wallet2"></i>
                      <span>{{ ucfirst($order->provider ?? 'Non spécifié') }}</span>
                    </div>
                  </div>
                  
                  <div class="transaction-amount">
                    <span class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }}</span>
                    <span class="currency">XOF</span>
                  </div>
                </div>
                
                <div class="transaction-actions">
                  @if($order->status === 'pending' && $order->payment_url)
                    <a href="{{ $order->payment_url }}" class="action-btn continue">
                      <i class="bi bi-play-circle"></i>
                      <span>Continuer</span>
                    </a>
                  @elseif($order->status === 'paid')
                    <a href="{{ route('payments.receipt', $order->id) }}" class="action-btn receipt">
                      <i class="bi bi-download"></i>
                      <span>Télécharger reçu</span>
                    </a>
                  @endif
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
</div>

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
        <div class="btn-content">
          <i class="bi bi-hourglass-split"></i>
          <span>Traitement en cours...</span>
        </div>
      `;
      this.disabled = true;
    });
  }

  // Initialiser l'affichage
  updatePayment();
});
</script>
@endsection
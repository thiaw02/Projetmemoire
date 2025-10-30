@extends('layouts.app')

@section('body_class', 'admin-page')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="admin-intelligent-sidebar sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">
    <div class="admin-main-content">

{{-- Header moderne simple --}}
<div class="admin-modern-header scroll-fade-in">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('admin.simple-evaluations.admin-dashboard') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
        <i class="bi bi-star-fill"></i>
        <span>Gestion Évaluations</span>
      </a>
      <div class="header-badge">
        <i class="bi bi-shield-check"></i>
        <span>{{ Auth::user()->name }}</span>
      </div>
    </div>
  </div>
</div>


@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- KPIs modernes sur une seule ligne --}}
<div class="row g-3 mb-4 scroll-slide-left">
  {{-- Utilisateurs --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon users">
        <i class="bi bi-people-fill"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $kpis['totalUsers'] ?? (\App\Models\User::count()) }}</div>
        <div class="kpi-label">Utilisateurs</div>
      </div>
    </div>
  </div>
  
  {{-- Patients --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon patients">
        <i class="bi bi-person-hearts"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $kpis['totalPatients'] ?? ($rolesCount['patient'] ?? ($users?->where('role','patient')->count() ?? 0)) }}</div>
        <div class="kpi-label">Patients</div>
      </div>
    </div>
  </div>
  
  {{-- Rendez-vous --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon rdv">
        <i class="bi bi-calendar-check"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $kpis['rdv_this_month'] ?? 0 }}</div>
        <div class="kpi-label">RDV ce mois</div>
      </div>
    </div>
  </div>
  
  {{-- Consultations --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon consultations">
        <i class="bi bi-clipboard2-pulse"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $kpis['consults_this_month'] ?? 0 }}</div>
        <div class="kpi-label">Consultations</div>
      </div>
    </div>
  </div>
  
  {{-- Paiements --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon payments">
        <i class="bi bi-wallet2"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ number_format(($kpis['payments_paid_this_month'] ?? 0) / 1000, 0) }}K</div>
        <div class="kpi-label">Paiements (XOF)</div>
        <div class="kpi-sub">{{ $kpis['payments_pending'] ?? 0 }} en attente</div>
      </div>
    </div>
  </div>
  
  {{-- Médecins --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon professionals">
        <i class="bi bi-heart-pulse"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $rolesCount['medecin'] ?? (\App\Models\User::where('role','medecin')->count()) }}</div>
        <div class="kpi-label">Médecins</div>
      </div>
    </div>
  </div>

  {{-- Infirmiers --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon users">
        <i class="bi bi-bandaid"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $rolesCount['infirmier'] ?? (\App\Models\User::where('role','infirmier')->count()) }}</div>
        <div class="kpi-label">Infirmiers</div>
      </div>
    </div>
  </div>

  {{-- Secrétaires --}}
  <div class="col">
    <div class="kpi-card scroll-card-hover gpu-accelerated">
      <div class="kpi-icon patients">
        <i class="bi bi-person-workspace"></i>
      </div>
      <div class="kpi-content">
        <div class="kpi-value">{{ $rolesCount['secretaire'] ?? (\App\Models\User::where('role','secretaire')->count()) }}</div>
        <div class="kpi-label">Secrétaires</div>
      </div>
    </div>
  </div>
</div>

{{-- Styles modernes minimalistes pour admin --}}
<style>
  /* Header admin moderne simple */
  /* Suppression complète de l'espacement excessif pour dashboard admin */
  body {
    padding-top: 90px !important;
  }
  
  .container.mt-4,
  .container {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }
  
  .row {
    margin-top: 0 !important;
  }
  
  .admin-modern-header {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    margin-top: 0;
    box-shadow: 0 8px 25px rgba(30, 64, 175, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
    font-size: 1.2rem;
  }
  
  .header-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.9rem;
  }
  
  /* Optimisation KPIs - plus compacts */
  .kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(39, 174, 96, 0.08);
    border: 1px solid rgba(39, 174, 96, 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
    height: 100px;
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
  }
  
  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.15);
  }
  
  .kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .kpi-icon.users { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .kpi-icon.patients { background: linear-gradient(135deg, #ef4444, #dc2626); }
  .kpi-icon.rdv { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .kpi-icon.consultations { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  .kpi-icon.payments { background: linear-gradient(135deg, #10b981, #059669); }
  .kpi-icon.evaluations { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
  .kpi-icon.professionals { background: linear-gradient(135deg, #6366f1, #4f46e5); }
  
  .kpi-content {
    flex: 1;
  }
  
  .kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }
  
  .kpi-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
  }
  
  .kpi-sub {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
  }
  
  /* Responsive pour KPIs */
  @media (max-width: 1200px) {
    .kpi-value { font-size: 2rem; }
    .kpi-icon { width: 50px; height: 50px; font-size: 20px; }
    .kpi-card { padding: 20px; height: 90px; }
  }
  
  @media (max-width: 768px) {
    .kpi-value { font-size: 1.75rem; }
    .kpi-label { font-size: 0.75rem; }
    .kpi-icon { width: 45px; height: 45px; font-size: 18px; }
    .kpi-card { padding: 16px; height: 80px; gap: 12px; }
  }
  
  /* Conteneur dashboard */
  body > .container { max-width: 1500px !important; }
  .page-section { padding-left: .75rem; padding-right: .75rem; }
  .content-card { background: #fff; border-radius: .75rem; box-shadow: 0 8px 24px rgba(0,0,0,.06); padding: 1rem; }
  /* Onglets admin ultra-modernisés */
  .admin-tabs { 
    display: flex; 
    flex-wrap: nowrap;
    gap: 4px; 
    background: linear-gradient(145deg, #f8fafc, #e2e8f0);
    padding: 8px; 
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.08);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .admin-tabs::-webkit-scrollbar {
    height: 4px;
  }
  
  .admin-tabs::-webkit-scrollbar-track {
    background: transparent;
  }
  
  .admin-tabs::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 2px;
  }
  
  .admin-tabs .nav-item {
    flex-shrink: 0;
  }
  
  .admin-tabs .nav-link { 
    position: relative;
    min-width: 160px;
    padding: 12px 20px; 
    border: none; 
    border-radius: 12px;
    white-space: nowrap; 
    color: #64748b; 
    font-weight: 600;
    font-size: 0.9rem;
    background: transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    overflow: hidden;
  }
  
  .admin-tabs .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.4));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
  }
  
  .admin-tabs .nav-link:hover::before {
    opacity: 1;
  }
  
  .admin-tabs .nav-link:hover {
    transform: translateY(-2px);
    color: #334155;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
  }
  
  .admin-tabs .nav-link.active { 
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    color: white;
    box-shadow: 0 8px 25px rgba(30, 64, 175, 0.3);
    transform: translateY(-1px);
  }
  
  .admin-tabs .nav-link.active::before {
    opacity: 0;
  }
  
  .admin-tabs .nav-link i {
    font-size: 1rem;
    opacity: 0.8;
    transition: all 0.3s ease;
  }
  
  .admin-tabs .nav-link:hover i,
  .admin-tabs .nav-link.active i {
    opacity: 1;
    transform: scale(1.1);
  }
  
  /* Onglet spécifiques - couleurs thématiques */
  .admin-tabs .nav-link:nth-child(1).active {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
  }
  
  .admin-tabs .nav-link:nth-child(2).active {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
  }
  
  .admin-tabs .nav-link:nth-child(3).active {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
  }
  
  .admin-tabs .nav-link:nth-child(4).active {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
  }
  
  .admin-tabs .nav-link:nth-child(5).active {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
  }
  
  .admin-tabs .nav-link:nth-child(6).active {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    box-shadow: 0 8px 25px rgba(6, 182, 212, 0.3);
  }
  
  .tab-scroll { 
    overflow: visible; 
  }
  
  /* Tableaux admin modernes */
  .table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  }
  
  .admin-table {
    margin: 0;
  }
  
  .admin-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 0.75rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .admin-table td {
    padding: 0.75rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .admin-table tbody tr:hover {
    background: #f8fafc;
  }
  
  /* Boutons admin optimisés */
  .btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 6px;
  }
  
  /* Sidebar collante et compacte */
  .sidebar-standardized { position: sticky; top: 1rem; }
  .sidebar-standardized img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  
  /* ========== STYLES POUR ONGLET SUPERVISION DES RÔLES ========== */
  .roles-supervision-header {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.08);
  }
  
  .text-purple { color: #8b5cf6 !important; }
  
  .roles-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .role-stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .role-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  }
  
  .role-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--bs-primary), var(--bs-info));
  }
  
  .role-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .role-stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
  }
  
  .role-stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .role-stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
  }
  
  .roles-management-table {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.06);
  }
  
  .role-badge {
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
  }
  
  .status-badge {
    font-weight: 500;
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
  }
  
  /* ========== STYLES POUR ONGLET STATISTIQUES ========== */
  .stats-header {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.08);
  }
  
  .stats-period-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .stats-charts-grid {
    padding: 0;
  }
  
  .stats-chart-card {
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
  }
  
  .stats-chart-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  }
  
  .stats-chart-header {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .stats-chart-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
  }
  
  .stats-chart-body {
    padding: 1.5rem;
  }
  
  /* ========== STYLES POUR ONGLET PERMISSIONS ========== */
  .permissions-header {
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.08);
  }
  
  .permissions-alerts {
    margin-bottom: 2rem;
  }
  
  .permissions-matrix {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.06);
  }
  
  .permissions-card {
    border: none;
  }
  
  .permissions-card-header {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .permission-module-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
  }
  
  .permissions-table-container {
    overflow-x: auto;
  }
  
  .permissions-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
  }
  
  .permissions-table th {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    color: #475569;
    font-weight: 600;
    padding: 1rem;
    border: none;
    font-size: 0.9rem;
    position: sticky;
    top: 0;
    z-index: 1;
  }
  
  .permissions-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .permission-row:hover {
    background: #f8fafc;
  }
  
  .role-cell {
    min-width: 200px;
  }
  
  .role-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .role-name {
    font-weight: 600;
    color: #1f2937;
  }
  
  .permission-level-selector {
    display: flex;
    gap: 4px;
    justify-content: center;
  }
  
  .permission-btn {
    min-width: 80px;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 6px;
  }
  
  .permissions-actions-footer {
    background: #f8fafc;
    padding: 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.06);
    margin-top: 2rem;
    border-radius: 0 0 16px 16px;
  }
  
  /* Responsive pour les nouveaux composants */
  @media (max-width: 768px) {
    .roles-stats-grid {
      grid-template-columns: 1fr;
    }
    
    .role-stat-number {
      font-size: 2rem;
    }
    
    .stats-period-selector {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
    
    .permission-level-selector {
      flex-direction: column;
      gap: 2px;
    }
    
    .permission-btn {
      min-width: 60px;
      font-size: 0.7rem;
    }
    
    .permissions-card-header,
    .stats-header .d-flex,
    .roles-supervision-header .d-flex {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
  }
  
  /* ========== STYLES POUR ONGLET PAIEMENTS ========== */
  .payments-header {
    background: linear-gradient(135deg, #e0f2fe, #b3e5fc);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid rgba(0,0,0,0.08);
  }
  
  .payments-kpis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .payment-kpi-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .payment-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  }
  
  .payment-kpi-card.revenue::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #10b981, #059669);
  }
  
  .payment-kpi-card.pending::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #d97706);
  }
  
  .payment-kpi-card.success-rate::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  }
  
  .payment-kpi-card.providers::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #8b5cf6, #7c3aed);
  }
  
  .payment-kpi-icon {
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
  
  .payment-kpi-card.revenue .payment-kpi-icon {
    background: linear-gradient(135deg, #10b981, #059669);
  }
  
  .payment-kpi-card.pending .payment-kpi-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
  }
  
  .payment-kpi-card.success-rate .payment-kpi-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  }
  
  .payment-kpi-card.providers .payment-kpi-icon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  }
  
  .payment-kpi-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
  }
  
  .payment-kpi-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .payment-kpi-sub {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
  }
  
  .payment-kpi-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  .payment-kpi-trend.up {
    color: #10b981;
  }
  
  .payment-kpi-trend.warning {
    color: #f59e0b;
  }
  
  .payment-kpi-trend.neutral {
    color: #6b7280;
  }
  
  .payments-table-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.06);
  }
  
  .payment-date,
  .payment-service,
  .payment-amount {
    line-height: 1.2;
  }
  
  .provider-badge,
  .action-badge,
  .severity-badge {
    font-weight: 600;
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
  }
  
  .provider-badge.stripe {
    background: #635bff;
    color: white;
  }
  
  .provider-badge.paypal {
    background: #0070ba;
    color: white;
  }
  
  .provider-badge.orange {
    background: #ff6900;
    color: white;
  }
  
  .provider-badge.local {
    background: #10b981;
    color: white;
  }
  
  .provider-badge.wave {
    background: #f59e0b;
    color: white;
  }
  
  /* ========== STYLES POUR PAGE AUDIT ========== */
  .audit-layout {
    display: flex;
    height: 100vh;
    background: #f9fafb;
  }
  
  .audit-sidebar {
    width: 300px;
    background: white;
    border-right: 1px solid rgba(0,0,0,0.08);
    box-shadow: 2px 0 10px rgba(0,0,0,0.05);
    overflow-y: auto;
    flex-shrink: 0;
  }
  
  .audit-content {
    flex: 1;
    overflow-y: auto;
    padding: 2rem;
  }
  
  .audit-header {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    padding: 2rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    position: sticky;
    top: 0;
    z-index: 10;
  }
  
  .audit-header h1 {
    color: #1f2937;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
  }
  
  .audit-subtitle {
    color: #6b7280;
    font-weight: 500;
  }
  
  .audit-kpis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .audit-kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .audit-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.12);
  }
  
  .audit-kpi-card.info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  }
  
  .audit-kpi-card.warning::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #f59e0b, #d97706);
  }
  
  .audit-kpi-card.danger::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ef4444, #dc2626);
  }
  
  .audit-kpi-card.success::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #10b981, #059669);
  }
  
  .audit-kpi-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  
  .audit-kpi-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .audit-filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.06);
    margin-bottom: 2rem;
  }
  
  .audit-table-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.06);
  }
  
  .action-badge.create {
    background: #dcfce7;
    color: #166534;
  }
  
  .action-badge.update {
    background: #fef3c7;
    color: #92400e;
  }
  
  .action-badge.delete {
    background: #fee2e2;
    color: #991b1b;
  }
  
  .action-badge.login {
    background: #e0f2fe;
    color: #0c4a6e;
  }
  
  .severity-badge.low {
    background: #f0f9ff;
    color: #0369a1;
  }
  
  .severity-badge.medium {
    background: #fffbeb;
    color: #d97706;
  }
  
  .severity-badge.high {
    background: #fef2f2;
    color: #dc2626;
  }
  
  .severity-badge.critical {
    background: #450a0a;
    color: #fecaca;
  }
  
  .audit-modal .modal-content {
    border-radius: 16px;
    box-shadow: 0 20px 48px rgba(0,0,0,0.16);
    border: none;
  }
  
  .audit-modal .modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 16px 16px 0 0;
  }
  
  .audit-modal .modal-body {
    padding: 1.5rem;
  }
  
  .diff-added {
    background: #dcfce7;
    color: #166534;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: monospace;
  }
  
  .diff-removed {
    background: #fee2e2;
    color: #991b1b;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: monospace;
    text-decoration: line-through;
  }
  
  .diff-unchanged {
    color: #6b7280;
    padding: 0.25rem 0.5rem;
    font-family: monospace;
  }
  
  .sidebar-nav {
    padding: 1.5rem;
  }
  
  .sidebar-nav .nav-link {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .sidebar-nav .nav-link:hover {
    background: #f3f4f6;
    color: #374151;
  }
  
  .sidebar-nav .nav-link.active {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .sidebar-nav .nav-link i {
    font-size: 1.1rem;
  }
  
  /* ========== RESPONSIVE DESIGN ========== */
  @media (max-width: 768px) {
    .audit-layout {
      flex-direction: column;
      height: auto;
    }
    
    .audit-sidebar {
      width: 100%;
      order: 2;
    }
    
    .audit-content {
      order: 1;
      padding: 1rem;
    }
    
    .payments-kpis-grid,
    .audit-kpis-grid {
      grid-template-columns: 1fr;
    }
    
    .payment-kpi-card,
    .audit-kpi-card {
      padding: 1rem;
    }
    
    .payments-table-section,
    .audit-table-section,
    .audit-filters-section {
      padding: 1rem;
    }
  }
</style>
{{-- Nav tabs --}}
<div class="tab-scroll">
<ul class="nav nav-tabs admin-tabs flex-nowrap" id="adminTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true"><i class="bi bi-people me-1"></i> Gérer utilisateurs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="patients-tab" data-bs-toggle="tab" data-bs-target="#patients" type="button" role="tab" aria-controls="patients" aria-selected="false"><i class="bi bi-person-vcard me-1"></i> Gérer patients</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false"><i class="bi bi-graph-up me-1"></i> Statistiques globales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="false"><i class="bi bi-person-gear me-1"></i> Superviser rôles</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="false"><i class="bi bi-shield-lock me-1"></i> Gestion permissions</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false"><i class="bi bi-wallet2 me-1"></i> Paiements</button>
    </li>
</ul>
</div>

<div class="tab-content mt-3" id="adminTabContent">

    {{-- Gérer utilisateurs --}}
    <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Liste des utilisateurs</h4>
          <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchUsers" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Liste avancée</a>
            {{-- Lien d'affectations retiré (fonctionnalité obsolète) --}}
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm" title="Ajouter un utilisateur" aria-label="Ajouter un utilisateur">➕ Ajouter</a>
          </div>
        </div>

        
        <div class="table-container">
          <table class="table admin-table" id="usersTable">
              <thead>
                  <tr>
                      <th><i class="bi bi-person me-1"></i>Nom</th>
                      <th><i class="bi bi-envelope me-1"></i>Email</th>
                      <th><i class="bi bi-person-badge me-1"></i>Rôle</th>
                      <th><i class="bi bi-briefcase me-1"></i>Spécialité</th>
                      <th><i class="bi bi-link me-1"></i>Liens</th>
                      <th><i class="bi bi-toggle-on me-1"></i>Actif</th>
                      <th><i class="bi bi-gear me-1"></i>Actions</th>
                  </tr>
              </thead>
            <tbody>
                @foreach($users as $user)
                    @if($user->role !== 'patient')
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->specialite ?? '-' }}</td>
                        <td>
                          @if($user->role==='medecin')
                            @php
                              $names = ($user->nurses ?? collect())->pluck('name')->all();
                            @endphp
                            <span class="badge bg-light text-dark border" title="{{ implode(', ', $names) }}">{{ count($names) }} infirmier(s)</span>
                          @elseif($user->role==='infirmier')
                            @php
                              $names = ($user->doctors ?? collect())->pluck('name')->all();
                            @endphp
                            <span class="badge bg-light text-dark border" title="{{ implode(', ', $names) }}">{{ count($names) }} médecin(s)</span>
                          @else
                            —
                          @endif
                        </td>
                        <td>
                          <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}" class="mb-0">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary">{{ $user->active ? 'Actif' : 'Inactif' }}</button>
                          </form>
                        </td>
                        <td>
                          <div class="d-flex gap-2" role="group" aria-label="Actions utilisateur">
<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                          </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
          </table>
        </div>
        
        {{-- Pagination dédiée aux utilisateurs --}}
        @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $users->appends(request()->except('patients_page'))->links() }}
            </div>
        @endif
    </div>
    
    {{-- Gérer patients --}}
    <div class="tab-pane fade" id="patients" role="tabpanel" aria-labelledby="patients-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Liste des patients</h4>
          <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchPatients" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm" title="Listes avancées">Listes avancées</a>
            <a href="{{ route('admin.patients.create') }}" class="btn btn-success btn-sm" title="Ajouter un patient" aria-label="Ajouter un patient">➕ Ajouter</a>
          </div>
        </div>

        <div class="table-container">
          <table class="table admin-table" id="patientsTable">
              <thead>
                  <tr>
                      <th><i class="bi bi-person me-1"></i>Nom</th>
                      <th><i class="bi bi-envelope me-1"></i>Email</th>
                      <th><i class="bi bi-calendar-plus me-1"></i>Date création</th>
                      <th><i class="bi bi-toggle-on me-1"></i>Actif</th>
                      <th><i class="bi bi-gear me-1"></i>Actions</th>
                  </tr>
              </thead>
            <tbody>
                @foreach($patients as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                          <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}" class="mb-0">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary">{{ $user->active ? 'Actif' : 'Inactif' }}</button>
                          </form>
                        </td>
                        <td>
                          <div class="d-flex gap-2" role="group" aria-label="Actions patient">
                            <a href="{{ route('admin.patients.edit', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.patients.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce patient ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                          </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
        </div>
        
        {{-- Pagination dédiée aux patients --}}
        @if(method_exists($patients, 'hasPages') && $patients->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $patients->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Statistiques globales --}}
    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
        <div class="stats-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1"><i class="bi bi-graph-up text-warning me-2"></i>Statistiques & Analytics</h4>
              <p class="text-muted mb-0">Tableau de bord des performances et tendances</p>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div class="stats-period-selector">
                <label class="form-label mb-0 me-2"><i class="bi bi-calendar3 me-1"></i>Période :</label>
                <div class="btn-group" role="group" aria-label="Fenêtre temporelle">
                  <input type="radio" class="btn-check" name="statsPeriod" id="period2" data-window="2">
                  <label class="btn btn-outline-secondary btn-sm" for="period2">2 mois</label>
                  
                  <input type="radio" class="btn-check" name="statsPeriod" id="period6" data-window="6">
                  <label class="btn btn-outline-secondary btn-sm" for="period6">6 mois</label>
                  
                  <input type="radio" class="btn-check" name="statsPeriod" id="period12" data-window="12" checked>
                  <label class="btn btn-outline-secondary btn-sm" for="period12">1 an</label>
                </div>
              </div>
              <div class="vr"></div>
              <button class="btn btn-outline-primary btn-sm" title="Exporter les statistiques">
                <i class="bi bi-download me-1"></i>Export
              </button>
            </div>
          </div>
        </div>
        <div class="stats-charts-grid">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="stats-chart-card">
                        <div class="stats-chart-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stats-chart-icon bg-primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Répartition des Rôles</h6>
                                    <small class="text-muted">Distribution par catégorie d'utilisateur</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" title="Options">
                                <i class="bi bi-three-dots"></i>
                            </button>
                        </div>
                        <div class="stats-chart-body">
                            <canvas id="rolesChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="stats-chart-card">
                        <div class="stats-chart-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stats-chart-icon bg-success">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Statuts des Rendez-vous</h6>
                                    <small class="text-muted">Répartition par statut</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" title="Options">
                                <i class="bi bi-three-dots"></i>
                            </button>
                        </div>
                        <div class="stats-chart-body">
                            <canvas id="rendezvousChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="stats-chart-card">
                        <div class="stats-chart-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stats-chart-icon bg-warning">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Volumes Mensuels</h6>
                                    <small class="text-muted">Evolution des activités (<span id="windowLabel">12 derniers mois</span>)</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" title="Voir en plein écran">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Options">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </div>
                        </div>
                        <div class="stats-chart-body">
                            <canvas id="monthlyChart" height="260"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="stats-chart-card">
                        <div class="stats-chart-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stats-chart-icon bg-info">
                                    <i class="bi bi-calendar2-range"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Tendances des Rendez-vous</h6>
                                    <small class="text-muted">Evolution par statut (<span id="windowLabelStatus">12 derniers mois</span>)</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" title="Voir en plein écran">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Options">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </div>
                        </div>
                        <div class="stats-chart-body">
                            <canvas id="rdvStatusMonthlyChart" height="240"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Superviser rôles --}}
    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
        <div class="roles-supervision-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="bi bi-person-gear text-purple me-2"></i>Gestion des rôles</h4>
                    <p class="text-muted mb-0">Gestion avancée des rôles et permissions utilisateurs</p>
                </div>
                <div>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="tab" data-bs-target="#permissions">
                    <i class="bi bi-shield-lock me-1"></i> Aller à Gestion rôles & permissions
                  </button>
                </div>
            </div>
        </div>
        
        {{-- Tableau de gestion des rôles --}}
        <div class="roles-management-table">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Gestion des Rôles Utilisateurs</h5>
            <div class="d-flex align-items-center gap-2">
              <input type="text" id="searchRoles" class="form-control form-control-sm" placeholder="Rechercher un utilisateur..." style="max-width: 200px;">
              <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-funnel me-1"></i>Filtrer
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" data-filter="all">Tous les rôles</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#" data-filter="admin">Administrateurs</a></li>
                  <li><a class="dropdown-item" href="#" data-filter="medecin">Médecins</a></li>
                  <li><a class="dropdown-item" href="#" data-filter="secretaire">Secrétaires</a></li>
                  <li><a class="dropdown-item" href="#" data-filter="infirmier">Infirmiers</a></li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="table-container">
            <table class="table admin-table" id="rolesTable">
              <thead>
                <tr>
                  <th><i class="bi bi-person me-1"></i>Utilisateur</th>
                  <th><i class="bi bi-envelope me-1"></i>Email</th>
                  <th><i class="bi bi-person-badge me-1"></i>Rôle actuel</th>
                  <th><i class="bi bi-calendar me-1"></i>Depuis</th>
                  <th><i class="bi bi-activity me-1"></i>Statut</th>
                  <th><i class="bi bi-gear me-1"></i>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $u)
                  <tr data-role="{{ $u->role }}">
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        @php
                          $avatar = $u->avatar_url ? asset($u->avatar_url) : 'https://ui-avatars.com/api/?size=32&name=' . urlencode($u->name);
                        @endphp
                        <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                        <div>
                          <div class="fw-medium">{{ $u->name }}</div>
                          <small class="text-muted">ID: {{ $u->id }}</small>
                        </div>
                      </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                      @php
                        $roleClasses = [
                          'admin' => 'bg-danger',
                          'medecin' => 'bg-success', 
                          'secretaire' => 'bg-info',
                          'infirmier' => 'bg-primary',
                          'patient' => 'bg-warning text-dark'
                        ];
                        $roleClass = $roleClasses[$u->role] ?? 'bg-secondary';
                      @endphp
                      <span class="badge {{ $roleClass }} role-badge">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td>
                      <small class="text-muted">{{ $u->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                      <span class="badge {{ $u->active ? 'bg-success' : 'bg-secondary' }} status-badge">
                        <i class="bi {{ $u->active ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                        {{ $u->active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary js-open-role-modal"
                                data-user-id="{{ $u->id }}"
                                data-user-name="{{ $u->name }}"
                                data-current-role="{{ $u->role }}"
                                data-bs-toggle="modal"
                                data-bs-target="#roleUpdateModal"
                                title="Changer le rôle">
                          <i class="bi bi-person-gear"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" title="Voir détails">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
    </div>

    {{-- Gestion rôles & permissions --}}
    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
        <div class="permissions-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="bi bi-shield-lock text-danger me-2"></i>Gestion des Permissions</h4>
                    <p class="text-muted mb-0">Configuration avancée des accès et autorisations par rôle</p>
                </div>
            </div>
        </div>
        
        {{-- Alertes de sécurité --}}
        <div class="permissions-alerts">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                <div>
                    <strong>Attention !</strong> Les modifications de permissions prennent effet immédiatement.
                    Assurez-vous de tester les accès après chaque changement.
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.permissions.save') }}" id="permForm" class="permissions-form">
          @csrf
          
          {{-- Matrice des permissions --}}
          <div class="permissions-matrix">
            <div class="permissions-card">
                <div class="permissions-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="permission-module-icon">
                            <i class="bi {{ $essentialModule['icon'] ?? 'bi-gear' }}"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $essentialModule['title'] ?? 'Module Principal' }}</h5>
                            <small class="text-muted">Gestion des accès essentiels du système</small>
                        </div>
                    </div>
                    <div class="permission-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" title="Tout activer">
                            <i class="bi bi-check-all"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Tout désactiver">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="permissions-table-container">
                    <table class="table permissions-table">
                        <thead>
                            <tr>
                                <th class="role-column">
                                    <i class="bi bi-person-badge me-2"></i>Rôle
                                </th>
                                <th class="text-center permission-column">
                                    <i class="bi bi-shield me-2"></i>Niveau d'Accès
                                </th>
                                <th class="text-center status-column">
                                    <i class="bi bi-activity me-2"></i>Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['admin','secretaire','medecin','infirmier','patient'] as $r)
                              @php
                                $keys = array_map(fn($p)=>$p['key'], $essentialModule['permissions'] ?? []);
                                $valsByKey = [];
                                foreach ($keys as $k) { $valsByKey[$k] = ($rolePermissions[$r][$k] ?? false); }
                                $countTrue = count(array_filter($valsByKey));
                                if ($countTrue === count($keys)) {
                                  $currentLevel = 'full';
                                } elseif ($countTrue === 1) {
                                  $viewKey = null;
                                  foreach ($keys as $k) { if (str_ends_with($k, '.view')) { $viewKey = $k; break; } }
                                  $currentLevel = ($viewKey && !empty($valsByKey[$viewKey])) ? 'read' : 'full';
                                } else {
                                  $currentLevel = $countTrue > 0 ? 'full' : 'none';
                                }
                                
                                $roleConfig = [
                                  'admin' => ['icon' => 'bi-shield-fill-check', 'color' => 'danger', 'label' => 'Administrateur'],
                                  'secretaire' => ['icon' => 'bi-person-workspace', 'color' => 'info', 'label' => 'Secrétaire'],
                                  'medecin' => ['icon' => 'bi-person-hearts', 'color' => 'success', 'label' => 'Médecin'],
                                  'infirmier' => ['icon' => 'bi-person-plus-fill', 'color' => 'primary', 'label' => 'Infirmier'],
                                  'patient' => ['icon' => 'bi-person-circle', 'color' => 'warning', 'label' => 'Patient']
                                ];
                                $config = $roleConfig[$r] ?? ['icon' => 'bi-person', 'color' => 'secondary', 'label' => ucfirst($r)];
                              @endphp
                              <tr class="permission-row">
                                <td class="role-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="role-avatar bg-{{ $config['color'] }}">
                                            <i class="bi {{ $config['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="role-name">{{ $config['label'] }}</div>
                                            <small class="text-muted">{{ $r }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="permission-level-selector">
                                        @foreach([['none','Aucun','secondary'],['read','Lecture','warning'],['full','Complet','success']] as $opt)
              @php $id = 'lvl_ess_'.preg_replace('/\W+/','_', ($essentialModule['title'] ?? 'module').'_'.$r.'_'.$opt[0]); @endphp
                                          <input type="radio" class="btn-check" name="levels[{{ $essentialModule['title'] ?? 'module' }}][{{ $r }}]" id="{{ $id }}" autocomplete="off" value="{{ $opt[0] }}" {{ $currentLevel===$opt[0]?'checked':'' }}>
                                          <label class="btn btn-outline-{{ $opt[2] }} btn-sm permission-btn" for="{{ $id }}">
                                              <i class="bi bi-{{ $opt[0] === 'none' ? 'x-circle' : ($opt[0] === 'read' ? 'eye' : 'check-circle') }} me-1"></i>
                                              {{ $opt[1] }}
                                          </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $currentLevel === 'full' ? 'success' : ($currentLevel === 'read' ? 'warning' : 'secondary') }} status-badge">
                                        <i class="bi bi-{{ $currentLevel === 'full' ? 'shield-fill-check' : ($currentLevel === 'read' ? 'shield-fill-exclamation' : 'shield-fill-x') }} me-1"></i>
                                        {{ $currentLevel === 'full' ? 'Actif' : ($currentLevel === 'read' ? 'Limité' : 'Inactif') }}
                                    </span>
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
          
          {{-- Actions de sauvegarde --}}
          <div class="permissions-actions-footer">
              <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2 text-muted">
                      <i class="bi bi-info-circle"></i>
                      <small>Dernière modification : {{ date('d/m/Y H:i') }}</small>
                  </div>
                  <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-secondary">
                          <i class="bi bi-arrow-clockwise me-1"></i>Annuler
                      </button>
                      <button type="submit" class="btn btn-success">
                          <i class="bi bi-shield-check me-1"></i>Enregistrer les Permissions
                      </button>
                  </div>
              </div>
          </div>
        </form>
    </div>
    
    {{-- Paiements --}}
    <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
        <div class="payments-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="bi bi-wallet2 text-info me-2"></i>Paiements & Évaluations</h4>
                    <p class="text-muted mb-0">Suivi des transactions, revenus et qualité de service</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('secretaire.payments') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-gear me-1"></i>Gérer paiement
                    </a>
                </div>
            </div>
        </div>
        @php
            $recentOrders = \App\Models\Order::with('items','user')->orderByDesc('created_at')->take(20)->get();
        @endphp
        {{-- Tableau des paiements --}}
        <div class="payments-table-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Transactions Récentes</h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="searchPayments" class="form-control form-control-sm" placeholder="Rechercher une transaction..." style="max-width: 220px;">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-1"></i>Filtrer
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-payment-filter="all">Toutes les transactions</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-payment-filter="paid">Payées</a></li>
                            <li><a class="dropdown-item" href="#" data-payment-filter="pending">En attente</a></li>
                            <li><a class="dropdown-item" href="#" data-payment-filter="failed">Echouées</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-info btn-sm" title="Actualiser">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <table class="table admin-table" id="paymentsTable">
                    <thead>
                        <tr>
                            <th><i class="bi bi-calendar me-1"></i>Date & Heure</th>
                            <th><i class="bi bi-person me-1"></i>Patient</th>
                            <th><i class="bi bi-tag me-1"></i>Libellé</th>
                            <th class="text-end"><i class="bi bi-currency-exchange me-1"></i>Montant</th>
                            <th><i class="bi bi-building me-1"></i>Prestataire</th>
                            <th><i class="bi bi-activity me-1"></i>Statut</th>
                            <th class="text-end"><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                            <tr data-payment-status="{{ $o->status }}">
                                <td>
                                    <div class="payment-date">
                                        <div class="fw-medium">{{ $o->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $o->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($o->user)
                                            @php $avatar = $o->user->avatar_url ? asset($o->user->avatar_url) : 'https://ui-avatars.com/api/?size=32&name=' . urlencode($o->user->name); @endphp
                                            <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                                            <div>
                                                <div class="fw-medium">{{ $o->user->name }}</div>
                                                <small class="text-muted">{{ $o->user->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Utilisateur supprimé</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="payment-service">
                                        <div class="fw-medium">{{ optional($o->items->first())->label ?? 'Service médical' }}</div>
                                        <small class="text-muted">ID: {{ $o->id }}</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="payment-amount">
                                        <div class="fw-bold">{{ number_format($o->total_amount, 0, ',', ' ') }}</div>
                                        <small class="text-muted">XOF</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $providerConfig = [
                                            'wave' => ['name' => 'Wave', 'color' => 'primary', 'icon' => 'bi-credit-card'],
                                            'orange' => ['name' => 'Orange Money', 'color' => 'warning', 'icon' => 'bi-phone'],
                                            'mtn' => ['name' => 'MTN MoMo', 'color' => 'success', 'icon' => 'bi-phone'],
                                            'bank' => ['name' => 'Virement', 'color' => 'info', 'icon' => 'bi-bank']
                                        ];
                                        $provider = $providerConfig[strtolower($o->provider ?? '')] ?? ['name' => strtoupper($o->provider ?? 'N/A'), 'color' => 'secondary', 'icon' => 'bi-question-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $provider['color'] }} provider-badge">
                                        <i class="bi {{ $provider['icon'] }} me-1"></i>
                                        {{ $provider['name'] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'paid' => ['label' => 'Payé', 'color' => 'success', 'icon' => 'bi-check-circle'],
                                            'pending' => ['label' => 'En attente', 'color' => 'warning', 'icon' => 'bi-clock'],
                                            'failed' => ['label' => 'Echoué', 'color' => 'danger', 'icon' => 'bi-x-circle'],
                                            'cancelled' => ['label' => 'Annulé', 'color' => 'secondary', 'icon' => 'bi-dash-circle']
                                        ];
                                        $status = $statusConfig[$o->status] ?? ['label' => ucfirst($o->status), 'color' => 'secondary', 'icon' => 'bi-question-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $status['color'] }} status-badge">
                                        <i class="bi {{ $status['icon'] }} me-1"></i>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($o->status === 'paid')
                                            <a href="{{ route('payments.receipt', $o->id) }}" class="btn btn-outline-success btn-sm" title="Télécharger la quittance">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-outline-info btn-sm" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($o->status === 'pending')
                                            <button class="btn btn-outline-warning btn-sm" title="Relancer">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                                        <h6 class="text-muted mt-2">Aucune transaction</h6>
                                        <p class="text-muted small">Les paiements apparaîtront ici</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div> {{-- Fin admin-main-content --}}
  </div> {{-- Fin col-lg-9 --}}
</div> {{-- Fin row --}}
@endsection

<!-- Modal de mise à jour de rôle -->
<div class="modal fade" id="roleUpdateModal" tabindex="-1" aria-labelledby="roleUpdateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleUpdateLabel">Changer le rôle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <form id="roleUpdateForm" method="POST" action="#">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-2 small text-muted">Utilisateur: <span id="roleUserName">—</span></div>
          <div class="mb-2">Sélectionnez le nouveau rôle:</div>
          <div class="d-flex flex-column gap-1">
            @foreach(['admin'=>'Administrateur','secretaire'=>'Secrétaire','medecin'=>'Médecin','infirmier'=>'Infirmier','patient'=>'Patient'] as $val=>$label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="role" id="role_{{ $val }}" value="{{ $val }}">
              <label class="form-check-label" for="role_{{ $val }}">{{ $label }}</label>
            </div>
            @endforeach
          </div>
          <div class="alert alert-warning mt-3 p-2" role="alert">
            Confirmez-vous la modification du rôle de cet utilisateur ?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Confirmer</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>

<!-- Toast succès -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
  <div id="roleToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="roleToastBody">
        Rôle mis à jour.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Rôle: ouverture du modal et soumission
    (function(){
      const modalEl = document.getElementById('roleUpdateModal');
      const form = document.getElementById('roleUpdateForm');
      const userNameSpan = document.getElementById('roleUserName');
      if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget; // bouton déclencheur
          if (!button) return;
          const uid = button.getAttribute('data-user-id');
          const uname = button.getAttribute('data-user-name') || '';
          const current = button.getAttribute('data-current-role') || '';
          if (userNameSpan) userNameSpan.textContent = uname;
          (form?.querySelectorAll('input[name="role"]')||[]).forEach(r=>{ r.checked = (r.value===current); });
          if (form) form.action = `/admin/users/${uid}/role`;
        });
      }

      // Toast succès si session('success')
      const hasSuccess = {{ session('success') ? 'true' : 'false' }};
      if (hasSuccess) {
        const toastEl = document.getElementById('roleToast');
        const toastBody = document.getElementById('roleToastBody');
        if (toastBody) toastBody.textContent = @json(session('success'));
        if (toastEl) new bootstrap.Toast(toastEl, { delay: 3000 }).show();
      }
    })();

    // Données dynamiques transmises depuis le contrôleur
    const rolesCount = @json($rolesCount ?? []);
    const months = @json($months ?? []);
    const rdvSeries = @json($rendezvousCounts ?? []);
    const admissionsSeries = @json($admissionsCounts ?? []);
    const consultsSeries = @json($consultationsCounts ?? []);
    const patientsSeries = @json($patientsCounts ?? []);
    const rdvStatusCounts = @json($rdvStatusCounts ?? []);
    const rdvPendingSeriesFull = @json($rdvPendingSeries ?? []);
    const rdvConfirmedSeriesFull = @json($rdvConfirmedSeries ?? []);
    const rdvCancelledSeriesFull = @json($rdvCancelledSeries ?? []);
    
    // Variables globales pour les graphiques
    let chartsInitialized = false;
    let monthlyChart, rdvStatusMonthlyChart;
    let windowMonths = 12; // défaut 12 mois
    
    // Palette
    const colors = {
      green: '#27ae60', darkGreen: '#145a32', teal: '#20c997', orange: '#fd7e14', pink: '#e83e8c', blue: '#3b82f6', red: '#ef4444', yellow: '#eab308'
    };
    
    // Outil: slicer pour fenêtre N derniers mois
    function lastN(arr, n){ return (arr || []).slice(Math.max((arr || []).length - n, 0)); }
    
    function windowLabelText(n){
      if (n===2) return '2 derniers mois';
      if (n===6) return '6 derniers mois';
      return '12 derniers mois';
    }
    
    function buildMonthlyConfig(n){
      const lbls = lastN(months, n);
      return {
        type: 'line',
        data: {
          labels: lbls,
          datasets: [
            { label: 'Rendez-vous', data: lastN(rdvSeries, n), borderColor: colors.green, backgroundColor: colors.green + '33', tension: .3, fill: true },
            { label: 'Consultations', data: lastN(consultsSeries, n), borderColor: colors.blue, backgroundColor: colors.blue + '33', tension: .3, fill: true },
            { label: 'Admissions', data: lastN(admissionsSeries, n), borderColor: colors.orange, backgroundColor: colors.orange + '33', tension: .3, fill: true },
            { label: 'Patients (créés)', data: lastN(patientsSeries, n), borderColor: colors.pink, backgroundColor: colors.pink + '33', tension: .3, fill: true }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' }, title: { display: true, text: windowLabelText(n) } },
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
      };
    }
    
    function buildRdvStatusMonthlyConfig(n){
      const lbls = lastN(months, n);
      return {
        type: 'line',
        data: {
          labels: lbls,
          datasets: [
            { label: 'RDV en attente', data: lastN(rdvPendingSeriesFull, n), borderColor: colors.yellow, backgroundColor: colors.yellow + '33', tension: .3, fill: true },
            { label: 'RDV confirmés', data: lastN(rdvConfirmedSeriesFull, n), borderColor: colors.green, backgroundColor: colors.green + '33', tension: .3, fill: true },
            { label: 'RDV annulés', data: lastN(rdvCancelledSeriesFull, n), borderColor: colors.red, backgroundColor: colors.red + '33', tension: .3, fill: true }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' }, title: { display: true, text: windowLabelText(n) } },
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
      };
    }
    
    // Fonction pour initialiser tous les graphiques
    function initializeCharts() {
        if (chartsInitialized) return;
        
        // Vérifier si Chart.js est chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js n\'est pas chargé !');
            return;
        }
        
        console.log('Initialisation des graphiques...');
        initRolesChart();
        initRendezVousChart();
        initMonthlyCharts();
        
        chartsInitialized = true;
    }
    
    // Graphique des rôles
    function initRolesChart() {

        const rolesLabels = Object.keys(rolesCount);
        const rolesValues = Object.values(rolesCount);
        
        const rolesChartElement = document.getElementById('rolesChart');
        if (!rolesChartElement) {
            console.error('Élément rolesChart non trouvé !');
            return;
        }
        
        new Chart(rolesChartElement, {
          type: 'bar',
          data: {
            labels: rolesLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{
              label: "Utilisateurs",
              data: rolesValues,
              backgroundColor: [colors.blue, colors.green, colors.yellow, colors.teal, colors.red]
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false }, title: { display: true, text: 'Utilisateurs par rôle' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
        });
    }
    
    // Graphique des statuts RDV
    function initRendezVousChart() {

        const rdvStatusLabels = Object.keys(rdvStatusCounts).map(s => (s || 'non défini').replace('_',' '));
        const rdvStatusValues = Object.values(rdvStatusCounts);
        
        const rendezvousChartElement = document.getElementById('rendezvousChart');
        if (!rendezvousChartElement) {
            console.error('Élément rendezvousChart non trouvé !');
            return;
        }
        
        new Chart(rendezvousChartElement, {
          type: 'doughnut',
          data: {
            labels: rdvStatusLabels,
            datasets: [{
              data: rdvStatusValues,
              backgroundColor: [colors.green, colors.orange, colors.red, colors.blue, colors.pink, colors.yellow]
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Répartition des statuts de RDV' } }
          }
        });
    }
    
    // Graphiques mensuels
    function initMonthlyCharts() {
        console.log('Initialisation des graphiques mensuels...');
        
        // Vérifier les données nécessaires
        if (!months || months.length === 0) {
            console.error('Données des mois manquantes !', months);
            return;
        }
        
        if (!rdvSeries || rdvSeries.length === 0) {
            console.error('Données RDV manquantes !', rdvSeries);
            return;
        }
        
        console.log('Vérification des éléments canvas...');
        const monthlyChartElement = document.getElementById('monthlyChart');
        const rdvStatusMonthlyElement = document.getElementById('rdvStatusMonthlyChart');
        
        if (!monthlyChartElement) {
            console.error('Élément monthlyChart non trouvé !', monthlyChartElement);
            return;
        }
        
        if (!rdvStatusMonthlyElement) {
            console.error('Élément rdvStatusMonthlyChart non trouvé !', rdvStatusMonthlyElement);
            return;
        }
        
        console.log('Éléments canvas trouvés:', {
            monthlyChart: monthlyChartElement,
            rdvStatusChart: rdvStatusMonthlyElement
        });
        
        try {
            console.log('Création du graphique monthlyChart...');
            const monthlyCtx = monthlyChartElement.getContext('2d');
            const monthlyConfig = buildMonthlyConfig(windowMonths);
            console.log('Configuration monthlyChart:', monthlyConfig);
            monthlyChart = new Chart(monthlyCtx, monthlyConfig);
            console.log('MonthlyChart créé:', monthlyChart);
        } catch (error) {
            console.error('Erreur lors de la création du monthlyChart:', error);
        }
        
        try {
            console.log('Création du graphique rdvStatusMonthlyChart...');
            const rdvStatusMonthlyCtx = rdvStatusMonthlyElement.getContext('2d');
            const rdvStatusConfig = buildRdvStatusMonthlyConfig(windowMonths);
            console.log('Configuration rdvStatusChart:', rdvStatusConfig);
            rdvStatusMonthlyChart = new Chart(rdvStatusMonthlyCtx, rdvStatusConfig);
            console.log('RdvStatusChart créé:', rdvStatusMonthlyChart);
        } catch (error) {
            console.error('Erreur lors de la création du rdvStatusChart:', error);
        }
        
        console.log('Graphiques mensuels - Processus terminé !');
    }



    function setWindow(n){
      windowMonths = n;
      document.getElementById('windowLabel').textContent = windowLabelText(n);
      document.getElementById('windowLabelStatus').textContent = windowLabelText(n);
      
      // Vérifier que les graphiques existent avant de les détruire
      if (monthlyChart) {
          monthlyChart.destroy();
      }
      if (rdvStatusMonthlyChart) {
          rdvStatusMonthlyChart.destroy();
      }
      
      // Recréer les graphiques s'ils existaient
      const monthlyChartElement = document.getElementById('monthlyChart');
      const rdvStatusMonthlyElement = document.getElementById('rdvStatusMonthlyChart');
      
      if (monthlyChartElement && rdvStatusMonthlyElement) {
          const monthlyCtx = monthlyChartElement.getContext('2d');
          const rdvStatusMonthlyCtx = rdvStatusMonthlyElement.getContext('2d');
          
          monthlyChart = new Chart(monthlyCtx, buildMonthlyConfig(n));
          rdvStatusMonthlyChart = new Chart(rdvStatusMonthlyCtx, buildRdvStatusMonthlyConfig(n));
      }
      
      // Boutons actifs
      document.querySelectorAll('[data-window]').forEach(b=>b.classList.toggle('active', parseInt(b.dataset.window,10)===n));
    }

    // Bind boutons de période (ancienne méthode + nouvelle)
    document.querySelectorAll('[data-window]').forEach(btn=>{
      btn.addEventListener('click', ()=> setWindow(parseInt(btn.dataset.window,10)) );
    });
    
    // Nouvelle méthode avec radio buttons
    document.querySelectorAll('input[name="statsPeriod"]').forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.checked) {
          setWindow(parseInt(this.dataset.window, 10));
        }
      });
    });
    
    // Initialiser les graphiques quand l'onglet statistiques est ouvert
    const statsTabButton = document.getElementById('stats-tab');
    if (statsTabButton) {
        statsTabButton.addEventListener('click', function() {
            // Attendre un peu que l'onglet soit visible
            setTimeout(initializeCharts, 100);
        });
    }
    
    // Si l'onglet statistiques est déjà actif au chargement de la page
    const statsTab = document.getElementById('stats');
    if (statsTab && statsTab.classList.contains('active')) {
        setTimeout(initializeCharts, 100);
    }

    // Filtres avancés pour les tables Admin
    function filterTable(inputId, tableId, filterAttribute = null){
      const q = (document.getElementById(inputId)?.value || '').toLowerCase();
      const rows = document.querySelectorAll(`#${tableId} tbody tr`);
      rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        let shouldShow = text.includes(q);
        
        // Filtre supplémentaire par attribut si spécifié
        if (filterAttribute && filterAttribute !== 'all') {
          const roleAttr = tr.getAttribute('data-role');
          shouldShow = shouldShow && (roleAttr === filterAttribute);
        }
        
        tr.style.display = shouldShow ? '' : 'none';
      });
    }
    
    // Filtres de base
    document.getElementById('searchUsers')?.addEventListener('input', ()=>filterTable('searchUsers','usersTable'));
    document.getElementById('searchPatients')?.addEventListener('input', ()=>filterTable('searchPatients','patientsTable'));
    
    // Nouveau filtre pour les rôles
    document.getElementById('searchRoles')?.addEventListener('input', ()=>filterTable('searchRoles','rolesTable'));
    
    // Filtres par rôle dans l'onglet supervision
    document.querySelectorAll('[data-filter]').forEach(filterBtn => {
      filterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const filter = this.getAttribute('data-filter');
        const searchInput = document.getElementById('searchRoles');
        const currentQuery = searchInput?.value || '';
        
        // Appliquer le filtre
        const rows = document.querySelectorAll('#rolesTable tbody tr');
        rows.forEach(tr => {
          const text = tr.innerText.toLowerCase();
          const roleAttr = tr.getAttribute('data-role');
          
          let shouldShow = text.includes(currentQuery.toLowerCase());
          if (filter !== 'all') {
            shouldShow = shouldShow && (roleAttr === filter);
          }
          
          tr.style.display = shouldShow ? '' : 'none';
        });
        
        // Mettre à jour l'apparence du bouton actif
        document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
      });
    });

    // Debug: Affichage des données pour vérification
    console.log('Données pour graphiques:');
    console.log('Rôles:', rolesCount);
    console.log('Mois:', months);
    console.log('RDV:', rdvSeries);
    console.log('Consultations:', consultsSeries);
    console.log('Admissions:', admissionsSeries);
    console.log('Patients:', patientsSeries);
    console.log('Statuts RDV:', rdvStatusCounts);
    console.log('RDV Pending:', rdvPendingSeriesFull);
    console.log('RDV Confirmed:', rdvConfirmedSeriesFull);
    console.log('RDV Cancelled:', rdvCancelledSeriesFull);
</script>
@endsection

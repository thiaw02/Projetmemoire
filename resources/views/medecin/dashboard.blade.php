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
  
  .action-card.evaluations {
    --action-color: #f59e0b;
  }
  .action-card.evaluations .action-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
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
  
  /* Styles pour le calendrier interactif */
  .calendar-grid {
    width: 100%;
  }
  
  .calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #f8fafc;
    border-radius: 8px 8px 0 0;
  }
  
  .calendar-day-name {
    text-align: center;
    padding: 0.75rem 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    color: #64748b;
    border-right: 1px solid #e2e8f0;
  }
  
  .calendar-day-name:last-child {
    border-right: none;
  }
  
  .calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border: 1px solid #e2e8f0;
    border-top: none;
  }
  
  .calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .calendar-day:nth-child(7n) {
    border-right: none;
  }
  
  .calendar-day:hover {
    background: #f1f5f9;
  }
  
  .calendar-day.other-month {
    color: #cbd5e1;
    background: #f8fafc;
  }
  
  .calendar-day.today {
    background: #3b82f6;
    color: white;
    font-weight: 700;
  }
  
  .calendar-day.has-rdv {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-weight: 600;
  }
  
  .calendar-day.has-rdv:hover {
    background: linear-gradient(135deg, #059669, #047857);
  }
  
  .calendar-day.selected {
    background: #667eea !important;
    color: white;
    box-shadow: inset 0 0 0 2px #4f46e5;
  }
  
  .rdv-indicator {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 6px;
    height: 6px;
    background: #ef4444;
    border-radius: 50%;
    font-size: 0.7rem;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 12px;
    min-height: 12px;
  }
  
  .selected-day-panel {
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
  }
  
  .selected-day-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    background: white;
  }
  
  .selected-day-content {
    max-height: 300px;
    overflow-y: auto;
    padding: 0;
  }
  
  .selected-day-rdv-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    background: white;
    margin: 0;
    transition: background 0.2s ease;
  }
  
  .selected-day-rdv-item:hover {
    background: #f8fafc;
  }
  
  .selected-day-rdv-item:last-child {
    border-bottom: none;
  }
  
  .selected-day-patient {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .selected-day-avatar {
    width: 36px;
    height: 36px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
  }
  
  .selected-day-info h6 {
    margin: 0;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  .selected-day-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.8rem;
  }
  
  .selected-day-actions {
    display: flex;
    gap: 0.5rem;
  }
  
  .btn-action.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
  
  /* Styles pour la vue unifiée */
  .unified-calendar-card {
    min-height: 650px;
    max-height: 750px;
  }
  
  .view-switcher .btn {
    border-color: rgba(255,255,255,0.8) !important;
    border-width: 1px !important;
    background-color: rgba(255,255,255,0.15) !important;
    color: rgba(255,255,255,0.95) !important;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
  }
  
  .view-switcher .btn:hover {
    background-color: rgba(255,255,255,0.25) !important;
    border-color: rgba(255,255,255,0.9) !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }
  
  .view-switcher .btn.active {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    border-color: #1e40af !important;
    border-width: 2px !important;
    color: white !important;
    font-weight: 700;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4), inset 0 1px 2px rgba(255,255,255,0.2);
  }
  
  .view-content {
    transition: all 0.3s ease;
  }
  
  /* Améliorations du calendrier en grand format */
  .unified-calendar-card .calendar-day {
    min-height: 70px;
    max-height: 70px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .unified-calendar-card .calendar-grid {
    border-radius: 12px;
    overflow: hidden;
    height: fit-content;
  }
  
  .unified-calendar-card .calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(6, 70px);
    border: 1px solid #e2e8f0;
    border-top: none;
  }
  
  .unified-calendar-card .selected-day-panel {
    max-height: 250px;
    border-top: 1px solid #e2e8f0;
  }
  
  /* Assurer que le calendrier tient dans la vue */
  .unified-calendar-card .modern-card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  
  .unified-calendar-card #calendar-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  
  /* Filtres redessinés avec couleurs vives */
  .rdv-filters .btn {
    border-color: rgba(255,255,255,0.8) !important;
    border-width: 1px !important;
    background-color: rgba(255,255,255,0.15) !important;
    color: rgba(255,255,255,0.95) !important;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
  }
  
  .rdv-filters .btn:hover {
    background-color: rgba(255,255,255,0.25) !important;
    border-color: rgba(255,255,255,0.9) !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }
  
  .rdv-filters .btn.active {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    border-color: #047857 !important;
    border-width: 2px !important;
    color: white !important;
    font-weight: 700;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4), inset 0 1px 2px rgba(255,255,255,0.2);
  }
  
  /* Badge amélioré avec statut */
  .patient-meta .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
  }
  
  /* Contrôles toujours visibles avec couleurs distinctives */
  #calendar-controls .btn {
    background: rgba(255,255,255,0.2) !important;
    border-color: rgba(255,255,255,0.8) !important;
    border-width: 1px !important;
    color: white !important;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
    min-width: 40px;
    border-radius: 8px;
  }
  
  #calendar-controls .btn:hover {
    background: rgba(255,255,255,0.3) !important;
    border-color: white !important;
    color: white !important;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }
  
  #calendar-controls .badge {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85)) !important;
    border: 1px solid rgba(255,255,255,0.8) !important;
    color: #1f2937 !important;
    font-weight: 700;
    text-shadow: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    min-width: 120px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2), inset 0 1px 2px rgba(255,255,255,0.5);
    backdrop-filter: blur(10px);
  }
  
  /* Indicateurs d'événements actifs distinctifs */
  .calendar-day.has-rdv {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: white !important;
    font-weight: 700;
    position: relative;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    transform: scale(1.02);
    cursor: pointer;
  }
  
  .calendar-day.has-rdv:hover {
    background: linear-gradient(135deg, #d97706, #b45309) !important;
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.6);
    transform: scale(1.05);
  }
  
  /* Les jours sans RDV ne sont pas cliquables */
  .calendar-day:not(.has-rdv) {
    cursor: default;
  }
  
  .calendar-day:not(.has-rdv):hover {
    background: #f1f5f9;
    transform: none;
  }
  
  .calendar-day.has-rdv::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #f59e0b, #10b981, #3b82f6, #8b5cf6);
    border-radius: inherit;
    z-index: -1;
    animation: borderGlow 2s ease-in-out infinite alternate;
  }
  
  @keyframes borderGlow {
    0% { opacity: 0.5; }
    100% { opacity: 0.8; }
  }
  
  .rdv-indicator {
    position: absolute;
    top: 3px;
    right: 3px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border-radius: 50%;
    min-width: 18px;
    min-height: 18px;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5);
    border: 2px solid white;
  }
  
  /* Jour sélectionné avec effet spécial */
  .calendar-day.selected {
    background: linear-gradient(135deg, #667eea, #764ba2) !important;
    color: white !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.5), inset 0 0 20px rgba(255,255,255,0.2) !important;
    transform: scale(1.1);
    z-index: 10;
    position: relative;
  }
  
  /* Responsive avec éléments toujours visibles */
  @media (max-width: 768px) {
    .unified-calendar-card {
      min-height: 600px;
    }
    
    .unified-calendar-card .calendar-day {
      min-height: 55px;
      max-height: 55px;
      font-size: 0.9rem;
    }
    
    .unified-calendar-card .calendar-days {
      grid-template-rows: repeat(6, 55px);
    }
    
    .view-switcher .btn,
    .rdv-filters .btn {
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
    }
    
    #calendar-controls .badge {
      min-width: 100px;
      padding: 0.4rem 0.8rem;
      font-size: 0.85rem;
    }
    
    .rdv-indicator {
      min-width: 16px;
      min-height: 16px;
      font-size: 0.65rem;
    }
  }
  
  @media (max-width: 576px) {
    .unified-calendar-card {
      min-height: 520px;
    }
    
    .unified-calendar-card .calendar-day {
      min-height: 50px;
      max-height: 50px;
      font-size: 0.85rem;
    }
    
    .unified-calendar-card .calendar-days {
      grid-template-rows: repeat(6, 50px);
    }
    
    .view-switcher .btn,
    .rdv-filters .btn {
      padding: 0.4rem 0.6rem;
      font-size: 0.8rem;
    }
    
    #calendar-controls .badge {
      min-width: 80px;
      padding: 0.3rem 0.6rem;
      font-size: 0.8rem;
    }
    
    .calendar-header .calendar-day-name {
      padding: 0.5rem 0.25rem;
      font-size: 0.8rem;
    }
    
    .rdv-indicator {
      min-width: 14px;
      min-height: 14px;
      font-size: 0.6rem;
      top: 2px;
      right: 2px;
    }
  }
  
  /* Styles pour la section dossiers récents */
  .recent-patient-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
  }
  
  .recent-patient-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #3b82f6;
  }
  
  .recent-patient-card .patient-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
  }
  
  .recent-patient-card .patient-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
    font-size: 1.1rem;
  }
  
  .recent-patient-card .patient-name {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
    font-weight: 600;
    font-size: 1rem;
  }
  
  .recent-patient-card .patient-name a:hover {
    color: #3b82f6 !important;
  }
  
  .recent-patient-card .patient-meta {
    margin: 0;
    color: #6b7280;
    font-size: 0.85rem;
  }
  
  .recent-patient-card .quick-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
  }
  
  .recent-patients-card .modern-card-header {
    background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
  }
  
  /* Styles améliorés pour l'affichage des RDV du jour */
  .selected-day-rdv-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    background: white;
    transition: all 0.2s ease;
    position: relative;
  }
  
  .selected-day-rdv-item:hover {
    background: #f8fafc;
  }
  
  .selected-day-rdv-item.completed {
    background: #f0fdf4;
    opacity: 0.8;
  }
  
  .selected-day-rdv-item:last-child {
    border-bottom: none;
  }
  
  .rdv-time-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 1rem;
    min-width: 60px;
  }
  
  .time-badge {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    padding: 0.4rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    min-width: 55px;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
  }
  
  .time-line {
    width: 2px;
    height: 40px;
    background: linear-gradient(to bottom, #3b82f6, #e2e8f0);
    margin-top: 0.5rem;
    border-radius: 1px;
  }
  
  .time-line.last {
    background: #e2e8f0;
    height: 10px;
  }
  
  .selected-day-patient {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
  }
  
  .selected-day-avatar {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
  }
  
  .selected-day-info {
    flex: 1;
  }
  
  .patient-name-cal {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
  }
  
  .rdv-details {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .rdv-details .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
  }
  
  .selected-day-actions {
    display: flex;
    gap: 0.4rem;
    align-items: center;
  }
  
  .selected-day-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 12px 12px 0 0;
  }
  
  .selected-day-header h6 {
    color: white !important;
    margin: 0;
    font-weight: 600;
  }
  
  .selected-day-header .badge {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
  }
  
  .selected-day-content {
    border-radius: 0 0 12px 12px;
    overflow: hidden;
  }
  
  /* Badge de date sélectionnée */
  #selected-date-badge {
    background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
    color: white !important;
    font-weight: 600;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    animation: slideInFromRight 0.3s ease;
  }
  
  @keyframes slideInFromRight {
    0% {
      opacity: 0;
      transform: translateX(20px);
    }
    100% {
      opacity: 1;
      transform: translateX(0);
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
          <a href="{{ route('simple-evaluations.professional-dashboard') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1" title="Mes Évaluations">
            <i class="bi bi-star-fill"></i>
            <span class="d-none d-md-inline">Mes Évaluations</span>
          </a>
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


    {{-- Section unifiée : Calendrier et RDV --}}
    <div class="row g-4">
      <div class="col-12">
        <div class="modern-card unified-calendar-card">
          <div class="modern-card-header">
            <div class="d-flex align-items-center">
              <i class="bi bi-calendar-check me-2"></i>
              <span>Planning des rendez-vous</span>
              <span class="badge bg-white text-success ms-2">{{ ($upcomingRdv ?? collect())->count() }} RDV</span>
            </div>
            
            {{-- Commutateur de vue --}}
            <div class="ms-auto d-flex align-items-center gap-2">
              <div class="btn-group view-switcher" role="group">
                <button type="button" id="calendar-view-btn" class="btn btn-outline-light btn-sm active">
                  <i class="bi bi-calendar3 me-1"></i>
                  <span>Calendrier</span>
                </button>
                <button type="button" id="list-view-btn" class="btn btn-outline-light btn-sm">
                  <i class="bi bi-list-ul me-1"></i>
                  <span>Liste</span>
                </button>
              </div>
              
              {{-- Contrôles du calendrier --}}
              <div id="calendar-controls" class="d-flex align-items-center gap-2">
                <button id="prevMonth" class="btn btn-sm btn-outline-light">
                  <i class="bi bi-chevron-left"></i>
                </button>
                <span id="currentMonth" class="badge bg-white text-dark px-3"></span>
                <button id="nextMonth" class="btn btn-sm btn-outline-light">
                  <i class="bi bi-chevron-right"></i>
                </button>
              </div>
              
              {{-- Filtres de la liste --}}
              <div id="list-controls" class="d-flex align-items-center gap-2" style="display: none;">
                <div class="btn-group rdv-filters" role="group">
                  <button class="btn btn-outline-light btn-sm filter-btn" data-rdv-filter="day">
                    <i class="bi bi-calendar-day me-1"></i>
                    <span>Aujourd'hui</span>
                  </button>
                  <button class="btn btn-outline-light btn-sm filter-btn" data-rdv-filter="week">
                    <i class="bi bi-calendar-week me-1"></i>
                    <span>Semaine</span>
                  </button>
                  <button class="btn btn-outline-light btn-sm filter-btn active" data-rdv-filter="all">
                    <i class="bi bi-list-task me-1"></i>
                    <span>Tous</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modern-card-body p-0">
            {{-- Vue Calendrier --}}
            <div id="calendar-view" class="view-content">
              <div id="calendar-container">
                <div class="calendar-grid">
                  <div class="calendar-header">
                    <div class="calendar-day-name">Dim</div>
                    <div class="calendar-day-name">Lun</div>
                    <div class="calendar-day-name">Mar</div>
                    <div class="calendar-day-name">Mer</div>
                    <div class="calendar-day-name">Jeu</div>
                    <div class="calendar-day-name">Ven</div>
                    <div class="calendar-day-name">Sam</div>
                  </div>
                  <div id="calendar-days" class="calendar-days">
                    <!-- Les jours seront générés dynamiquement -->
                  </div>
                </div>
              </div>
              
              {{-- Zone d'affichage des RDV du jour sélectionné --}}
              <div id="selected-day-rdv" class="selected-day-panel" style="display: none;">
                <div class="selected-day-header">
                  <h6 class="mb-0" id="selected-day-title">RDV du jour</h6>
                  <button class="btn btn-sm btn-outline-secondary" id="close-day-panel">
                    <i class="bi bi-x"></i>
                  </button>
                </div>
                <div id="selected-day-content" class="selected-day-content">
                  <!-- Contenu dynamique des RDV -->
                </div>
              </div>
            </div>
            
            {{-- Vue Liste --}}
            <div id="list-view" class="view-content" style="display: none;">
              <div class="p-4">
                @if(($upcomingRdv ?? collect())->isEmpty())
                  <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <h5>Aucun RDV programmé</h5>
                    <p>Votre agenda est libre pour le moment.</p>
                  </div>
                @else
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
                              <span class="badge ms-2 {{ in_array(strtolower($rdv->statut), ['terminé','termine','terminée','terminee']) ? 'bg-success' : 'bg-primary' }}">{{ $rdv->statut }}</span>
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
        </div>
      </div>
    </div>
    
    {{-- Actions rapides modernes --}}
    <div class="row mt-4">
      <div class="col-12">
        <h4 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Actions Rapides</h4>
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
      </div>
    </div>

    {{-- Dossiers récents consultés --}}
    <div class="row mt-4">
      <div class="col-12">
        <div class="modern-card recent-patients-card">
          <div class="modern-card-header bg-info">
            <i class="bi bi-clock-history me-2"></i>
            <span>Dossiers récemment consultés</span>
            <span class="badge bg-white text-info ms-2">{{ ($recentPatients ?? collect())->count() }}</span>
          </div>
          <div class="modern-card-body">
            @if(($recentPatients ?? collect())->isEmpty())
              <div class="empty-state">
                <i class="bi bi-folder2-open"></i>
                <h5>Aucun dossier consulté</h5>
                <p>Vos consultations récentes apparaîtront ici.</p>
              </div>
            @else
              <div class="row g-3">
                @foreach($recentPatients as $patient)
                  <div class="col-lg-4 col-md-6">
                    <div class="recent-patient-card">
                      <div class="patient-info">
                        <div class="patient-avatar bg-primary">
                          {{ strtoupper(substr($patient->nom ?? 'P', 0, 1)) }}
                        </div>
                        <div class="patient-details">
                          <h6 class="patient-name">
                            <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="text-decoration-none text-dark">
                              {{ $patient->nom }} {{ $patient->prenom }}
                            </a>
                          </h6>
                          <p class="patient-meta">
                            <i class="bi bi-eye me-1"></i>Consulté récemment
                          </p>
                        </div>
                      </div>
                      
                      <div class="quick-actions">
                        <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn-action btn-primary-action" title="Ouvrir le dossier">
                          <i class="bi bi-folder2-open"></i>
                        </a>
                        <a href="{{ route('medecin.consultations') }}?patient_id={{ $patient->id }}" class="btn-action btn-success-action" title="Nouvelle consultation">
                          <i class="bi bi-clipboard-plus"></i>
                        </a>
                        <a href="{{ route('medecin.ordonnances') }}?patient_id={{ $patient->id }}" class="btn-action btn-warning-action" title="Nouvelle ordonnance">
                          <i class="bi bi-prescription2"></i>
                        </a>
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
      
      // Supprimer le badge de date filtrée si il existe
      const dateBadge = document.getElementById('selected-date-badge');
      if (dateBadge) {
        dateBadge.remove();
      }
      
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
    
    // === CALENDRIER INTERACTIF ===
    
    // Données des RDV pour l'affichage du calendrier (générées depuis Laravel)
    const rdvData = @json($upcomingRdv ?? []);
    // Données des consultations pour l'affichage détaillé
    const consultationsData = @json($consultations ?? []);
    
    let currentDate = new Date();
    let selectedDate = null;
    
    // Éléments du DOM
    const calendarDays = document.getElementById('calendar-days');
    const currentMonthSpan = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const selectedDayPanel = document.getElementById('selected-day-rdv');
    const selectedDayTitle = document.getElementById('selected-day-title');
    const selectedDayContent = document.getElementById('selected-day-content');
    const closeDayPanel = document.getElementById('close-day-panel');
    
    // Utilitaires de date
    function formatDateKey(date) {
        return date.toISOString().slice(0, 10);
    }
    
    function getMonthName(date) {
        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        return `${months[date.getMonth()]} ${date.getFullYear()}`;
    }
    
    function isSameDay(date1, date2) {
        return formatDateKey(date1) === formatDateKey(date2);
    }
    
    // Créer un index des RDV par date pour l'affichage du calendrier
    function createRdvIndex() {
        const index = {};
        rdvData.forEach(rdv => {
            const dateKey = rdv.date;
            if (!index[dateKey]) {
                index[dateKey] = [];
            }
            index[dateKey].push(rdv);
        });
        return index;
    }
    
    // Créer un index des consultations par date pour l'affichage détaillé
    function createConsultationsIndex() {
        const index = {};
        consultationsData.forEach(consultation => {
            const dateKey = consultation.date_consultation;
            if (!index[dateKey]) {
                index[dateKey] = [];
            }
            index[dateKey].push(consultation);
        });
        return index;
    }
    
    const rdvIndex = createRdvIndex();
    const consultationsIndex = createConsultationsIndex();
    
    // Générer le calendrier pour le mois courant
    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Mise à jour du titre du mois
        currentMonthSpan.textContent = getMonthName(currentDate);
        
        // Premier jour du mois et nombre de jours
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();
        
        // Vider le calendrier
        calendarDays.innerHTML = '';
        
        // Jours du mois précédent (grisés)
        const prevMonth = new Date(year, month - 1, 0);
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonth.getDate() - i;
            const dayElement = createDayElement(new Date(year, month - 1, day), true);
            calendarDays.appendChild(dayElement);
        }
        
        // Jours du mois courant
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = createDayElement(new Date(year, month, day), false);
            calendarDays.appendChild(dayElement);
        }
        
        // Jours du mois suivant pour compléter la grille
        const totalCells = calendarDays.children.length;
        const remainingCells = 42 - totalCells; // 6 semaines x 7 jours
        for (let day = 1; day <= Math.min(remainingCells, 14); day++) {
            const dayElement = createDayElement(new Date(year, month + 1, day), true);
            calendarDays.appendChild(dayElement);
        }
    }
    
    // Créer un élément jour
    function createDayElement(date, isOtherMonth) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = date.getDate();
        
        const dateKey = formatDateKey(date);
        const today = new Date();
        
        // Classes CSS conditionnelles
        if (isOtherMonth) {
            dayElement.classList.add('other-month');
        }
        
        if (isSameDay(date, today) && !isOtherMonth) {
            dayElement.classList.add('today');
        }
        
        if (rdvIndex[dateKey] && !isOtherMonth) {
            dayElement.classList.add('has-rdv');
            
            // Indicateur du nombre de RDV
            const indicator = document.createElement('div');
            indicator.className = 'rdv-indicator';
            indicator.textContent = rdvIndex[dateKey].length;
            dayElement.appendChild(indicator);
        }
        
        // Gestionnaire de clic
        dayElement.addEventListener('click', () => {
            if (isOtherMonth) return;
            
            // Vérifier s'il y a des RDV pour cette date
            if (!rdvIndex[dateKey] || rdvIndex[dateKey].length === 0) {
                // Si pas de RDV, ne rien faire ou afficher un message
                console.log('Aucun RDV pour cette date');
                return;
            }
            
            // Retirer la sélection précédente
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Ajouter la sélection
            dayElement.classList.add('selected');
            selectedDate = new Date(date);
            
            // Basculer vers la vue liste
            switchToListView();
            
            // Filtrer les RDV pour ce jour spécifique
            filterRdvForDate(dateKey, date);
        });
        
        return dayElement;
    }
    
    // Afficher les consultations d'un jour sélectionné
    function showDayConsultations(dateKey, date) {
        const dayConsultations = consultationsIndex[dateKey] || [];
        
        // Titre
        const dateStr = date.toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        selectedDayTitle.innerHTML = `
            <i class="bi bi-clipboard-pulse me-2"></i>
            Consultations du ${dateStr} 
            <span class="badge bg-success ms-2">${dayConsultations.length} consultation${dayConsultations.length > 1 ? 's' : ''}</span>
        `;
        
        // Contenu
        if (dayConsultations.length === 0) {
            selectedDayContent.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-clipboard-x display-4 mb-3 text-secondary"></i>
                    <h6 class="text-muted">Aucune consultation</h6>
                    <p class="small mb-0">Pas de consultation enregistrée pour cette date.</p>
                </div>
            `;
        } else {
            // Trier les consultations par heure (extraire l'heure de date_consultation)
            const sortedConsultations = dayConsultations.sort((a, b) => {
                const timeA = new Date(a.date_consultation).toTimeString().slice(0, 5);
                const timeB = new Date(b.date_consultation).toTimeString().slice(0, 5);
                return timeA.localeCompare(timeB);
            });
            
            const consultationsHtml = sortedConsultations.map((consultation, index) => {
                const patientNom = consultation.patient?.nom || consultation.patient?.user?.name || '—';
                const patientPrenom = consultation.patient?.prenom || '';
                const patientId = consultation.patient?.id;
                const avatar = (patientNom && patientNom !== '—') ? patientNom.charAt(0).toUpperCase() : 'P';
                const isCompleted = ['terminé', 'termine', 'terminée', 'terminee', 'completed'].includes(consultation.statut?.toLowerCase());
                
                // Couleur de l'avatar basée sur le statut
                const avatarColor = isCompleted ? 'bg-success' : 'bg-info';
                
                // Extraire l'heure de la consultation
                const consultationTime = new Date(consultation.date_consultation).toTimeString().slice(0, 5);
                
                // Vérifier que patientId existe avant de créer les URLs
                const dossierUrl = patientId ? `/medecin/patients/${patientId}` : '#';
                const editConsultationUrl = consultation.id ? `/medecin/consultations/${consultation.id}/edit` : '#';
                const ordonnanceUrl = patientId ? `/medecin/ordonnances?patient_id=${patientId}` : '#';
                
                return `
                    <div class="selected-day-rdv-item ${isCompleted ? 'completed' : ''}">
                        <div class="rdv-time-indicator">
                            <div class="time-badge">${consultationTime}</div>
                            <div class="time-line ${index === sortedConsultations.length - 1 ? 'last' : ''}"></div>
                        </div>
                        <div class="selected-day-patient">
                            <div class="selected-day-avatar ${avatarColor}">${avatar}</div>
                            <div class="selected-day-info">
                                <h6 class="patient-name-cal">
                                    ${patientNom} ${patientPrenom}
                                    ${isCompleted ? '<i class="bi bi-check-circle-fill text-success ms-1" title="Consultation terminée"></i>' : ''}
                                </h6>
                                <p class="rdv-details">
                                    <span class="badge ${isCompleted ? 'bg-success' : 'bg-info text-white'} me-2">
                                        ${consultation.statut || 'En cours'}
                                    </span>
                                    ${consultation.symptomes ? `<small class="text-muted">${consultation.symptomes.substring(0, 30)}...</small>` : '<small class="text-muted">Consultation</small>'}
                                </p>
                            </div>
                        </div>
                        <div class="selected-day-actions">
                            <a href="${dossierUrl}" class="btn-action btn-primary-action ${!patientId ? 'disabled' : ''}" title="Voir dossier patient" ${!patientId ? 'onclick="return false;"' : ''}>
                                <i class="bi bi-folder2-open"></i>
                            </a>
                            <a href="${editConsultationUrl}" class="btn-action btn-success-action ${!consultation.id ? 'disabled' : ''}" title="Modifier consultation" ${!consultation.id ? 'onclick="return false;"' : ''}>
                                <i class="bi bi-clipboard-check"></i>
                            </a>
                            <a href="${ordonnanceUrl}" class="btn-action btn-warning-action ${!patientId ? 'disabled' : ''}" title="Nouvelle ordonnance" ${!patientId ? 'onclick="return false;"' : ''}>
                                <i class="bi bi-prescription2"></i>
                            </a>
                        </div>
                    </div>
                `;
            }).join('');
            
            selectedDayContent.innerHTML = consultationsHtml;
        }
        
        // Afficher le panel
        selectedDayPanel.style.display = 'block';
    }
    
    // Gestionnaires d'événements
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    
    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
    
    closeDayPanel.addEventListener('click', () => {
        selectedDayPanel.style.display = 'none';
        document.querySelectorAll('.calendar-day.selected').forEach(el => {
            el.classList.remove('selected');
        });
        selectedDate = null;
    });
    
    // Filtrer les RDV pour une date spécifique
    function filterRdvForDate(dateKey, date) {
        const rdvList = document.getElementById('rdv-upcoming-list');
        if (!rdvList) return;
        
        // Formater la date pour comparaison
        const targetDateStr = formatDateKey(date);
        
        // Afficher/masquer les RDV selon la date
        rdvList.querySelectorAll('li[data-date]').forEach(li => {
            const rdvDate = li.getAttribute('data-date');
            if (rdvDate === targetDateStr) {
                li.style.display = '';
            } else {
                li.style.display = 'none';
            }
        });
        
        // Mettre à jour les boutons de filtre pour indiquer qu'on filtre par date
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Ajouter un indicateur visuel pour la date sélectionnée
        const dateStr = date.toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long' 
        });
        
        // Créer ou mettre à jour un badge informatif
        let dateBadge = document.getElementById('selected-date-badge');
        if (!dateBadge) {
            dateBadge = document.createElement('span');
            dateBadge.id = 'selected-date-badge';
            dateBadge.className = 'badge bg-primary ms-2';
            const listControls = document.getElementById('list-controls');
            if (listControls) {
                listControls.appendChild(dateBadge);
            }
        }
        dateBadge.textContent = `Filtré: ${dateStr}`;
        
        // Trier par heure les RDV visibles
        sortRdvListAsc();
    }
    
    // Initialisation
    renderCalendar();
    
    // === COMMUTATEUR DE VUE ===
    
    const calendarViewBtn = document.getElementById('calendar-view-btn');
    const listViewBtn = document.getElementById('list-view-btn');
    const calendarView = document.getElementById('calendar-view');
    const listView = document.getElementById('list-view');
    const calendarControls = document.getElementById('calendar-controls');
    const listControls = document.getElementById('list-controls');
    
    function switchToCalendarView() {
        // Mettre à jour les boutons
        calendarViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        
        // Afficher/cacher les vues
        calendarView.style.display = 'block';
        listView.style.display = 'none';
        
        // Afficher/cacher les contrôles
        calendarControls.style.display = 'flex';
        listControls.style.display = 'none';
        
        // Fermer le panel du jour sélectionné si ouvert
        selectedDayPanel.style.display = 'none';
        document.querySelectorAll('.calendar-day.selected').forEach(el => {
            el.classList.remove('selected');
        });
        selectedDate = null;
        
        // Sauvegarder la préférence
        localStorage.setItem('medecin_dashboard_view', 'calendar');
    }
    
    function switchToListView() {
        // Mettre à jour les boutons
        listViewBtn.classList.add('active');
        calendarViewBtn.classList.remove('active');
        
        // Afficher/cacher les vues
        listView.style.display = 'block';
        calendarView.style.display = 'none';
        
        // Afficher/cacher les contrôles
        listControls.style.display = 'flex';
        calendarControls.style.display = 'none';
        
        // Sauvegarder la préférence
        localStorage.setItem('medecin_dashboard_view', 'list');
    }
    
    // Gestionnaires d'événements pour le commutateur
    calendarViewBtn.addEventListener('click', switchToCalendarView);
    listViewBtn.addEventListener('click', switchToListView);
    
    // Restaurer la vue préférée au chargement
    const savedView = localStorage.getItem('medecin_dashboard_view');
    if (savedView === 'list') {
        switchToListView();
    } else {
        switchToCalendarView(); // Vue par défaut
    }

  })();
</script>
@endsection

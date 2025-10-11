@extends('layouts.app')

@section('content')
{{-- Header moderne pour rapports de paiement --}}
<div class="payments-reports-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-file-earmark-bar-graph"></i>
      <div>
        <span>Rapports et Analyses de Paiements</span>
        <small>Suivi détaillé des transactions et performances financières</small>
      </div>
    </div>
    <div class="header-actions">
      <div class="date-range-picker">
        <input type="date" id="startDate" class="form-control">
        <span>à</span>
        <input type="date" id="endDate" class="form-control">
        <button class="btn-apply-range" onclick="applyDateRange()">
          <i class="bi bi-search"></i>
        </button>
      </div>
      <button class="btn-export-all" onclick="exportAllReports()">
        <i class="bi bi-download"></i>
        Export complet
      </button>
      <a href="{{ route('admin.payments.index') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour
      </a>
    </div>
  </div>
</div>

{{-- KPI financiers avancés --}}
<div class="financial-kpis">
  <div class="kpi-card revenue-kpi">
    <div class="kpi-header">
      <div class="kpi-icon">
        <i class="bi bi-cash-stack"></i>
      </div>
      <div class="kpi-menu">
        <button class="btn-kpi-menu" onclick="showKpiDetails('revenue')">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>
    </div>
    <div class="kpi-content">
      <div class="kpi-value">{{ number_format($financial_kpis['total_revenue'] ?? 2450000) }} XOF</div>
      <div class="kpi-label">Chiffre d'affaires total</div>
      <div class="kpi-period">Période sélectionnée</div>
      <div class="kpi-trend positive">
        <i class="bi bi-arrow-up"></i>
        +12.5% vs période précédente
      </div>
    </div>
    <div class="kpi-chart">
      <canvas id="revenueSparkline" width="100" height="40"></canvas>
    </div>
  </div>
  
  <div class="kpi-card transactions-kpi">
    <div class="kpi-header">
      <div class="kpi-icon">
        <i class="bi bi-receipt"></i>
      </div>
      <div class="kpi-menu">
        <button class="btn-kpi-menu" onclick="showKpiDetails('transactions')">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>
    </div>
    <div class="kpi-content">
      <div class="kpi-value">{{ $financial_kpis['total_transactions'] ?? 1247 }}</div>
      <div class="kpi-label">Transactions traitées</div>
      <div class="kpi-period">Ce mois</div>
      <div class="kpi-trend positive">
        <i class="bi bi-arrow-up"></i>
        +8.3% vs mois dernier
      </div>
    </div>
    <div class="kpi-chart">
      <canvas id="transactionsSparkline" width="100" height="40"></canvas>
    </div>
  </div>
  
  <div class="kpi-card average-kpi">
    <div class="kpi-header">
      <div class="kpi-icon">
        <i class="bi bi-calculator"></i>
      </div>
      <div class="kpi-menu">
        <button class="btn-kpi-menu" onclick="showKpiDetails('average')">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>
    </div>
    <div class="kpi-content">
      <div class="kpi-value">{{ number_format($financial_kpis['average_transaction'] ?? 19643) }} XOF</div>
      <div class="kpi-label">Montant moyen</div>
      <div class="kpi-period">Par transaction</div>
      <div class="kpi-trend positive">
        <i class="bi bi-arrow-up"></i>
        +5.7% vs moyenne
      </div>
    </div>
    <div class="kpi-chart">
      <canvas id="averageSparkline" width="100" height="40"></canvas>
    </div>
  </div>
  
  <div class="kpi-card growth-kpi">
    <div class="kpi-header">
      <div class="kpi-icon">
        <i class="bi bi-graph-up-arrow"></i>
      </div>
      <div class="kpi-menu">
        <button class="btn-kpi-menu" onclick="showKpiDetails('growth')">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>
    </div>
    <div class="kpi-content">
      <div class="kpi-value">+{{ $financial_kpis['growth_rate'] ?? 15.2 }}%</div>
      <div class="kpi-label">Taux de croissance</div>
      <div class="kpi-period">Mensuel</div>
      <div class="kpi-trend positive">
        <i class="bi bi-arrow-up"></i>
        Tendance haussière
      </div>
    </div>
    <div class="kpi-chart">
      <canvas id="growthSparkline" width="100" height="40"></canvas>
    </div>
  </div>
</div>

{{-- Navigation des rapports --}}
<div class="reports-navigation">
  <button class="nav-item active" data-report="overview" onclick="switchReport('overview')">
    <i class="bi bi-pie-chart"></i>
    Vue d'ensemble
  </button>
  <button class="nav-item" data-report="detailed" onclick="switchReport('detailed')">
    <i class="bi bi-table"></i>
    Détails des transactions
  </button>
  <button class="nav-item" data-report="analytics" onclick="switchReport('analytics')">
    <i class="bi bi-graph-up"></i>
    Analyses avancées
  </button>
  <button class="nav-item" data-report="doctors" onclick="switchReport('doctors')">
    <i class="bi bi-person-badge"></i>
    Performance médecins
  </button>
  <button class="nav-item" data-report="services" onclick="switchReport('services')">
    <i class="bi bi-hospital"></i>
    Services & Tarifs
  </button>
</div>

{{-- Contenu des rapports --}}
<div class="reports-content">
  {{-- Vue d'ensemble --}}
  <div id="overview-report" class="report-panel active">
    <div class="overview-grid">
      {{-- Graphique des revenus par période --}}
      <div class="chart-container revenue-evolution">
        <div class="chart-header">
          <h3><i class="bi bi-bar-chart-line"></i> Évolution des revenus</h3>
          <div class="chart-controls">
            <select class="form-select" id="revenueTimeframe">
              <option value="daily">Journalier</option>
              <option value="weekly" selected>Hebdomadaire</option>
              <option value="monthly">Mensuel</option>
              <option value="yearly">Annuel</option>
            </select>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="revenueEvolutionChart" height="300"></canvas>
        </div>
      </div>
      
      {{-- Répartition par type de service --}}
      <div class="chart-container services-breakdown">
        <div class="chart-header">
          <h3><i class="bi bi-pie-chart-fill"></i> Répartition par service</h3>
          <div class="chart-total">
            Total: {{ number_format($services_total ?? 2450000) }} XOF
          </div>
        </div>
        <div class="chart-body">
          <canvas id="servicesBreakdownChart" height="300"></canvas>
          <div class="chart-legend" id="servicesLegend"></div>
        </div>
      </div>
      
      {{-- Top médecins par revenus --}}
      <div class="ranking-container top-doctors">
        <div class="ranking-header">
          <h3><i class="bi bi-trophy"></i> Top médecins par revenus</h3>
          <span class="ranking-period">Cette période</span>
        </div>
        <div class="ranking-list">
          @foreach($top_doctors_revenue ?? [] as $index => $doctor)
            <div class="ranking-item">
              <div class="rank-position">
                @if($index < 3)
                  <i class="bi bi-trophy-fill rank-{{ $index + 1 }}"></i>
                @else
                  {{ $index + 1 }}
                @endif
              </div>
              <div class="doctor-info">
                <div class="doctor-avatar">{{ substr($doctor['name'], 0, 1) }}</div>
                <div class="doctor-details">
                  <span class="doctor-name">{{ $doctor['name'] }}</span>
                  <small class="doctor-specialty">{{ $doctor['specialty'] }}</small>
                </div>
              </div>
              <div class="doctor-revenue">
                <div class="revenue-amount">{{ number_format($doctor['revenue']) }} XOF</div>
                <div class="revenue-patients">{{ $doctor['patients'] }} patients</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      
      {{-- Métriques de performance --}}
      <div class="metrics-container performance-metrics">
        <div class="metrics-header">
          <h3><i class="bi bi-speedometer2"></i> Métriques de performance</h3>
        </div>
        <div class="metrics-grid">
          <div class="metric-item">
            <div class="metric-icon success">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="metric-content">
              <div class="metric-value">{{ $performance_metrics['success_rate'] ?? 98.5 }}%</div>
              <div class="metric-label">Taux de réussite</div>
            </div>
          </div>
          
          <div class="metric-item">
            <div class="metric-icon time">
              <i class="bi bi-clock"></i>
            </div>
            <div class="metric-content">
              <div class="metric-value">{{ $performance_metrics['avg_processing_time'] ?? 2.3 }}s</div>
              <div class="metric-label">Temps moyen</div>
            </div>
          </div>
          
          <div class="metric-item">
            <div class="metric-icon refunds">
              <i class="bi bi-arrow-counterclockwise"></i>
            </div>
            <div class="metric-content">
              <div class="metric-value">{{ $performance_metrics['refund_rate'] ?? 0.8 }}%</div>
              <div class="metric-label">Taux de remboursement</div>
            </div>
          </div>
          
          <div class="metric-item">
            <div class="metric-icon satisfaction">
              <i class="bi bi-star"></i>
            </div>
            <div class="metric-content">
              <div class="metric-value">{{ $performance_metrics['satisfaction'] ?? 4.7 }}/5</div>
              <div class="metric-label">Satisfaction</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  {{-- Détails des transactions --}}
  <div id="detailed-report" class="report-panel">
    <div class="transactions-section">
      <div class="section-header">
        <h2>Détails des transactions</h2>
        <div class="section-actions">
          <div class="search-transactions">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Rechercher une transaction..." id="transactionSearch">
          </div>
          <select class="form-select" id="statusFilter">
            <option value="">Tous les statuts</option>
            <option value="completed">Complétée</option>
            <option value="pending">En attente</option>
            <option value="failed">Échouée</option>
            <option value="refunded">Remboursée</option>
          </select>
          <button class="btn-export-transactions" onclick="exportTransactions()">
            <i class="bi bi-download"></i>
            Exporter
          </button>
        </div>
      </div>
      
      <div class="transactions-table-container">
        <table class="transactions-table">
          <thead>
            <tr>
              <th>
                <input type="checkbox" id="selectAllTransactions">
              </th>
              <th>ID Transaction</th>
              <th>Date & Heure</th>
              <th>Patient</th>
              <th>Médecin</th>
              <th>Service</th>
              <th>Montant</th>
              <th>Mode paiement</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions ?? [] as $transaction)
              <tr class="transaction-row">
                <td>
                  <input type="checkbox" class="transaction-checkbox" value="{{ $transaction['id'] }}">
                </td>
                <td class="transaction-id">
                  <span class="id-badge">#{{ $transaction['id'] }}</span>
                </td>
                <td class="transaction-date">
                  <div class="date-time">
                    <span class="date">{{ $transaction['date'] }}</span>
                    <span class="time">{{ $transaction['time'] }}</span>
                  </div>
                </td>
                <td class="patient-info">
                  <div class="patient-details">
                    <span class="patient-name">{{ $transaction['patient'] }}</span>
                    <small class="patient-id">ID: {{ $transaction['patient_id'] }}</small>
                  </div>
                </td>
                <td class="doctor-info">
                  <div class="doctor-details">
                    <span class="doctor-name">{{ $transaction['doctor'] }}</span>
                    <small class="doctor-specialty">{{ $transaction['doctor_specialty'] }}</small>
                  </div>
                </td>
                <td class="service-info">
                  <span class="service-name">{{ $transaction['service'] }}</span>
                </td>
                <td class="transaction-amount">
                  <span class="amount">{{ number_format($transaction['amount']) }} XOF</span>
                </td>
                <td class="payment-method">
                  <span class="method-badge {{ $transaction['method'] }}">
                    @switch($transaction['method'])
                      @case('cash')
                        <i class="bi bi-cash"></i> Espèces
                        @break
                      @case('card')
                        <i class="bi bi-credit-card"></i> Carte
                        @break
                      @case('mobile')
                        <i class="bi bi-phone"></i> Mobile
                        @break
                    @endswitch
                  </span>
                </td>
                <td class="transaction-status">
                  <span class="status-badge {{ $transaction['status'] }}">
                    {{ ucfirst($transaction['status']) }}
                  </span>
                </td>
                <td class="transaction-actions">
                  <div class="action-buttons">
                    <button class="btn-view" onclick="viewTransaction('{{ $transaction['id'] }}')">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn-print" onclick="printReceipt('{{ $transaction['id'] }}')">
                      <i class="bi bi-printer"></i>
                    </button>
                    @if($transaction['status'] === 'completed')
                      <button class="btn-refund" onclick="refundTransaction('{{ $transaction['id'] }}')">
                        <i class="bi bi-arrow-counterclockwise"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="table-pagination">
        <div class="pagination-info">
          Affichage de 1 à 20 sur 1247 transactions
        </div>
        <div class="pagination-controls">
          <button class="btn-page" disabled>
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="btn-page active">1</button>
          <button class="btn-page">2</button>
          <button class="btn-page">3</button>
          <span>...</span>
          <button class="btn-page">63</button>
          <button class="btn-page">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  
  {{-- Analyses avancées --}}
  <div id="analytics-report" class="report-panel">
    <div class="analytics-dashboard">
      <div class="analytics-grid">
        {{-- Analyse des tendances --}}
        <div class="analytics-card trends-analysis">
          <div class="card-header">
            <h3><i class="bi bi-graph-up"></i> Analyse des tendances</h3>
            <button class="btn-card-options" onclick="configureTrends()">
              <i class="bi bi-gear"></i>
            </button>
          </div>
          <div class="card-body">
            <div class="trends-metrics">
              <div class="trend-item">
                <span class="trend-label">Croissance mensuelle</span>
                <span class="trend-value positive">+15.2%</span>
              </div>
              <div class="trend-item">
                <span class="trend-label">Prévision prochain mois</span>
                <span class="trend-value positive">+18.7%</span>
              </div>
              <div class="trend-item">
                <span class="trend-label">Saisonnalité</span>
                <span class="trend-value neutral">Stable</span>
              </div>
            </div>
            <div class="trends-chart">
              <canvas id="trendsAnalysisChart" height="200"></canvas>
            </div>
          </div>
        </div>
        
        {{-- Analyse de cohort --}}
        <div class="analytics-card cohort-analysis">
          <div class="card-header">
            <h3><i class="bi bi-people"></i> Analyse de cohorte</h3>
            <select class="form-select" id="cohortPeriod">
              <option value="monthly">Mensuelle</option>
              <option value="quarterly">Trimestrielle</option>
              <option value="yearly">Annuelle</option>
            </select>
          </div>
          <div class="card-body">
            <div class="cohort-heatmap" id="cohortHeatmap">
              <!-- Heatmap générée dynamiquement -->
            </div>
          </div>
        </div>
        
        {{-- Segmentation des patients --}}
        <div class="analytics-card patient-segmentation">
          <div class="card-header">
            <h3><i class="bi bi-diagram-3"></i> Segmentation patients</h3>
          </div>
          <div class="card-body">
            <div class="segments-list">
              <div class="segment-item">
                <div class="segment-color" style="background: #059669;"></div>
                <div class="segment-info">
                  <span class="segment-name">Patients réguliers</span>
                  <span class="segment-percentage">45%</span>
                </div>
                <div class="segment-value">1,125,000 XOF</div>
              </div>
              <div class="segment-item">
                <div class="segment-color" style="background: #3b82f6;"></div>
                <div class="segment-info">
                  <span class="segment-name">Nouveaux patients</span>
                  <span class="segment-percentage">32%</span>
                </div>
                <div class="segment-value">784,000 XOF</div>
              </div>
              <div class="segment-item">
                <div class="segment-color" style="background: #f59e0b;"></div>
                <div class="segment-info">
                  <span class="segment-name">Patients VIP</span>
                  <span class="segment-percentage">18%</span>
                </div>
                <div class="segment-value">441,000 XOF</div>
              </div>
              <div class="segment-item">
                <div class="segment-color" style="background: #8b5cf6;"></div>
                <div class="segment-info">
                  <span class="segment-name">Patients occasionnels</span>
                  <span class="segment-percentage">5%</span>
                </div>
                <div class="segment-value">100,000 XOF</div>
              </div>
            </div>
          </div>
        </div>
        
        {{-- Analyse prédictive --}}
        <div class="analytics-card predictive-analysis">
          <div class="card-header">
            <h3><i class="bi bi-crystal-ball"></i> Analyse prédictive</h3>
          </div>
          <div class="card-body">
            <div class="predictions-list">
              <div class="prediction-item">
                <div class="prediction-icon success">
                  <i class="bi bi-arrow-up"></i>
                </div>
                <div class="prediction-content">
                  <span class="prediction-title">Revenus du prochain mois</span>
                  <span class="prediction-value">2,900,000 XOF</span>
                  <small class="prediction-confidence">Confiance: 87%</small>
                </div>
              </div>
              <div class="prediction-item">
                <div class="prediction-icon warning">
                  <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="prediction-content">
                  <span class="prediction-title">Pic d'affluence prévu</span>
                  <span class="prediction-value">Semaine du 15/01</span>
                  <small class="prediction-confidence">Confiance: 92%</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  {{-- Performance médecins --}}
  <div id="doctors-report" class="report-panel">
    <div class="doctors-performance-section">
      <div class="section-header">
        <h2>Performance des médecins</h2>
        <div class="section-controls">
          <select class="form-select" id="doctorMetric">
            <option value="revenue">Revenus générés</option>
            <option value="patients">Nombre de patients</option>
            <option value="satisfaction">Satisfaction client</option>
            <option value="efficiency">Efficacité</option>
          </select>
        </div>
      </div>
      
      <div class="doctors-grid">
        @foreach($doctors_performance ?? [] as $doctor)
          <div class="doctor-performance-card">
            <div class="doctor-header">
              <div class="doctor-avatar-large">{{ substr($doctor['name'], 0, 1) }}</div>
              <div class="doctor-basic-info">
                <h4>{{ $doctor['name'] }}</h4>
                <span class="doctor-specialty">{{ $doctor['specialty'] }}</span>
                <div class="doctor-rating">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $doctor['rating'] ? 'bi-star-fill' : 'bi-star' }}"></i>
                  @endfor
                  <span>({{ $doctor['rating'] }}/5)</span>
                </div>
              </div>
              <div class="doctor-status {{ $doctor['status'] }}">
                <i class="bi bi-circle-fill"></i>
              </div>
            </div>
            
            <div class="doctor-metrics">
              <div class="metric-row">
                <span class="metric-label">Revenus générés</span>
                <span class="metric-value">{{ number_format($doctor['revenue']) }} XOF</span>
              </div>
              <div class="metric-row">
                <span class="metric-label">Patients traités</span>
                <span class="metric-value">{{ $doctor['patients'] }}</span>
              </div>
              <div class="metric-row">
                <span class="metric-label">Taux de retour</span>
                <span class="metric-value">{{ $doctor['return_rate'] }}%</span>
              </div>
              <div class="metric-row">
                <span class="metric-label">Temps moyen consultation</span>
                <span class="metric-value">{{ $doctor['avg_time'] }} min</span>
              </div>
            </div>
            
            <div class="doctor-chart">
              <canvas id="doctorChart{{ $doctor['id'] }}" height="100"></canvas>
            </div>
            
            <div class="doctor-actions">
              <button class="btn-doctor-details" onclick="viewDoctorDetails({{ $doctor['id'] }})">
                <i class="bi bi-eye"></i>
                Détails
              </button>
              <button class="btn-doctor-report" onclick="generateDoctorReport({{ $doctor['id'] }})">
                <i class="bi bi-file-text"></i>
                Rapport
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  
  {{-- Services & Tarifs --}}
  <div id="services-report" class="report-panel">
    <div class="services-analysis-section">
      <div class="section-header">
        <h2>Analyse des services et tarifs</h2>
        <button class="btn-optimize-pricing" onclick="optimizePricing()">
          <i class="bi bi-lightning"></i>
          Optimiser les tarifs
        </button>
      </div>
      
      <div class="services-analytics">
        <div class="services-overview">
          <div class="overview-chart">
            <canvas id="servicesRevenueChart" height="300"></canvas>
          </div>
          <div class="services-insights">
            <h3>Insights clés</h3>
            <div class="insight-item">
              <i class="bi bi-lightbulb text-warning"></i>
              <span>Les consultations spécialisées génèrent 65% des revenus</span>
            </div>
            <div class="insight-item">
              <i class="bi bi-trend-up text-success"></i>
              <span>Hausse de 23% des analyses de laboratoire</span>
            </div>
            <div class="insight-item">
              <i class="bi bi-exclamation-triangle text-danger"></i>
              <span>Baisse des consultations d'urgence (-12%)</span>
            </div>
          </div>
        </div>
        
        <div class="pricing-optimization">
          <h3>Recommandations tarifaires</h3>
          <div class="optimization-suggestions">
            <div class="suggestion-item">
              <div class="suggestion-service">Consultation générale</div>
              <div class="suggestion-current">Actuel: 5,000 XOF</div>
              <div class="suggestion-recommended">Recommandé: 5,500 XOF</div>
              <div class="suggestion-impact">+10% revenus estimés</div>
              <button class="btn-apply-suggestion" onclick="applySuggestion('consultation')">
                Appliquer
              </button>
            </div>
            <div class="suggestion-item">
              <div class="suggestion-service">Analyse sanguine</div>
              <div class="suggestion-current">Actuel: 10,000 XOF</div>
              <div class="suggestion-recommended">Recommandé: 9,500 XOF</div>
              <div class="suggestion-impact">+15% volume estimé</div>
              <button class="btn-apply-suggestion" onclick="applySuggestion('analyse')">
                Appliquer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Variables CSS */
  :root {
    --primary-color: #3b82f6;
    --success-color: #059669;
    --warning-color: #f59e0b;
    --danger-color: #dc2626;
    --purple-color: #8b5cf6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-800: #1f2937;
    --gray-900: #111827;
  }
  
  /* Conteneur principal */
  body > .container { max-width: 1800px !important; }
  
  /* Header rapports paiements */
  .payments-reports-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.75rem;
    border-radius: 12px;
    font-size: 1.5rem;
  }
  
  .header-title span {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  
  .header-title small {
    font-size: 1rem;
    opacity: 0.9;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .date-range-picker {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem;
    border-radius: 10px;
  }
  
  .date-range-picker input {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 6px;
    padding: 0.5rem;
    font-size: 0.9rem;
  }
  
  .date-range-picker span {
    color: white;
    font-size: 0.9rem;
  }
  
  .btn-apply-range {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  
  .btn-apply-range:hover {
    background: rgba(255, 255, 255, 0.3);
  }
  
  .btn-export-all, .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-export-all:hover, .action-btn:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-2px);
  }
  
  /* KPI financiers */
  .financial-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .kpi-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }
  
  .kpi-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
  }
  
  .revenue-kpi .kpi-icon { background: linear-gradient(135deg, var(--success-color), #047857); }
  .transactions-kpi .kpi-icon { background: linear-gradient(135deg, var(--primary-color), #1d4ed8); }
  .average-kpi .kpi-icon { background: linear-gradient(135deg, var(--purple-color), #7c3aed); }
  .growth-kpi .kpi-icon { background: linear-gradient(135deg, var(--warning-color), #d97706); }
  
  .btn-kpi-menu {
    background: none;
    border: none;
    color: var(--gray-600);
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .btn-kpi-menu:hover {
    background: var(--gray-100);
  }
  
  .kpi-content {
    margin-bottom: 1rem;
  }
  
  .kpi-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  
  .kpi-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
  }
  
  .kpi-period {
    font-size: 0.8rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem;
  }
  
  .kpi-trend {
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .kpi-trend.positive { color: var(--success-color); }
  .kpi-trend.negative { color: var(--danger-color); }
  
  .kpi-chart {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    opacity: 0.6;
  }
  
  /* Navigation des rapports */
  .reports-navigation {
    background: white;
    border-radius: 16px;
    padding: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
  }
  
  .nav-item {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    white-space: nowrap;
  }
  
  .nav-item:hover {
    background: var(--gray-100);
    color: var(--gray-800);
  }
  
  .nav-item.active {
    background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  /* Contenu des rapports */
  .reports-content {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  }
  
  .report-panel {
    display: none;
  }
  
  .report-panel.active {
    display: block;
  }
  
  /* Vue d'ensemble */
  .overview-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: auto auto;
    gap: 2rem;
  }
  
  .chart-container {
    background: var(--gray-50);
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }
  
  .revenue-evolution {
    grid-column: 1;
    grid-row: 1;
  }
  
  .services-breakdown {
    grid-column: 2;
    grid-row: 1;
  }
  
  .top-doctors {
    grid-column: 1;
    grid-row: 2;
  }
  
  .performance-metrics {
    grid-column: 2;
    grid-row: 2;
  }
  
  .chart-header {
    background: white;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .chart-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .chart-body {
    padding: 1.5rem;
    position: relative;
  }
  
  /* Classement des médecins */
  .ranking-container {
    background: var(--gray-50);
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }
  
  .ranking-header {
    background: white;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .ranking-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .ranking-list {
    padding: 1rem 0;
  }
  
  .ranking-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    transition: all 0.2s ease;
  }
  
  .ranking-item:hover {
    background: white;
  }
  
  .rank-position {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    background: var(--gray-200);
    color: var(--gray-600);
  }
  
  .rank-1 { color: #ffd700 !important; }
  .rank-2 { color: #c0c0c0 !important; }
  .rank-3 { color: #cd7f32 !important; }
  
  .doctor-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
  }
  
  .doctor-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--primary-color), var(--purple-color));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
  }
  
  .doctor-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .doctor-name {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .doctor-specialty {
    color: var(--gray-600);
    font-size: 0.85rem;
  }
  
  .doctor-revenue {
    text-align: right;
  }
  
  .revenue-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--success-color);
  }
  
  .revenue-patients {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  /* Métriques de performance */
  .metrics-container {
    background: var(--gray-50);
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }
  
  .metrics-header {
    background: white;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
  }
  
  .metrics-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .metrics-grid {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .metric-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
  }
  
  .metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .metric-icon.success { background: var(--success-color); }
  .metric-icon.time { background: var(--primary-color); }
  .metric-icon.refunds { background: var(--warning-color); }
  .metric-icon.satisfaction { background: var(--purple-color); }
  
  .metric-content {
    flex: 1;
  }
  
  .metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
  }
  
  .metric-label {
    font-size: 0.8rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  /* Table des transactions */
  .transactions-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
  }
  
  .section-header h2 {
    margin: 0;
    color: var(--gray-800);
    font-weight: 700;
  }
  
  .section-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .search-transactions {
    position: relative;
  }
  
  .search-transactions i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-600);
  }
  
  .search-transactions input {
    padding-left: 2.5rem;
    border-radius: 10px;
    border: 2px solid var(--gray-200);
    min-width: 250px;
  }
  
  .btn-export-transactions {
    background: var(--success-color);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-export-transactions:hover {
    background: #047857;
    transform: translateY(-1px);
  }
  
  .transactions-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .transactions-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .transactions-table thead {
    background: var(--gray-100);
  }
  
  .transactions-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--gray-800);
    border-bottom: 2px solid var(--gray-200);
  }
  
  .transactions-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
  }
  
  .transaction-row:hover {
    background: var(--gray-50);
  }
  
  .id-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  .date-time {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .date {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .time {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  .patient-details, .doctor-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .patient-name, .doctor-name {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .patient-id, .doctor-specialty {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  .amount {
    font-weight: 700;
    color: var(--success-color);
  }
  
  .method-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .method-badge.cash {
    background: #dcfce7;
    color: var(--success-color);
  }
  
  .method-badge.card {
    background: #dbeafe;
    color: var(--primary-color);
  }
  
  .method-badge.mobile {
    background: #f3e8ff;
    color: var(--purple-color);
  }
  
  .status-badge.completed {
    background: #dcfce7;
    color: var(--success-color);
  }
  
  .status-badge.pending {
    background: #fef3c7;
    color: var(--warning-color);
  }
  
  .status-badge.failed {
    background: #fee2e2;
    color: var(--danger-color);
  }
  
  .status-badge.refunded {
    background: var(--gray-100);
    color: var(--gray-600);
  }
  
  .action-buttons {
    display: flex;
    gap: 0.25rem;
  }
  
  .btn-view, .btn-print, .btn-refund {
    background: none;
    border: none;
    color: var(--gray-600);
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .btn-view:hover {
    background: #eff6ff;
    color: var(--primary-color);
  }
  
  .btn-print:hover {
    background: var(--gray-100);
    color: var(--gray-800);
  }
  
  .btn-refund:hover {
    background: #fee2e2;
    color: var(--danger-color);
  }
  
  /* Pagination */
  .table-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-top: 1px solid var(--gray-200);
  }
  
  .pagination-info {
    color: var(--gray-600);
    font-size: 0.9rem;
  }
  
  .pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-page {
    background: none;
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    min-width: 40px;
  }
  
  .btn-page:hover:not(:disabled) {
    background: var(--gray-100);
    color: var(--gray-800);
  }
  
  .btn-page.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
  }
  
  .btn-page:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  /* Analytics Dashboard */
  .analytics-dashboard {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
  }
  
  .analytics-card {
    background: var(--gray-50);
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }
  
  .analytics-card .card-header {
    background: white;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .analytics-card .card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .analytics-card .card-body {
    padding: 1.5rem;
  }
  
  .trends-metrics {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }
  
  .trend-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
  }
  
  .trend-label {
    color: var(--gray-600);
    font-size: 0.9rem;
  }
  
  .trend-value {
    font-weight: 600;
  }
  
  .trend-value.positive { color: var(--success-color); }
  .trend-value.negative { color: var(--danger-color); }
  .trend-value.neutral { color: var(--gray-600); }
  
  /* Segmentation des patients */
  .segments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .segment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
  }
  
  .segment-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
  }
  
  .segment-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .segment-name {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .segment-percentage {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  .segment-value {
    font-weight: 700;
    color: var(--success-color);
  }
  
  /* Analyse prédictive */
  .predictions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .prediction-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
  }
  
  .prediction-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .prediction-icon.success { background: var(--success-color); }
  .prediction-icon.warning { background: var(--warning-color); }
  
  .prediction-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .prediction-title {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .prediction-value {
    font-weight: 700;
    color: var(--primary-color);
  }
  
  .prediction-confidence {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  /* Performance des médecins */
  .doctors-performance-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .doctors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
  }
  
  .doctor-performance-card {
    background: var(--gray-50);
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .doctor-performance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  
  .doctor-header {
    background: white;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .doctor-avatar-large {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--purple-color));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
  }
  
  .doctor-basic-info {
    flex: 1;
  }
  
  .doctor-basic-info h4 {
    margin: 0 0 0.5rem 0;
    color: var(--gray-800);
    font-weight: 600;
  }
  
  .doctor-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }
  
  .doctor-rating i {
    color: var(--warning-color);
    font-size: 0.8rem;
  }
  
  .doctor-rating span {
    font-size: 0.8rem;
    color: var(--gray-600);
  }
  
  .doctor-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
  }
  
  .doctor-status.active { color: var(--success-color); }
  .doctor-status.inactive { color: var(--danger-color); }
  
  .doctor-metrics {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
  }
  
  .metric-label {
    color: var(--gray-600);
    font-size: 0.9rem;
  }
  
  .metric-value {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .doctor-chart {
    padding: 0 1.5rem;
    margin-bottom: 1rem;
  }
  
  .doctor-actions {
    padding: 1rem 1.5rem;
    background: white;
    border-top: 1px solid var(--gray-200);
    display: flex;
    gap: 1rem;
  }
  
  .btn-doctor-details, .btn-doctor-report {
    flex: 1;
    background: none;
    border: 2px solid var(--gray-200);
    color: var(--gray-600);
    padding: 0.6rem;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-doctor-details:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: #eff6ff;
  }
  
  .btn-doctor-report:hover {
    border-color: var(--success-color);
    color: var(--success-color);
    background: #dcfce7;
  }
  
  /* Services et tarifs */
  .services-analysis-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .btn-optimize-pricing {
    background: linear-gradient(135deg, var(--warning-color), #d97706);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-optimize-pricing:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
  }
  
  .services-analytics {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .services-overview {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
  }
  
  .overview-chart {
    background: var(--gray-50);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
  }
  
  .services-insights {
    background: var(--gray-50);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
  }
  
  .services-insights h3 {
    margin: 0 0 1rem 0;
    color: var(--gray-800);
    font-weight: 600;
  }
  
  .insight-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  
  .pricing-optimization {
    background: var(--gray-50);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
  }
  
  .pricing-optimization h3 {
    margin: 0 0 1rem 0;
    color: var(--gray-800);
    font-weight: 600;
  }
  
  .optimization-suggestions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .suggestion-item {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
  }
  
  .suggestion-service {
    font-weight: 600;
    color: var(--gray-800);
  }
  
  .suggestion-current {
    color: var(--gray-600);
    font-size: 0.9rem;
  }
  
  .suggestion-recommended {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  .suggestion-impact {
    color: var(--success-color);
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  .btn-apply-suggestion {
    background: var(--success-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .btn-apply-suggestion:hover {
    background: #047857;
    transform: translateY(-1px);
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .overview-grid {
      grid-template-columns: 1fr;
      grid-template-rows: auto;
    }
    
    .revenue-evolution,
    .services-breakdown,
    .top-doctors,
    .performance-metrics {
      grid-column: 1;
      grid-row: auto;
    }
    
    .services-overview {
      grid-template-columns: 1fr;
    }
  }
  
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }
    
    .header-actions {
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .financial-kpis {
      grid-template-columns: 1fr;
    }
    
    .reports-navigation {
      flex-direction: column;
    }
    
    .section-header {
      flex-direction: column;
      align-items: start;
      gap: 1rem;
    }
    
    .section-actions {
      width: 100%;
      justify-content: space-between;
    }
    
    .transactions-table-container {
      overflow-x: auto;
    }
    
    .analytics-grid {
      grid-template-columns: 1fr;
    }
    
    .doctors-grid {
      grid-template-columns: 1fr;
    }
    
    .suggestion-item {
      grid-template-columns: 1fr;
      text-align: center;
      gap: 0.5rem;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Variables globales
  let currentReport = 'overview';
  let charts = {};
  
  // Initialisation des graphiques sparkline
  function initKpiCharts() {
    // Graphique sparkline revenus
    const revenueCtx = document.getElementById('revenueSparkline')?.getContext('2d');
    if(revenueCtx) {
      charts.revenueSparkline = new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: ['', '', '', '', '', '', ''],
          datasets: [{
            data: [100, 120, 110, 140, 130, 160, 150],
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { display: false },
            y: { display: false }
          }
        }
      });
    }
    
    // Répéter pour les autres sparklines...
  }
  
  // Initialisation du graphique d'évolution des revenus
  function initRevenueEvolutionChart() {
    const ctx = document.getElementById('revenueEvolutionChart')?.getContext('2d');
    if(ctx) {
      charts.revenueEvolution = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
          datasets: [{
            label: 'Revenus (XOF)',
            data: [120000, 190000, 300000, 250000, 320000, 380000, 420000],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                }
              }
            }
          }
        }
      });
    }
  }
  
  // Initialisation du graphique de répartition des services
  function initServicesBreakdownChart() {
    const ctx = document.getElementById('servicesBreakdownChart')?.getContext('2d');
    if(ctx) {
      const data = {
        labels: ['Consultations', 'Analyses', 'Urgences', 'Spécialisées'],
        datasets: [{
          data: [45, 25, 15, 15],
          backgroundColor: ['#3b82f6', '#059669', '#f59e0b', '#8b5cf6'],
          borderWidth: 0
        }]
      };
      
      charts.servicesBreakdown = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          }
        }
      });
      
      // Génération de la légende
      generateChartLegend('servicesLegend', data);
    }
  }
  
  // Génération de légende personnalisée
  function generateChartLegend(containerId, chartData) {
    const container = document.getElementById(containerId);
    if(container && chartData) {
      container.innerHTML = '';
      chartData.labels.forEach((label, index) => {
        const legendItem = document.createElement('div');
        legendItem.style.display = 'flex';
        legendItem.style.alignItems = 'center';
        legendItem.style.gap = '0.5rem';
        legendItem.style.marginBottom = '0.5rem';
        
        legendItem.innerHTML = `
          <div style="width: 12px; height: 12px; background: ${chartData.datasets[0].backgroundColor[index]}; border-radius: 50%;"></div>
          <span style="font-size: 0.9rem; color: #4b5563;">${label}</span>
        `;
        container.appendChild(legendItem);
      });
    }
  }
  
  // Gestion des rapports
  function switchReport(reportName) {
    // Mise à jour des boutons de navigation
    document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-report="${reportName}"]`)?.classList.add('active');
    
    // Mise à jour des panneaux
    document.querySelectorAll('.report-panel').forEach(panel => panel.classList.remove('active'));
    document.getElementById(`${reportName}-report`)?.classList.add('active');
    
    currentReport = reportName;
  }
  
  // Fonctions utilitaires
  function applyDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if(startDate && endDate) {
      console.log(`Filtrage des données du ${startDate} au ${endDate}`);
      // Ici on rechargerait les données selon la période
    }
  }
  
  function exportAllReports() {
    alert('Export de tous les rapports en cours...');
  }
  
  function showKpiDetails(kpiType) {
    alert(`Affichage des détails pour ${kpiType}`);
  }
  
  function exportTransactions() {
    alert('Export des transactions en cours...');
  }
  
  function viewTransaction(id) {
    alert(`Affichage des détails de la transaction ${id}`);
  }
  
  function printReceipt(id) {
    alert(`Impression du reçu pour la transaction ${id}`);
  }
  
  function refundTransaction(id) {
    if(confirm(`Confirmer le remboursement de la transaction ${id} ?`)) {
      alert(`Transaction ${id} remboursée`);
    }
  }
  
  function configureTrends() {
    alert('Configuration de l\'analyse des tendances');
  }
  
  function viewDoctorDetails(doctorId) {
    alert(`Affichage des détails du médecin ${doctorId}`);
  }
  
  function generateDoctorReport(doctorId) {
    alert(`Génération du rapport pour le médecin ${doctorId}`);
  }
  
  function optimizePricing() {
    alert('Lancement de l\'optimisation automatique des tarifs...');
  }
  
  function applySuggestion(serviceType) {
    if(confirm(`Appliquer la suggestion tarifaire pour ${serviceType} ?`)) {
      alert(`Tarif mis à jour pour ${serviceType}`);
    }
  }
  
  // Gestion de la recherche de transactions
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('transactionSearch');
    const statusFilter = document.getElementById('statusFilter');
    
    if(searchInput) {
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.transaction-row');
        
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
      });
    }
    
    if(statusFilter) {
      statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value;
        const rows = document.querySelectorAll('.transaction-row');
        
        rows.forEach(row => {
          const statusBadge = row.querySelector('.status-badge');
          if(selectedStatus === '' || statusBadge?.textContent.toLowerCase().includes(selectedStatus)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
    
    // Initialisation des graphiques
    initKpiCharts();
    initRevenueEvolutionChart();
    initServicesBreakdownChart();
    
    // Gestion du sélecteur de période pour les revenus
    const revenueTimeframe = document.getElementById('revenueTimeframe');
    if(revenueTimeframe) {
      revenueTimeframe.addEventListener('change', function() {
        console.log('Changement de période:', this.value);
        // Ici on rechargerait les données du graphique
      });
    }
  });
</script>
@endsection
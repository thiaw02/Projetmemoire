@extends('layouts.app')

@section('content')
<div class="sandbox-container">
  <div class="sandbox-wrapper">
    <div class="sandbox-card">
      {{-- Header moderne --}}
      <div class="sandbox-header">
        <div class="provider-badge">
          <i class="bi bi-gear-fill"></i>
          {{ strtoupper($order->provider ?? 'LOCAL') }}
        </div>
        <h2 class="sandbox-title">
          <i class="bi bi-credit-card-2-front"></i>
          Sandbox Paiement
        </h2>
        <p class="sandbox-subtitle">Mode test - Simulation de paiement</p>
      </div>

      {{-- Détails de la commande --}}
      <div class="sandbox-body">
        <div class="order-info">
          <h4>
            <i class="bi bi-receipt"></i>
            Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
          </h4>
          
          <div class="items-list">
            @foreach($order->items as $it)
              <div class="item">
                <div class="item-details">
                  <i class="bi bi-dot"></i>
                  <span class="item-label">{{ $it->label }}</span>
                </div>
                <div class="item-amount">{{ number_format($it->amount, 0, ',', ' ') }} XOF</div>
              </div>
            @endforeach
          </div>
          
          <div class="total-amount">
            <span>Total à payer</span>
            <span class="amount">{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</span>
          </div>
        </div>

        {{-- Actions de simulation --}}
        <div class="sandbox-actions">
          <h5><i class="bi bi-play-circle"></i> Actions de test</h5>
          <div class="action-buttons">
            <a href="{{ route('payments.success', ['order' => $order->id]) }}" class="btn btn-success-modern">
              <i class="bi bi-check-circle-fill"></i>
              <span>Simuler succès</span>
              <small>Paiement réussi</small>
            </a>
            <a href="{{ route('payments.cancel', ['order' => $order->id]) }}" class="btn btn-cancel-modern">
              <i class="bi bi-x-circle-fill"></i>
              <span>Simuler échec</span>
              <small>Paiement annulé</small>
            </a>
          </div>
        </div>

        {{-- Info sandbox --}}
        <div class="sandbox-info">
          <div class="info-icon">
            <i class="bi bi-info-circle-fill"></i>
          </div>
          <div class="info-content">
            <strong>Mode SANDBOX activé</strong>
            <p>Cette interface simule les paiements. Configurez vos clés API Wave/Orange Money dans le fichier .env pour passer en mode production.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  /* Variables pour sandbox */
  :root {
    --sandbox-primary: #0ea5e9;
    --sandbox-success: #10b981;
    --sandbox-warning: #f59e0b;
    --sandbox-danger: #ef4444;
    --sandbox-bg: #f1f5f9;
    --sandbox-card: #ffffff;
  }

  /* Style global pour sandbox */
  body {
    background: var(--sandbox-bg) !important;
  }

  .sandbox-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .sandbox-wrapper {
    max-width: 600px;
    width: 100%;
  }

  .sandbox-card {
    background: var(--sandbox-card);
    border-radius: 24px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    border: 1px solid rgba(14, 165, 233, 0.1);
  }

  /* Header sandbox */
  .sandbox-header {
    background: linear-gradient(135deg, var(--sandbox-primary), #0284c7);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .sandbox-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }

  .provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    backdrop-filter: blur(10px);
  }

  .sandbox-title {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
  }

  .sandbox-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
    margin: 0;
  }

  /* Corps sandbox */
  .sandbox-body {
    padding: 2.5rem 2rem;
  }

  .order-info {
    margin-bottom: 2rem;
  }

  .order-info h4 {
    color: #1e293b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .items-list {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
  }

  .item:last-child {
    border-bottom: none;
  }

  .item-details {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .item-label {
    font-weight: 500;
    color: #374151;
  }

  .item-amount {
    font-weight: 600;
    color: var(--sandbox-success);
  }

  .total-amount {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    padding: 1.5rem;
    border-radius: 12px;
    border: 2px solid var(--sandbox-success);
  }

  .total-amount span:first-child {
    font-weight: 600;
    color: #065f46;
  }

  .total-amount .amount {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--sandbox-success);
  }

  /* Actions sandbox */
  .sandbox-actions {
    margin-bottom: 2rem;
  }

  .sandbox-actions h5 {
    color: #1e293b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-success-modern, .btn-cancel-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid;
    font-weight: 600;
  }

  .btn-success-modern {
    background: linear-gradient(135deg, var(--sandbox-success), #059669);
    color: white;
    border-color: var(--sandbox-success);
  }

  .btn-success-modern:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    color: white;
  }

  .btn-cancel-modern {
    background: white;
    color: var(--sandbox-danger);
    border-color: var(--sandbox-danger);
  }

  .btn-cancel-modern:hover {
    background: var(--sandbox-danger);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
  }

  .btn-success-modern i, .btn-cancel-modern i {
    font-size: 1.5rem;
  }

  .btn-success-modern span, .btn-cancel-modern span {
    font-size: 1rem;
  }

  .btn-success-modern small, .btn-cancel-modern small {
    opacity: 0.8;
    font-size: 0.75rem;
  }

  /* Info sandbox */
  .sandbox-info {
    display: flex;
    gap: 1rem;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--sandbox-primary);
  }

  .info-icon {
    flex-shrink: 0;
  }

  .info-icon i {
    font-size: 1.5rem;
    color: var(--sandbox-primary);
  }

  .info-content strong {
    color: #1e40af;
    display: block;
    margin-bottom: 0.5rem;
  }

  .info-content p {
    color: #3730a3;
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .sandbox-container {
      padding: 1rem;
    }

    .sandbox-header {
      padding: 2rem 1.5rem;
    }

    .sandbox-title {
      font-size: 1.5rem;
    }

    .sandbox-body {
      padding: 2rem 1.5rem;
    }

    .action-buttons {
      grid-template-columns: 1fr;
    }

    .total-amount {
      flex-direction: column;
      gap: 0.5rem;
      text-align: center;
    }
  }
</style>
@endsection

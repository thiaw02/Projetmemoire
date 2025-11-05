@extends('layouts.app')

@section('content')
{{-- Header moderne pour paiements --}}
<div class="payments-modern-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-wallet2"></i>
      <span>Gestion des Paiements</span>
    </div>
    <div class="header-actions">
      <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>Rafraîchir</a>
      <a href="{{ route('secretaire.payments.export.csv') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-filetype-csv me-1"></i> CSV</a>
      <a href="{{ route('secretaire.payments.export.pdf') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-filetype-pdf me-1"></i> PDF</a>
      <a href="{{ route('secretaire.payments.settings') }}" class="btn btn-light btn-sm"><i class="bi bi-gear me-1"></i> Tarifs</a>
      <a href="{{ Auth::user()->hasRole('admin') ? route('admin.dashboard') : route('secretaire.dashboard') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Retour</a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    @if(session('payment_url'))
      <div class="mt-2 p-2 bg-light border rounded">
        <small class="text-muted">Lien de paiement:</small><br>
        <a href="{{ session('payment_url') }}" target="_blank" class="text-decoration-none">{{ session('payment_url') }}</a>
      </div>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="create-payment-card">
  <div class="payment-card-header">
    <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Créer un lien de paiement</h5>
  </div>
  <div class="payment-card-body">
    <form method="POST" action="{{ route('secretaire.payments.create') }}" class="payment-form">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Patient</label>
          <select name="patient_user_id" class="form-select" required>
            <option value="">— Sélectionner un patient —</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->email }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-tags me-1"></i>Type de paiement</label>
          <select name="kind" class="form-select">
            <option value="consultation" selected>Consultation</option>
            <option value="analyse">Analyse</option>
            <option value="acte">Acte</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold"><i class="bi bi-currency-exchange me-1"></i>Montant (XOF)</label>
          <input type="number" name="amount" min="100" step="100" value="5000" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Prestataire</label>
          <select name="provider" class="form-select">
            <option value="paydunya" selected>PayDunya (PAR)</option>
          </select>
        </div>
        <div class="col-md-4">
          <div class="d-flex align-items-end h-100">
            <button class="btn btn-success btn-lg w-100"><i class="bi bi-link-45deg me-2"></i> Générer le lien</button>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold"><i class="bi bi-tag me-1"></i>Libellé (optionnel)</label>
          <input type="text" name="label" class="form-control" placeholder="Ex: Consultation cardiologie">
        </div>
      </div>
    </form>
  </div>
</div>

<div class="payments-table-container">
  <div class="table-header">
    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Historique des paiements</h5>
  </div>
  <div class="table-responsive">
    <table class="table payments-table">
      <thead>
        <tr>
          <th><i class="bi bi-calendar me-1"></i>Date</th>
          <th><i class="bi bi-person me-1"></i>Patient</th>
          <th><i class="bi bi-tag me-1"></i>Libellé</th>
          <th class="text-end"><i class="bi bi-currency-exchange me-1"></i>Montant</th>
          <th><i class="bi bi-building me-1"></i>Prestataire</th>
          <th><i class="bi bi-check-circle me-1"></i>Statut</th>
          <th class="text-end"><i class="bi bi-gear me-1"></i>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $o)
        <tr>
          <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $o->user->name ?? '—' }}</td>
          <td>{{ optional($o->items->first())->label ?? '—' }}</td>
          <td class="text-end fw-bold">{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</td>
          <td>{{ strtoupper($o->provider ?? '—') }}</td>
          <td>
            <span class="badge {{ $o->status==='paid' ? 'bg-success' : ($o->status==='pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $o->status }}</span>
          </td>
          <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
              @if($o->payment_url && $o->status==='pending')
                <a class="btn btn-outline-primary btn-sm" href="{{ $o->payment_url }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Ouvrir</a>
              @endif
              @if($o->status==='pending')
                <form action="{{ route('secretaire.payments.markPaid', $o->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-outline-success btn-sm" onclick="return confirm('Confirmer l\'encaissement en espèces ?');">
                    <i class="bi bi-cash-coin me-1"></i>Marquer payé (cash)
                  </button>
                </form>
              @endif
              @if($o->status==='paid')
                <a class="btn btn-outline-success btn-sm" href="{{ route('payments.receipt', $o->id) }}"><i class="bi bi-receipt me-1"></i>Quittance</a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Aucun paiement enregistré</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
{{-- Styles modernes pour la page paiements --}}
<style>
  /* Conteneur principal */
  body > .container { max-width: 1400px !important; }
  
  /* Header moderne paiements */
  .payments-modern-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
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
  
  .header-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
  
  .header-actions .btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  /* Carte de création de paiement */
  .create-payment-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(37, 99, 235, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .payment-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .payment-card-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .payment-card-body {
    padding: 2rem;
  }
  
  .payment-form .form-label {
    color: #374151;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .payment-form .form-control,
  .payment-form .form-select {
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
  }
  
  .payment-form .form-control:focus,
  .payment-form .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }
  
  .payment-form .btn-success {
    background: linear-gradient(135deg, #16a085 0%, #27ae60 100%);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .payment-form .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(22, 160, 133, 0.3);
  }
  
  /* Conteneur tableau paiements */
  .payments-table-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(37, 99, 235, 0.1);
  }
  
  .payments-table-container .table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .payments-table-container .table-header h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .payments-table {
    margin: 0;
  }
  
  .payments-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .payments-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .payments-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .payments-table .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
  }
  
  .payments-table .btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 6px;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .header-actions {
      justify-content: center;
    }
    
    .payment-card-body {
      padding: 1.5rem;
    }
    
    .payments-table th,
    .payments-table td {
      padding: 0.75rem 1rem;
    }
  }
</style>
@endsection

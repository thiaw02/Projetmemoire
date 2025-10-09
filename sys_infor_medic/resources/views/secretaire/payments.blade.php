@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1200px !important; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Paiements patients</h4>
  <div class="d-flex gap-2">
    <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-secondary btn-sm">Rafraîchir</a>
    <a href="{{ route('secretaire.payments.export.csv') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv me-1"></i> Export CSV</a>
    <a href="{{ route('secretaire.payments.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-filetype-pdf me-1"></i> Export PDF</a>
    <a href="{{ route('secretaire.payments.settings') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-gear me-1"></i> Tarifs</a>
    <a href="{{ route('secretaire.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}
    @if(session('payment_url'))
      <div class="mt-1 small">Lien: <a href="{{ session('payment_url') }}" target="_blank">{{ session('payment_url') }}</a></div>
    @endif
  </div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card mb-3">
  <div class="card-header">Créer un lien de paiement</div>
  <div class="card-body">
    <form method="POST" action="{{ route('secretaire.payments.create') }}" class="row g-2 align-items-end">
      @csrf
      <div class="col-md-4">
        <label class="form-label">Patient</label>
        <select name="patient_user_id" class="form-select form-select-sm" required>
          <option value="">— Sélectionner —</option>
          @foreach($patients as $p)
            <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->email }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="kind" class="form-select form-select-sm">
          <option value="consultation" selected>Consultation</option>
          <option value="analyse">Analyse</option>
          <option value="acte">Acte</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Montant (XOF)</label>
        <input type="number" name="amount" min="100" step="100" value="5000" class="form-control form-control-sm" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Prestataire</label>
        <select name="provider" class="form-select form-select-sm">
          <option value="wave">Wave</option>
          <option value="orangemoney">Orange Money</option>
        </select>
      </div>
      <div class="col-md-9">
        <label class="form-label">Libellé (optionnel)</label>
        <input type="text" name="label" class="form-control form-control-sm" placeholder="Ex: Ticket consultation">
      </div>
      <div class="col-md-3 text-end">
        <button class="btn btn-success btn-sm"><i class="bi bi-link-45deg me-1"></i> Générer le lien</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Dernières commandes</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Patient</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th>Prestataire</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $o)
          <tr>
            <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $o->user->name ?? '—' }}</td>
            <td>{{ optional($o->items->first())->label ?? '—' }}</td>
            <td>{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</td>
            <td>{{ strtoupper($o->provider ?? '—') }}</td>
            <td>
              <span class="badge {{ $o->status==='paid' ? 'bg-success' : ($o->status==='pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $o->status }}</span>
            </td>
            <td class="text-end d-flex gap-1 justify-content-end">
              @if($o->payment_url && $o->status==='pending')
                <a class="btn btn-outline-primary btn-sm" href="{{ $o->payment_url }}" target="_blank">Ouvrir</a>
              @endif
              @if($o->status==='paid')
                <a class="btn btn-outline-success btn-sm" href="{{ route('payments.receipt', $o->id) }}">Quittance</a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-muted">Aucune commande</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

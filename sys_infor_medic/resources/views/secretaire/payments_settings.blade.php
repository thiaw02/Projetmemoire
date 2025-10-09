@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 900px !important; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Tarifs des tickets</h4>
  <a href="{{ route('secretaire.payments') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('secretaire.payments.settings.save') }}" class="row g-3">
      @csrf
      <div class="col-md-6">
        <label class="form-label">Ticket consultation (XOF)</label>
        <input type="number" name="price_consultation" min="100" step="100" value="{{ $defaults['consultation'] ?? 5000 }}" class="form-control form-control-sm" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Analyse (XOF)</label>
        <input type="number" name="price_analyse" min="100" step="100" value="{{ $defaults['analyse'] ?? 10000 }}" class="form-control form-control-sm" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Acte médical (XOF)</label>
        <input type="number" name="price_acte" min="100" step="100" value="{{ $defaults['acte'] ?? 7000 }}" class="form-control form-control-sm" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Devise</label>
        <input type="text" name="currency" value="{{ $defaults['currency'] ?? 'XOF' }}" class="form-control form-control-sm" required>
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-1"></i> Enregistrer</button>
      </div>
    </form>
  </div>
</div>
@endsection

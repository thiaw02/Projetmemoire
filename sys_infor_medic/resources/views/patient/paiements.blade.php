@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1100px !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Mes paiements</h4>
  @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-0">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger py-1 px-2 mb-0">{{ $errors->first() }}</div>
  @endif
</div>

<div class="card mb-3">
  <div class="card-header">Nouveau paiement</div>
  <div class="card-body">
    <form method="POST" action="{{ route('patient.payments.checkout') }}" class="row g-2 align-items-end" id="paymentForm">
      @csrf
      <div class="col-md-4">
        <label class="form-label">Type</label>
        <select name="kind" id="kind" class="form-select form-select-sm">
          <option value="consultation" selected>Ticket de consultation</option>
          <option value="analyse">Analyse prescrite</option>
          <option value="acte">Acte médical</option>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">Référence (optionnelle)</label>
        <select name="ref_id" id="ref_id" class="form-select form-select-sm">
          <option value="">— Aucune —</option>
          @if(!empty($consultationsList))
            @foreach($consultationsList as $c)
              <option data-type="consultation" value="consultation:{{ $c->id }}">Consultation #{{ $c->id }} — {{ optional($c->date_consultation)->format('d/m/Y') ?? (string)($c->date_consultation ?? '') }}</option>
            @endforeach
          @endif
          @if(!empty($analysesList))
            @foreach($analysesList as $a)
              <option data-type="analyse" value="analyse:{{ $a->id }}">Analyse #{{ $a->id }} — {{ $a->type_analyse ?? $a->type ?? '—' }}</option>
            @endforeach
          @endif
        </select>
        <small class="text-muted">Sélectionnez une consultation ou analyse liée si disponible</small>
      </div>
      <div class="col-md-3">
        <label class="form-label">Prestataire</label>
        <select name="provider" class="form-select form-select-sm">
          <option value="wave">Wave</option>
          <option value="orangemoney">Orange Money</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Montant (XOF)</label>
        <input type="number" min="100" step="100" name="amount" id="amount" value="{{ $priceConsult }}" class="form-control form-control-sm" required readonly>
      </div>
      <div class="col-md-8">
        <label class="form-label">Libellé</label>
        <input type="text" name="label" id="label" class="form-control form-control-sm" placeholder="Ex: Ticket consultation">
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-success btn-sm"><i class="bi bi-credit-card me-1"></i> Payer</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Historique</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Ticket</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th>Prestataire</th>
            <th>Statut</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $o)
            @php($item = $o->items->first())
            <tr>
              <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $item->ticket_number ?? '—' }}</td>
              <td>{{ $item->label ?? '—' }}</td>
              <td>{{ number_format($o->total_amount, 0, ',', ' ') }} XOF</td>
              <td>{{ strtoupper($o->provider ?? '—') }}</td>
              <td>
                <span class="badge {{ $o->status==='paid' ? 'bg-success' : ($o->status==='pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                  {{ $o->status }}
                </span>
              </td>
              <td class="text-end d-flex gap-1 justify-content-end">
                @if($o->status==='pending' && $o->payment_url)
                  <a href="{{ $o->payment_url }}" class="btn btn-outline-primary btn-sm">Poursuivre</a>
                @endif
                @if($o->status==='paid')
                  <a href="{{ route('payments.receipt', $o->id) }}" class="btn btn-outline-success btn-sm">Quittance</a>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-muted">Aucun paiement</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@section('scripts')
<script>
(function(){
  const prices = {
    consultation: {{ (int)($priceConsult ?? 5000) }},
    analyse: {{ (int)($priceAnalyse ?? 10000) }},
    acte: {{ (int)($priceActe ?? 7000) }}
  };
  const kindSel = document.getElementById('kind');
  const amountInp = document.getElementById('amount');
  const labelInp = document.getElementById('label');
  const refSel = document.getElementById('ref_id');
  function updateAmountAndLabel(){
    const k = (kindSel?.value)||'consultation';
    amountInp.value = prices[k]||prices.consultation;
    if (!labelInp.value) {
      labelInp.value = k==='consultation' ? 'Ticket de consultation' : (k==='analyse' ? 'Analyse' : 'Acte médical');
    }
  }
  kindSel?.addEventListener('change', ()=>{
    updateAmountAndLabel();
    // Optionnel: filtrer les ref visibles par type
    const type = kindSel.value;
    Array.from(refSel.options).forEach(opt=>{
      if (!opt.value) return (opt.hidden=false);
      const is = (opt.value||'').startsWith(type+':');
      opt.hidden = !is;
    });
    refSel.value = '';
  });
  updateAmountAndLabel();
})();
</script>
@endsection
@endsection

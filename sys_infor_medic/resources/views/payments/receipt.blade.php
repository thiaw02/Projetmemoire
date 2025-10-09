@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Quittance de paiement</div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div class="fw-bold">{{ $hospital['name'] }}</div>
              <div class="text-muted small">{{ $hospital['address'] }}</div>
              @if(!empty($hospital['phone']))<div class="text-muted small">Tél: {{ $hospital['phone'] }}</div>@endif
            </div>
            <div class="text-end">
              <div class="small text-muted">Quittance #{{ $order->id }}</div>
              <div class="small">Généré le {{ $generatedAt->format('d/m/Y H:i') }}</div>
            </div>
          </div>
          <hr>
          <div class="mb-2">
            <div><strong>Payeur:</strong> {{ $order->user->name ?? '—' }}</div>
            <div><strong>Date:</strong> {{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}</div>
            <div><strong>Prestataire:</strong> {{ strtoupper($order->provider ?? '—') }}</div>
            <div><strong>Référence:</strong> {{ $order->provider_ref ?? '-' }}</div>
            <div><strong>Statut:</strong> {{ strtoupper($order->status) }}</div>
          </div>
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Désignation</th>
                <th class="text-end">Montant (XOF)</th>
              </tr>
            </thead>
            <tbody>
            @foreach($order->items as $it)
              <tr>
                <td>{{ $it->label }}</td>
                <td class="text-end">{{ number_format($it->amount, 0, ',', ' ') }}</td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }}</th>
              </tr>
            </tfoot>
          </table>
          <div class="small text-muted">Document généré automatiquement. Merci pour votre confiance.</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

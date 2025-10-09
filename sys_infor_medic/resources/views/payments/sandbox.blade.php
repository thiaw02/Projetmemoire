@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Sandbox Paiement ({{ strtoupper($order->provider ?? 'LOCAL') }})</div>
        <div class="card-body">
          <p>Commande #{{ $order->id }}</p>
          <ul>
            @foreach($order->items as $it)
              <li>{{ $it->label }} — {{ number_format($it->amount, 0, ',', ' ') }} XOF</li>
            @endforeach
          </ul>
          <p>Total: <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</strong></p>
          <div class="d-flex gap-2">
            <a href="{{ route('payments.success', ['order' => $order->id]) }}" class="btn btn-success">Simuler succès</a>
            <a href="{{ route('payments.cancel', ['order' => $order->id]) }}" class="btn btn-outline-secondary">Annuler</a>
          </div>
          <div class="alert alert-info mt-3">
            Mode SANDBOX activé. Configurez vos clés WAVE/ORANGE MONEY pour passer en production.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

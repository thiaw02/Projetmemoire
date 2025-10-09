@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Liste des paiements</h4>
  <div class="mb-2 small text-muted">Généré le {{ $generatedAt->format('d/m/Y H:i') }}</div>
  <table class="table table-sm table-bordered">
    <thead>
      <tr>
        <th>Date</th>
        <th>Patient</th>
        <th>Libellé</th>
        <th class="text-end">Montant (XOF)</th>
        <th>Prestataire</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>
    @foreach($orders as $o)
      <tr>
        <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
        <td>{{ $o->user->name ?? '—' }}</td>
        <td>{{ optional($o->items->first())->label ?? '—' }}</td>
        <td class="text-end">{{ number_format($o->total_amount, 0, ',', ' ') }}</td>
        <td>{{ strtoupper($o->provider ?? '—') }}</td>
        <td>{{ strtoupper($o->status) }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endsection

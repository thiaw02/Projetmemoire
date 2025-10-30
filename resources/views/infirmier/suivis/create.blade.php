@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0 text-success"><i class="bi bi-heart-pulse me-2"></i>Saisir un suivi patient</h3>
      <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour Dashboard
      </a>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('infirmier.suivis.store') }}" class="card p-3">
      @csrf
      <input type="hidden" name="rdv_id" value="{{ $rdvId ?? '' }}">
      <div class="mb-3">
        <label class="form-label">Patient</label>
        @php($selected = isset($selectedPatient) && $selectedPatient ? $selectedPatient : (($preselect ?? 0) ? $patients->firstWhere('id', $preselect) : null))
        @if($selected)
          <input type="hidden" name="patient_id" value="{{ $selected->id }}">
          <input type="text" class="form-control" value="{{ $selected->nom }} {{ $selected->prenom }}{{ $selected->user ? ' ('.$selected->user->email.')' : '' }}" readonly>
        @else
          <select name="patient_id" class="form-select" required>
            <option value="">Sélectionner un patient…</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">
                {{ $p->nom }} {{ $p->prenom }} @if($p->user) ({{ $p->user->email }}) @endif
              </option>
            @endforeach
          </select>
        @endif
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Température (°C)</label>
          <input type="number" step="0.1" name="temperature" class="form-control" placeholder="Ex: 37.0">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tension</label>
          <input type="text" name="tension" class="form-control" placeholder="Ex: 12/8">
        </div>
        <div class="col-md-4">
          <label class="form-label">Date du suivi</label>
          <input type="date" name="date_suivi" class="form-control" value="{{ now()->toDateString() }}">
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-success"><i class="bi bi-save me-1"></i>Enregistrer</button>
        <a href="{{ route('infirmier.rendezvous.index') }}" class="btn btn-outline-secondary">Annuler</a>
      </div>
    </form>
  </div>
</div>
@endsection

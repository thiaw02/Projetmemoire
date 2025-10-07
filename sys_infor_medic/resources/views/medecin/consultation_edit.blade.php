@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">✏️ Modifier la consultation</h3>
  <a href="{{ route('medecin.consultations') }}" class="btn btn-outline-secondary">← Retour</a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('medecin.consultations.update', $consult->id) }}">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">Patient</label>
        <input type="text" class="form-control" value="{{ $consult->patient->nom }} {{ $consult->patient->prenom }}" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Date & Heure</label>
        <input type="datetime-local" name="date_consultation" class="form-control" value="{{ old('date_consultation', optional($consult->date_consultation ? \Carbon\Carbon::parse($consult->date_consultation) : null)->format('Y-m-d\TH:i')) }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Symptômes</label>
        <textarea name="symptomes" class="form-control" rows="2">{{ old('symptomes', $consult->symptomes) }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Diagnostic</label>
        <textarea name="diagnostic" class="form-control" rows="2">{{ old('diagnostic', $consult->diagnostic) }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Traitement</label>
        <textarea name="traitement" class="form-control" rows="2">{{ old('traitement', $consult->traitement) }}</textarea>
      </div>
      <button class="btn btn-primary">Enregistrer</button>
    </form>
  </div>
</div>
@endsection

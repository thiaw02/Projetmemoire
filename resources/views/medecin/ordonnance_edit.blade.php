@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">✏️ Modifier l'ordonnance</h3>
  <a href="{{ route('medecin.ordonnances') }}" class="btn btn-outline-secondary">← Retour</a>
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
    <form method="POST" action="{{ route('medecin.ordonnances.update', $ordonnance->id) }}">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">Médicaments / Instructions</label>
        <textarea name="medicaments" class="form-control" rows="6" required>{{ old('medicaments', $ordonnance->medicaments ?: $ordonnance->contenu) }}</textarea>
        <div class="form-text">Une ligne par médicament/instruction.</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Dosage global (optionnel)</label>
        <input type="text" name="dosage" class="form-control" value="{{ old('dosage', $ordonnance->dosage) }}">
      </div>
      <button class="btn btn-primary">Enregistrer</button>
    </form>
  </div>
</div>
@endsection

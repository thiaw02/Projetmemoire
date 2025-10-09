@extends('layouts.app')

@section('content')
<style>
  /* Élargir légèrement la zone de travail pour cette page */
  body > .container { max-width: 1360px !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Affectations Médecin ↔ Infirmiers</h4>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card shadow-sm aff-card">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end" aria-label="Choisir un médecin">
      <div class="col-md-6 col-lg-5 col-xl-4">
        <label for="doctor_id" class="form-label">Médecin</label>
        <select class="form-select form-select-sm" id="doctor_id" name="doctor_id" onchange="this.form.submit()">
          <option value="">— Sélectionner un médecin —</option>
          @foreach($doctors as $doc)
            <option value="{{ $doc->id }}" {{ $selectedDoctor && $selectedDoctor->id === $doc->id ? 'selected' : '' }}>
              {{ $doc->name }} @if($doc->specialite) — {{ $doc->specialite }} @endif
            </option>
          @endforeach
        </select>
      </div>
    </form>

    @if($selectedDoctor)
      <hr>
      <form method="POST" action="{{ route('admin.affectations.update', $selectedDoctor->id) }}" aria-label="Affecter des infirmiers">
        @csrf
        @method('PUT')

        <div class="mb-2 text-muted">
          Médecin sélectionné: <strong>{{ $selectedDoctor->name }}</strong>
        </div>

        <div class="row g-2">
          @forelse($nurses as $n)
            <div class="col-xl-4 col-lg-6 col-sm-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="nurses[]" id="nurse_{{ $n->id }}" value="{{ $n->id }}" {{ in_array($n->id, $assignedNurseIds) ? 'checked' : '' }}>
                <label class="form-check-label" for="nurse_{{ $n->id }}">
                  {{ $n->name }} @if($n->pro_phone) <span class="text-muted">— {{ $n->pro_phone }}</span> @endif
                </label>
              </div>
            </div>
          @empty
            <div class="col-12"><em>Aucun infirmier trouvé.</em></div>
          @endforelse
        </div>

        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Enregistrer les affectations</button>
        </div>
      </form>
    @endif

  </div>
</div>
@endsection

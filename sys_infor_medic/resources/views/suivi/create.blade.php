@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">📋 Saisir un suivi patient</h3>
  <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-secondary">← Retour au Dashboard</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('suivi.store') }}" method="POST" id="suiviForm">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                @foreach(App\Models\Patient::orderBy('nom')->get() as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Température (°C)</label>
            <input type="number" step="0.1" name="temperature" class="form-control" placeholder="37.0">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tension</label>
            <input type="text" name="tension" class="form-control" placeholder="120/80">
          </div>
          <div class="col-12">
            <label class="form-label">Observation</label>
            <textarea name="observation" class="form-control" rows="2" placeholder="Notes complémentaires..."></textarea>
          </div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-success" id="btnSubmitSuivi">
            Enregistrer
          </button>
          <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
        </div>
    </form>
  </div>
</div>

<script>
  (function(){
    const form = document.getElementById('suiviForm');
    const btn = document.getElementById('btnSubmitSuivi');
    form?.addEventListener('submit', function(){
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Enregistrement...';
    });
  })();
</script>
@endsection

@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">🔍 Historique des soins (Infirmier)</h3>
  <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-secondary">← Retour au Dashboard</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="text-muted small">Consultez l'historique des suivis et gestes réalisés</div>
      <div class="d-flex gap-2">
        <input type="text" id="searchHistorique" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 280px;">
        <a href="{{ route('suivi.create') }}" class="btn btn-sm btn-success qa-btn">➕ Nouveau suivi</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle" id="historiqueTable">
        <thead>
          <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Température</th>
            <th>Tension</th>
            <th>Observation</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($historiques ?? []) as $h)
            <tr>
              <td>{{ $h->patient->nom ?? '—' }} {{ $h->patient->prenom ?? '' }}</td>
              <td>{{ optional($h->created_at)->format('d/m/Y H:i') }}</td>
              <td>{{ $h->temperature ?? '—' }}</td>
              <td>{{ $h->tension ?? '—' }}</td>
              <td>{{ $h->observation ?? '—' }}</td>
              <td>
                @if(isset($h->id))
                <a href="{{ route('suivi.edit', $h->id) }}" class="btn btn-sm btn-primary qa-btn">Modifier</a>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">Aucun historique n'est disponible.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@section('scripts')
<script>
  (function(){
    // Recherche
    const inp = document.getElementById('searchHistorique');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('#historiqueTable tbody tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    inp?.addEventListener('input', filter);

    // Loader boutons
    document.querySelectorAll('.qa-btn')?.forEach(btn=>{
      btn.addEventListener('click', function(){
        this.classList.add('disabled');
        const orig = this.innerHTML;
        this.dataset.original = orig;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Ouverture...';
      });
    });
  })();
</script>
@endsection
@endsection

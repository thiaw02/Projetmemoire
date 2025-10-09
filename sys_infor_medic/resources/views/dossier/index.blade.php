@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">📁 Dossiers (Infirmier)</h3>
  <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-secondary">← Retour au Dashboard</a>
</div>

<!-- Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="text-muted small">Gérez et mettez à jour les dossiers des patients</div>
      <div class="d-flex gap-2">
        <input type="text" id="searchDossier" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 280px;">
        <a href="{{ route('dossier.create') }}" class="btn btn-sm btn-success qa-btn">➕ Nouveau dossier</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle" id="dossierTable">
        <thead>
          <tr>
            <th>Patient</th>
            <th>Observation</th>
            <th>Dernière MAJ</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($dossiers ?? []) as $d)
            <tr>
              <td>{{ $d->patient->nom ?? '—' }} {{ $d->patient->prenom ?? '' }}</td>
              <td>{{ $d->observation ?? '—' }}</td>
              <td>{{ optional($d->updated_at)->format('d/m/Y H:i') }}</td>
              <td class="d-flex gap-1">
                <a href="{{ route('dossier.show', $d->id) }}" class="btn btn-sm btn-outline-info qa-btn">Voir</a>
                <a href="{{ route('dossier.edit', $d->id) }}" class="btn btn-sm btn-primary qa-btn">Modifier</a>
                <form method="POST" action="{{ route('dossier.destroy', $d->id) }}" onsubmit="return confirm('Supprimer ce dossier ?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger qa-btn">Supprimer</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Aucun dossier n'a été trouvé.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@section('scripts')
<script>
  (function(){
    // Recherche rapide
    const inp = document.getElementById('searchDossier');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('#dossierTable tbody tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    inp?.addEventListener('input', filter);

    // Etat de chargement sur actions
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

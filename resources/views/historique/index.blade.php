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
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
      <div class="text-muted small">Consultez l'historique des suivis et gestes réalisés</div>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        @php
          $patientsList = collect($historiques ?? [])->map(fn($h) => trim(($h->patient->nom ?? '') . ' ' . ($h->patient->prenom ?? '')))->filter()->unique()->values();
        @endphp
        <input type="date" id="startDate" class="form-control form-control-sm" title="Date début">
        <input type="date" id="endDate" class="form-control form-control-sm" title="Date fin">
        <select id="patientFilter" class="form-select form-select-sm" style="max-width: 220px;">
          <option value="">Tous les patients</option>
          @foreach($patientsList as $pName)
            <option value="{{ $pName }}">{{ $pName }}</option>
          @endforeach
        </select>
        <input type="text" id="searchHistorique" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 280px;">
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
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">Aucun historique n'est disponible.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@section('scripts')
<script>
  (function(){
    const searchInp = document.getElementById('searchHistorique');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const patientSel = document.getElementById('patientFilter');

    function parseFrDate(str){
      // expects 'dd/mm/YYYY HH:ii' or 'dd/mm/YYYY'
      if(!str) return null;
      const parts = str.split(' ');
      const dmy = parts[0]?.split('/') || [];
      if(dmy.length !== 3) return null;
      const d = parseInt(dmy[0],10), m = parseInt(dmy[1],10)-1, y = parseInt(dmy[2],10);
      let h=0,i=0;
      if(parts[1]){
        const hi = parts[1].split(':');
        h = parseInt(hi[0]||'0',10); i = parseInt(hi[1]||'0',10);
      }
      return new Date(y,m,d,h,i,0,0);
    }

    function applyFilters(){
      const q = (searchInp?.value || '').toLowerCase();
      const sd = startDate.value ? new Date(startDate.value) : null;
      const ed = endDate.value ? new Date(endDate.value) : null;
      const patient = (patientSel?.value || '').toLowerCase();

      document.querySelectorAll('#historiqueTable tbody tr').forEach(tr=>{
        const tds = tr.querySelectorAll('td');
        if(tds.length < 5){ tr.style.display=''; return; }
        const patientName = (tds[0].innerText || '').toLowerCase();
        const dateText = (tds[1].innerText || '').trim();
        const rowDate = parseFrDate(dateText);
        const textMatch = tr.innerText.toLowerCase().includes(q);
        const patientMatch = !patient || patientName === patient;
        let dateMatch = true;
        if(sd && rowDate) dateMatch = dateMatch && rowDate >= sd;
        if(ed && rowDate) dateMatch = dateMatch && rowDate <= new Date(ed.getFullYear(), ed.getMonth(), ed.getDate(), 23,59,59,999);

        tr.style.display = (textMatch && patientMatch && dateMatch) ? '' : 'none';
      });
    }

    [searchInp, startDate, endDate, patientSel].forEach(el=> el && el.addEventListener('input', applyFilters));
    applyFilters();

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

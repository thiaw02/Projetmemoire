@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>

    {{-- Bouton retour --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">Retour</a>
    </div>

    {{-- Bouton Planifier un rendez-vous --}}
    <div class="mb-3">
        <button class="btn btn-success" id="toggleFormBtn">📅 Planifier un rendez-vous</button>
    </div>

    {{-- Formulaire caché --}}
    <div class="card shadow p-4 mb-4" id="rdvForm" style="display:none;">
        <h3 class="text-success mb-4">📄 Nouveau rendez-vous</h3>

        <form action="{{ route('secretaire.rendezvous.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="patient" class="form-label">Patient</label>
                <select name="patient_id" id="patient" class="form-control" required>
                    <option value="">-- Sélectionnez un patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="medecin" class="form-label">Médecin</label>
                <select name="medecin_id" id="medecin" class="form-control" required>
                    <option value="">-- Sélectionnez un médecin --</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">{{ $medecin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="heure" class="form-label">Heure</label>
                <input type="time" name="heure" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif</label>
                <input type="text" name="motif" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Planifier</button>
        </form>
    </div>

    {{-- Liste des rendez-vous --}}
    <div class="card shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="text-primary mb-0">📅 Rendez-vous existants</h3>
          <input type="text" id="searchRdv" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
        </div>
        <table class="table table-bordered" id="rdvTable">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rendezvous as $rdv)
                    <tr>
                        <td>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</td>
                        <td>{{ $rdv->medecin->name }}</td>
                        <td>{{ $rdv->date }}</td>
                        <td>{{ $rdv->heure }}</td>
                        <td>
                            @php $s = strtolower((string)$rdv->statut);
                              $badge = in_array($s,['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array($s,['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark');
                            @endphp
                            <span class="badge {{ $badge }}">{{ str_replace('_',' ', $rdv->statut) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions rendez-vous">
                              <a href="{{ route('secretaire.rendezvous.confirm', $rdv->id) }}" class="btn btn-success" title="Confirmer" aria-label="Confirmer">✔</a>
                              <a href="{{ route('secretaire.rendezvous.cancel', $rdv->id) }}" class="btn btn-danger" title="Annuler" aria-label="Annuler">✖</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('scripts')
<script>
    document.getElementById('toggleFormBtn').addEventListener('click', function() {
        var form = document.getElementById('rdvForm');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    });
<script>
  (function(){
    const inp = document.getElementById('searchRdv');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('#rdvTable tbody tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    inp?.addEventListener('input', filter);
  })();
</script>
@endsection

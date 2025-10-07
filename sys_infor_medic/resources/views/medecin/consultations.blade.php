@extends('layouts.app')

@section('content')
<style>
  /* Harmoniser la largeur avec les dashboards */
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>📋 Consultations</h3>
    <a href="{{ route('medecin.dashboard') }}" class="btn btn-secondary">
        ← Retour au dashboard
    </a>
</div>

    <!-- Bouton pour afficher le formulaire -->
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formConsultation" aria-expanded="false" aria-controls="formConsultation">
        ➕ Ajouter une consultation
    </button>

    <!-- Formulaire pour ajouter une consultation (collapsible) -->
    <div class="collapse {{ request('patient_id') ? 'show' : '' }}" id="formConsultation">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Ajouter une consultation
            </div>
            <div class="card-body">
                <form action="{{ route('medecin.consultations.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-control" required>
                            <option value="">-- Sélectionner un patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (request('patient_id')==$patient->id) ? 'selected' : '' }}>{{ $patient->nom }} {{ $patient->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date_consultation" class="form-label">Date & Heure</label>
                        <input type="datetime-local" name="date_consultation" id="date_consultation" class="form-control" required value="{{ old('date_consultation', request('date_time')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="symptomes" class="form-label">Symptômes</label>
                        <textarea name="symptomes" id="symptomes" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="diagnostic" class="form-label">Diagnostic</label>
                        <textarea name="diagnostic" id="diagnostic" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="traitement" class="form-label">Traitement</label>
                        <textarea name="traitement" id="traitement" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Ajouter la consultation</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des consultations -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            Mes consultations
        </div>
        <div class="card-body">
            @if($consultations->isEmpty())
                <p>Aucune consultation à venir.</p>
            @else
                <div class="d-flex align-items-center gap-2 mb-2">
                    <input type="text" id="searchConsult" class="form-control form-control-sm" placeholder="Rechercher (patient, diagnostic, symptômes...)" style="max-width: 360px;">
                    <button type="button" id="btnSearchConsult" class="btn btn-sm btn-outline-secondary">Rechercher</button>
                </div>
                <div class="table-responsive">
                <table class="table table-bordered align-middle" id="consultationsTable">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date & Heure</th>
                            <th>Symptômes</th>
                            <th>Diagnostic</th>
                            <th>Traitement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                            <tr>
                                <td>{{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}</td>
                                <td>{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y H:i') }}</td>
                                <td>{{ $consultation->symptomes }}</td>
                                <td>{{ $consultation->diagnostic }}</td>
                                <td>{{ $consultation->traitement }}</td>
                                <td>
                                  @php $s = strtolower((string)($consultation->statut ?? 'en_attente'));
                                    $badge = in_array($s,['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array($s,['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark');
                                  @endphp
                                  <span class="badge {{ $badge }}">{{ str_replace('_',' ', $consultation->statut ?? 'En attente') }}</span>
                                </td>
                                <td>
                                  <a href="{{ route('medecin.consultations.edit', $consultation->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <script>
                  (function(){
                    const inp = document.getElementById('searchConsult');
                    const btn = document.getElementById('btnSearchConsult');
                    const table = document.getElementById('consultationsTable');
                    function apply(){
                      const q = (inp?.value || '').toLowerCase();
                      table?.querySelectorAll('tbody tr')?.forEach(tr=>{
                        const text = tr.innerText.toLowerCase();
                        tr.style.display = text.includes(q) ? '' : 'none';
                      });
                    }
                    btn?.addEventListener('click', apply);
                    inp?.addEventListener('keyup', (e)=>{ if(e.key==='Enter'){ apply(); } });
                  })();
                </script>
            @endif
        </div>
    </div>
@endsection

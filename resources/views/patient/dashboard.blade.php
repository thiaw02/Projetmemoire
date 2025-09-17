@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Déconnexion</button>
    </form>
</div>

<div class="container bg-white p-4 rounded shadow-sm">
    <h2 class="text-success mb-4">👋 Bienvenue {{ $patient->prenom }}</h2>

    {{-- Onglets --}}
    <ul class="nav nav-tabs mb-3" id="patientTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active text-success" data-bs-toggle="tab" data-bs-target="#rdv">📅 Prendre un rendez-vous</button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-success" data-bs-toggle="tab" data-bs-target="#dossier">📁 Mon dossier médical</button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-success" data-bs-toggle="tab" data-bs-target="#historique">📜 Historique</button>
        </li>
    </ul>

    {{-- Contenu des onglets --}}
    <div class="tab-content">
        {{-- 📅 Rendez-vous --}}
        <div class="tab-pane fade show active" id="rdv">
            <div id="calendar"></div>
            <!-- Bouton pour ouvrir le modal -->
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rendezVousModal">
  Prendre un rendez-vous
</button>

<!-- Modal -->
<div class="modal fade" id="rendezVousModal" tabindex="-1" aria-labelledby="rendezVousLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('rendez.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="rendezVousLabel">Prendre un rendez-vous</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Heure -->
          <div class="mb-3">
            <label for="heure" class="form-label">Heure</label>
            <input type="time" class="form-control" id="heure" name="heure" required>
          </div>

          <!-- Motif -->
          <div class="mb-3">
            <label for="motif" class="form-label">Motif</label>
            <input type="text" class="form-control" id="motif" name="motif" placeholder="Motif du rendez-vous" required>
          </div>

          <!-- Médecin -->
          <div class="mb-3">
            <label for="medecin_id" class="form-label">Médecin</label>
            <select class="form-select" id="medecin_id" name="medecin_id" required>
              <option value="" selected disabled>Choisir un médecin</option>
              @foreach($medecins as $medecin)
                <option value="{{ $medecin->id }}">{{ $medecin->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Prendre rendez-vous</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</div>

        </div>

         {{-- 📁 Dossier médical --}}
        <div class="tab-pane fade" id="dossier">
            <div class="row">
                {{-- Ordonnances --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">💊 Ordonnances</div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @forelse($ordonnances ?? collect() as $ordonnance)
                                    <li>
                                        {{ \Carbon\Carbon::parse($ordonnance->date ?? now())->format('d/m/Y') }} –
                                        {{ $ordonnance->contenu ?? '—' }}
                                        (Médecin : {{ $ordonnance->medecin->name ?? 'Inconnu' }})
                                    </li>
                                @empty
                                    <li>Aucune ordonnance enregistrée</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Analyses --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">🧪 Analyses</div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @forelse($analyses ?? collect() as $analyse)
                                    <li>
                                        {{ \Carbon\Carbon::parse($analyse->date_analyse ?? now())->format('d/m/Y') }} –
                                        {{ $analyse->type_analyse ?? '—' }} : {{ $analyse->resultats ?? '—' }}
                                        (Médecin : {{ $analyse->medecin->name ?? 'Inconnu' }})
                                    </li>
                                @empty
                                    <li>Aucune analyse enregistrée</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Consultations --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">🩺 Consultations</div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @forelse($consultations ?? collect() as $consultation)
                                    <li>
                                        {{ \Carbon\Carbon::parse($consultation->date_consultation ?? now())->format('d/m/Y') }} –
                                        Symptômes : {{ $consultation->symptomes ?? '—' }}
                                        @if($consultation->diagnostic)
                                            <br><em>Diagnostic : {{ $consultation->diagnostic }}</em>
                                        @endif
                                        @if($consultation->traitement)
                                            <br><em>Traitement : {{ $consultation->traitement }}</em>
                                        @endif
                                        (Médecin : {{ $consultation->medecin->name ?? 'Inconnu' }})
                                    </li>
                                @empty
                                    <li>Aucune consultation enregistrée</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📜 Historique des consultations --}}
<div class="tab-pane fade" id="historique">

    <h4>🧑 Informations personnelles</h4>
    <table class="table table-bordered w-50 mb-4">
        <tr>
            <th>Prénom</th>
            <td>{{ $patient->prenom ?? '—' }}</td>
        </tr>
        <tr>
            <th>Nom</th>
            <td>{{ $patient->nom ?? '—' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $patient->user->email ?? '—' }}</td>
        </tr>
        <tr>
            <th>Téléphone</th>
            <td>{{ $patient->telephone ?? '—' }}</td>
        </tr>
        <tr>
            <th>Date de naissance</th>
            <td>{{ $patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr>
            <th>Adresse</th>
            <td>{{ $patient->adresse ?? '—' }}</td>
        </tr>
    </table>

    <h4>Historique des consultations</h4>
    <table class="table table-striped table-bordered">
        <thead class="table-success">
            <tr>
                <th>Date</th>
                <th>Symptômes / Diagnostic / Traitement</th>
                <th>Médecin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultations ?? collect() as $consultation)
            <tr>
                <td>{{ \Carbon\Carbon::parse($consultation->date_consultation ?? now())->format('d/m/Y') }}</td>
                <td>
                    Symptômes : {{ $consultation->symptomes ?? '—' }}
                    @if($consultation->diagnostic)
                        <br>Diagnostic : {{ $consultation->diagnostic }}
                    @endif
                    @if($consultation->traitement)
                        <br>Traitement : {{ $consultation->traitement }}
                    @endif
                </td>
                <td>{{ $consultation->medecin->name ?? 'Non attribué' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Aucune consultation enregistrée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            selectable: true,
            events: [
                @foreach($rendezVous ?? collect() as $rdv)
                {
                    title: '{{ $rdv->motif }} - {{ $rdv->medecin->name ?? "Médecin non renseigné" }}',
                    start: '{{ $rdv->date }}T{{ $rdv->heure }}'
                },
                @endforeach
            ],
            dateClick: function(info) {
                document.getElementById('rdv-date').value = info.dateStr;
                var modal = new bootstrap.Modal(document.getElementById('rdvModal'));
                modal.show();
            }
        });
        calendar.render();
    }
});
</script>
@endsection

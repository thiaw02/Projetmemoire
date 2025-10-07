@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .card-body.scrollable { max-height: 380px; overflow: auto; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">📁 Dossier Patient</h3>
  <div class="btn-group" role="group" aria-label="Actions dossier">
    <a href="{{ route('medecin.consultations', ['patient_id' => $patient->id, 'date_time' => \Carbon\Carbon::now()->format('Y-m-d\TH:i')]) }}" class="btn btn-success btn-sm" title="Créer consultation"><i class="bi bi-clipboard-plus"></i></a>
    <a href="{{ route('medecin.ordonnances', ['patient_id' => $patient->id]) }}" class="btn btn-outline-warning btn-sm" title="Rédiger ordonnance"><i class="bi bi-capsule"></i></a>
    <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-outline-secondary btn-sm" title="Retour à la liste"><i class="bi bi-arrow-left"></i></a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="text-muted small">Identité</div>
        <div class="fs-5 fw-semibold">{{ $patient->nom }} {{ $patient->prenom }}</div>
        <div class="mt-1">
          <span class="badge bg-light text-dark border">Sexe: {{ $patient->sexe }}</span>
          <span class="badge bg-light text-dark border">Né(e) le: {{ optional($patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance) : null)->format('d/m/Y') }}</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Contact</div>
        <div>Email: {{ $patient->email ?? '—' }}</div>
        <div>Téléphone: {{ $patient->telephone ?? '—' }}</div>
        <div>Adresse: {{ $patient->adresse ?? '—' }}</div>
        <div>Groupe sanguin: {{ $patient->groupe_sanguin ?? '—' }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card border-info shadow-sm mb-3">
      <div class="card-header bg-info text-white">Constantes (dernier suivi)</div>
      <div class="card-body">
        @if($lastSuivi)
          <div class="d-flex flex-column gap-1">
            <div>Température: <strong>{{ $lastSuivi->temperature ?? '—' }} °C</strong></div>
            <div>Tension: <strong>{{ $lastSuivi->tension ?? '—' }}</strong></div>
            <div class="text-muted small">Enregistré le {{ optional($lastSuivi->created_at)->format('d/m/Y H:i') }}</div>
          </div>
        @else
          <div class="text-muted">Aucune constante enregistrée.</div>
        @endif
      </div>
    </div>

    <div class="card border-secondary shadow-sm">
      <div class="card-header bg-secondary text-white">Historique des constantes</div>
      <div class="card-body scrollable">
        @if($patient->suivis->isEmpty())
          <div class="text-muted">Aucun suivi.</div>
        @else
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Temp.</th>
                <th>Tension</th>
              </tr>
            </thead>
            <tbody>
              @foreach($patient->suivis as $sv)
                <tr>
                  <td>{{ optional($sv->created_at)->format('d/m/Y H:i') }}</td>
                  <td>{{ $sv->temperature ?? '—' }}</td>
                  <td>{{ $sv->tension ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-primary text-white">Consultations (vous)</div>
      <div class="card-body scrollable">
        @php $consults = $patient->consultations ?? collect(); @endphp
        @if($consults->isEmpty())
          <div class="text-muted">Aucune consultation enregistrée par vous pour ce patient.</div>
        @else
          <div class="list-group list-group-flush">
            @foreach($consults as $c)
              <div class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-semibold">{{ optional($c->date_consultation ? \Carbon\Carbon::parse($c->date_consultation) : null)->format('d/m/Y') }}</div>
                    <div class="small text-muted">Statut: {{ $c->statut ?? '—' }}</div>
                  </div>
                  <div class="text-end">
                    <div class="small text-muted">Diagnostic</div>
                    <div>{{ $c->diagnostic ?? '—' }}</div>
                  </div>
                </div>
                @if($c->symptomes)
                <div class="mt-2 small"><span class="text-muted">Symptômes:</span> {{ $c->symptomes }}</div>
                @endif
                @if($c->traitement)
                <div class="mt-1 small"><span class="text-muted">Traitement:</span> {{ $c->traitement }}</div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="card shadow-sm mb-3">
      <div class="card-header bg-warning">Ordonnances</div>
      <div class="card-body scrollable">
        @php $ords = $patient->ordonnances ?? collect(); @endphp
        @if($ords->isEmpty())
          <div class="text-muted">Aucune ordonnance.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($ords as $o)
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div class="me-3">
                    <div class="fw-semibold">{{ optional($o->created_at)->format('d/m/Y H:i') }}</div>
                    @php($text = $o->medicaments ?: $o->contenu)
                    @if(!empty($text))
                      @php($lines = preg_split("/(\r\n|\r|\n)/", $text))
                      <ul class="mb-1 mt-1">
                        @foreach($lines as $ln)
                          @if(trim($ln) !== '')
                            <li>{{ $ln }}</li>
                          @endif
                        @endforeach
                      </ul>
                    @else
                      <div class="text-muted small">—</div>
                    @endif
                    @if(!empty($o->dosage))
                      <div class="small text-muted">Dosage: {{ $o->dosage }}</div>
                    @endif
                  </div>
                  <div class="text-muted small">Par Dr. {{ optional($o->medecin)->name }}</div>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">Analyses</div>
      <div class="card-body scrollable">
        @php $analyses = $patient->analyses ?? collect(); @endphp
        @if($analyses->isEmpty())
          <div class="text-muted">Aucune analyse.</div>
        @else
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Résultats</th>
              </tr>
            </thead>
            <tbody>
              @foreach($analyses as $a)
                <tr>
                  <td>{{ optional($a->date_analyse ? \Carbon\Carbon::parse($a->date_analyse) : null)->format('d/m/Y') }}</td>
                  <td>{{ $a->type_analyse ?? '—' }}</td>
                  <td>{{ $a->resultats ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">💊 Ordonnances</h3>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#formOrdonnance" aria-expanded="false" aria-controls="formOrdonnance">
        ➕ Ajouter une ordonnance
    </button>
    <a href="{{ route('medecin.dashboard') }}" class="btn btn-outline-secondary">← Retour au Dashboard</a>
  </div>
</div>

    <!-- Formulaire pour ajouter une ordonnance (collapse) -->
    <div class="collapse {{ request('patient_id') ? 'show' : '' }}" id="formOrdonnance">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Rédiger une ordonnance
            </div>
            <div class="card-body">
                <form action="{{ route('medecin.ordonnances.store') }}" method="POST">
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
                        <label for="medicaments" class="form-label">Médicaments / Instructions</label>
                        <textarea name="medicaments" id="medicaments" class="form-control" rows="5" required placeholder="Exemple:\nParacétamol 500mg — 1 comprimé, 3x/jour\nIbuprofène 200mg — 1 comprimé, si douleur"></textarea>
                        <div class="form-text">Vous pouvez saisir plusieurs lignes, elles seront affichées en liste.</div>
                    </div>

                    <div class="mb-3">
                        <label for="dosage" class="form-label">Dosage global (optionnel)</label>
                        <input type="text" id="dosage" name="dosage" class="form-control" placeholder="Ex: Pendant 5 jours après les repas">
                    </div>

                    <button type="submit" class="btn btn-success">Ajouter l'ordonnance</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des ordonnances -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            Ordonnances déjà rédigées
        </div>
        <div class="card-body">
            @if($ordonnances->isEmpty())
                <p>Aucune ordonnance pour le moment.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Médicaments / Instructions</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordonnances as $ordonnance)
                            <tr>
                                <td>{{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}</td>
                                <td>
                                    @php($text = $ordonnance->medicaments ?: $ordonnance->contenu)
                                    @if(!empty($text))
                                        @php($lines = preg_split("/(\r\n|\r|\n)/", $text))
                                        <ul class="mb-1">
                                            @foreach($lines as $ln)
                                                @if(trim($ln) !== '')
                                                    <li>{{ $ln }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        —
                                    @endif
                                    @if(!empty($ordonnance->dosage))
                                        <div class="small text-muted">Dosage: {{ $ordonnance->dosage }}</div>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ordonnance->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('medecin.ordonnances.edit', $ordonnance->id) }}" class="btn btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                        <a href="{{ route('medecin.ordonnances.download', $ordonnance->id) }}" class="btn btn-outline-secondary" title="Télécharger"><i class="bi bi-download"></i></a>
                                        <form method="POST" action="{{ route('medecin.ordonnances.resend', $ordonnance->id) }}" class="d-inline">
                                          @csrf
                                          <button type="submit" class="btn btn-outline-success" title="Renvoyer par e-mail"><i class="bi bi-envelope-arrow-up"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

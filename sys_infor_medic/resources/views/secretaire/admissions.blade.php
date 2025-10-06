@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>

    {{-- Boutons --}}
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> Retour vers le dashboard
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAdmissionModal">
            <i class="bi bi-plus-circle"></i> Ajouter une admission
        </button>
    </div>

    <div class="card shadow p-4">
        <h3 class="text-success mb-4">🏥 Historique des Admissions</h3>
        <p class="text-muted">Liste de toutes les admissions enregistrées.</p>

        <table class="table table-bordered table-hover mt-3">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Date d'Admission</th>
                    <th>Motif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admissions as $index => $admission)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $admission->patient->nom ?? '' }} {{ $admission->patient->prenom ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($admission->date_admission)->format('d/m/Y') }}</td>
                    <td>{{ $admission->motif }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAdmissionModal{{ $admission->id }}">
                            Voir / Modifier
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucune admission enregistrée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- Modal Ajouter Admission --}}
<div class="modal fade" id="addAdmissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Ajouter une admission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.storeAdmission') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">-- Sélectionner un patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'Admission</label>
                            <input type="date" name="date_admission" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Motif</label>
                            <textarea name="motif" class="form-control" rows="3" placeholder="Ex : Consultation générale..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modals pour modifier chaque admission --}}
@foreach($admissions as $admission)
<div class="modal fade" id="editAdmissionModal{{ $admission->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Modifier l'admission de {{ $admission->patient->nom ?? '' }} {{ $admission->patient->prenom ?? '' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('secretaire.updateAdmission', $admission->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Patient</label>
                            <select name="patient_id" class="form-select" required>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $admission->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->nom }} {{ $patient->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'Admission</label>
                            <input type="date" name="date_admission" class="form-control" value="{{ $admission->date_admission }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Motif</label>
                            <textarea name="motif" class="form-control" rows="3" required>{{ $admission->motif }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@extends('layouts.app')

@section('content')
<style>
    body > .container { max-width: 1500px !important; }
    .sidebar-sticky { position: sticky; top: 1rem; }
    .stats-card { transition: transform 0.2s; }
    .stats-card:hover { transform: translateY(-2px); }
    .badge-programmee { background-color: #007bff; }
    .badge-en_cours { background-color: #fd7e14; }
    .badge-terminee { background-color: #28a745; }
    .badge-annulee { background-color: #6c757d; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🧪 Analyses Médicales</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('medecin.analyses.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nouvelle Analyse
        </a>
        <a href="{{ route('medecin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour Dashboard
        </a>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card border-primary">
            <div class="card-body text-center">
                <h4 class="text-primary mb-1">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total analyses</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-info">
            <div class="card-body text-center">
                <h4 class="text-info mb-1">{{ $stats['ce_mois'] }}</h4>
                <small class="text-muted">Ce mois</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-warning">
            <div class="card-body text-center">
                <h4 class="text-warning mb-1">{{ $stats['en_attente'] ?? 0 }}</h4>
                <small class="text-muted">En attente</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-success">
            <div class="card-body text-center">
                <h4 class="text-success mb-1">{{ $stats['terminees'] }}</h4>
                <small class="text-muted">Terminées</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et Export -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtres et Export</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('medecin.analyses.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="patient_id" class="form-label">Patient</label>
                <select name="patient_id" id="patient_id" class="form-select">
                    <option value="">Tous les patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->nom }} {{ $patient->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="type_analyse" class="form-label">Type d'analyse</label>
                <input type="text" name="type_analyse" id="type_analyse" class="form-control" 
                       value="{{ request('type_analyse') }}" placeholder="ex: Hémogramme">
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label">Du</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" 
                       value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label">Au</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" 
                       value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
        
        <hr>
        
        <!-- Boutons d'export -->
        <div class="d-flex gap-2">
            <a href="{{ route('medecin.analyses.export.csv') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
               class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <a href="{{ route('medecin.analyses.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
               class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>
</div>

<!-- Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Liste des analyses -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">📋 Liste des Analyses ({{ $analyses->total() }} résultats)</h6>
    </div>
    <div class="card-body">
        @if($analyses->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-clipboard-x display-1 text-muted"></i>
                <p class="text-muted mt-2">Aucune analyse trouvée.</p>
                <a href="{{ route('medecin.analyses.create') }}" class="btn btn-primary">
                    Créer votre première analyse
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Type d'analyse</th>
                            <th>État</th>
                            <th>Résultats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analyses as $analyse)
                            <tr>
                                <td>
                                    <strong>{{ $analyse->date_analyse ? \Carbon\Carbon::parse($analyse->date_analyse)->format('d/m/Y') : '—' }}</strong><br>
                                    <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($analyse->patient)
                                        <div class="fw-semibold">{{ $analyse->patient->nom }} {{ $analyse->patient->prenom }}</div>
                                        <small class="text-muted">{{ $analyse->patient->telephone ?? '—' }}</small>
                                    @else
                                        <span class="text-muted">Patient non trouvé</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $analyse->type_analyse }}</span>
                                </td>
                                <td>
                                    @php
                                        $etats = [
                                            'programmee' => ['badge-programmee', '📅 Programmée'],
                                            'en_cours' => ['badge-en_cours', '⏳ En cours'],
                                            'terminee' => ['badge-terminee', '✅ Terminée'],
                                            'annulee' => ['badge-annulee', '❌ Annulée']
                                        ];
                                        $etat = $etats[$analyse->etat] ?? ['badge-secondary', $analyse->etat];
                                    @endphp
                                    <span class="badge {{ $etat[0] }} text-white">{{ $etat[1] }}</span>
                                </td>
                                <td>
                                    @if($analyse->resultats)
                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                            {{ Str::limit($analyse->resultats, 50) }}
                                        </div>
                                    @else
                                        <span class="text-muted">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('medecin.analyses.show', $analyse->id) }}" 
                                           class="btn btn-outline-primary" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('medecin.analyses.edit', $analyse->id) }}" 
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                title="Supprimer"
                                                onclick="confirmDelete({{ $analyse->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $analyses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette analyse ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(analyseId) {
    const form = document.getElementById('deleteForm');
    form.action = `/medecin/analyses/${analyseId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-actualisation des données toutes les 2 minutes
setInterval(() => {
    // Actualiser seulement les statistiques pour éviter de perdre la pagination
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(() => {
        // Optionnel: ajouter un indicateur de mise à jour
        console.log('Données actualisées');
    });
}, 120000); // 2 minutes
</script>
@endpush
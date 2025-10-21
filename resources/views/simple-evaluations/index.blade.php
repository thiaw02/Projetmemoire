@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        @if($user)
                            Évaluations de Dr. {{ $user->name }}
                        @else
                            Toutes les évaluations
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        @if($user)
                            Toutes les évaluations reçues par ce professionnel
                        @else
                            Liste de toutes les évaluations du système
                        @endif
                    </p>
                </div>
                @if(auth()->user()->role === 'patient')
                    <div>
                        <a href="{{ route('simple-evaluations.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nouvelle évaluation
                        </a>
                    </div>
                @endif
            </div>

            <!-- Statistiques du professionnel -->
            @if($user && $stats)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">{{ number_format($stats['average_rating'], 1) }}</div>
                                <div class="small">Note moyenne générale</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">{{ $stats['total_count'] }}</div>
                                <div class="small">Total évaluations</div>
                            </div>
                        </div>
                    </div>
                    @if($stats['medecin_rating'] > 0)
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <div class="display-6 fw-bold">{{ number_format($stats['medecin_rating'], 1) }}</div>
                                    <div class="small">Note médecin</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($stats['infirmier_rating'] > 0)
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <div class="display-6 fw-bold">{{ number_format($stats['infirmier_rating'], 1) }}</div>
                                    <div class="small">Note infirmier</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ request()->url() }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Rechercher dans les commentaires...">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous types</option>
                                <option value="medecin" {{ request('type') === 'medecin' ? 'selected' : '' }}>Médecin</option>
                                <option value="infirmier" {{ request('type') === 'infirmier' ? 'selected' : '' }}>Infirmier</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="note" class="form-label">Note</label>
                            <select class="form-select" id="note" name="note">
                                <option value="">Toutes notes</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('note') == $i ? 'selected' : '' }}>
                                        {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>
                                    Filtrer
                                </button>
                                <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($evaluations->count() > 0)
                <!-- Liste des évaluations -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Liste des évaluations ({{ $evaluations->total() }} résultat{{ $evaluations->total() > 1 ? 's' : '' }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @if(!$user)
                                            <th>Professionnel évalué</th>
                                        @endif
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Note</th>
                                        <th>Commentaire</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluations as $evaluation)
                                        <tr>
                                            @if(!$user)
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i class="bi bi-person-badge"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">Dr. {{ $evaluation->evaluatedUser->name }}</div>
                                                            @if($evaluation->evaluatedUser->specialite)
                                                                <small class="text-muted">{{ $evaluation->evaluatedUser->specialite }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $evaluation->patient->name }}</div>
                                                        <small class="text-muted">Patient</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $evaluation->type_evaluation === 'medecin' ? 'primary' : 'info' }}">
                                                    <i class="bi bi-{{ $evaluation->type_evaluation === 'medecin' ? 'person-check' : 'heart-pulse' }} me-1"></i>
                                                    {{ ucfirst($evaluation->type_evaluation) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {!! $evaluation->stars_html !!}
                                                    <span class="ms-2 fw-bold">{{ $evaluation->note }}/5</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($evaluation->commentaire)
                                                    <span class="text-truncate d-inline-block" style="max-width: 250px;" 
                                                          title="{{ $evaluation->commentaire }}">
                                                        {{ $evaluation->commentaire }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic">Aucun commentaire</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ $evaluation->created_at->format('d/m/Y') }}<br>
                                                    <span class="text-muted">{{ $evaluation->created_at->format('H:i') }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(auth()->user()->role === 'patient' && $evaluation->patient_id === auth()->id())
                                                        <a href="{{ route('simple-evaluations.show', $evaluation) }}" 
                                                           class="btn btn-outline-primary" 
                                                           title="Voir les détails">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @elseif(in_array(auth()->user()->role, ['admin', 'medecin', 'infirmier']))
                                                        <button class="btn btn-outline-info btn-sm" 
                                                                onclick="showEvaluationModal({{ json_encode($evaluation) }})"
                                                                title="Voir les détails">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                @if($evaluations->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $evaluations->appends(request()->query())->links() }}
                    </div>
                @endif

            @else
                <!-- Aucune évaluation -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-star display-1 text-muted"></i>
                    </div>
                    <h3 class="text-muted">Aucune évaluation trouvée</h3>
                    <p class="text-muted mb-4">
                        @if($user)
                            Ce professionnel n'a encore reçu aucune évaluation correspondant à vos critères.
                        @else
                            Aucune évaluation ne correspond à vos critères de recherche.
                        @endif
                    </p>
                    @if(auth()->user()->role === 'patient')
                        <a href="{{ route('simple-evaluations.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Donner une évaluation
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour afficher les détails d'évaluation (pour admin/professionnels) -->
<div class="modal fade" id="evaluationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="evaluationModalBody">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 2rem;
    height: 2rem;
    font-size: 0.875rem;
}
</style>

<script>
function showEvaluationModal(evaluation) {
    const modalBody = document.getElementById('evaluationModalBody');
    
    // Générer les étoiles
    let stars = '';
    for(let i = 1; i <= 5; i++) {
        if(i <= evaluation.note) {
            stars += '<i class="bi bi-star-fill text-warning"></i>';
        } else {
            stars += '<i class="bi bi-star text-muted"></i>';
        }
    }
    
    // Formater la date
    const date = new Date(evaluation.created_at);
    const formattedDate = date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Professionnel évalué</h6>
                <p><strong>Dr. ${evaluation.evaluated_user.name}</strong></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Patient</h6>
                <p>${evaluation.patient.name}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Type d'évaluation</h6>
                <p><span class="badge bg-${evaluation.type_evaluation === 'medecin' ? 'primary' : 'info'}">${evaluation.type_evaluation.charAt(0).toUpperCase() + evaluation.type_evaluation.slice(1)}</span></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Note</h6>
                <p>${stars} <strong>${evaluation.note}/5</strong></p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h6 class="text-muted">Date d'évaluation</h6>
                <p>${formattedDate}</p>
            </div>
        </div>
        ${evaluation.commentaire ? `
            <div class="row">
                <div class="col-12">
                    <h6 class="text-muted">Commentaire</h6>
                    <div class="bg-light p-3 rounded">
                        ${evaluation.commentaire}
                    </div>
                </div>
            </div>
        ` : ''}
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('evaluationModal'));
    modal.show();
}
</script>
@endsection
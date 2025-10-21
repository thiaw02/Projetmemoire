@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary me-3" title="Retour au dashboard">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Retour</span>
                    </a>
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>
                            Mes évaluations
                        </h1>
                        <p class="text-muted mb-0">Toutes les évaluations que vous avez données</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('simple-evaluations.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nouvelle évaluation
                    </a>
                </div>
            </div>

            @if($evaluations->count() > 0)
                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">{{ $evaluations->total() }}</div>
                                <div class="small">Évaluations données</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">{{ number_format($evaluations->avg('note'), 1) }}</div>
                                <div class="small">Note moyenne donnée</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">
                                    {{ $evaluations->where('type_evaluation', 'medecin')->count() }}
                                </div>
                                <div class="small">Évaluations médecins</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold">
                                    {{ $evaluations->where('type_evaluation', 'infirmier')->count() }}
                                </div>
                                <div class="small">Évaluations infirmiers</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des évaluations -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Liste de vos évaluations
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Professionnel</th>
                                        <th>Type</th>
                                        <th>Note</th>
                                        <th>Commentaire</th>
                                        <th>Date</th>
                                        <th>Consultation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluations as $evaluation)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">Dr. {{ $evaluation->evaluatedUser->name }}</div>
                                                        @if($evaluation->evaluatedUser->specialite)
                                                            <small class="text-muted">{{ $evaluation->evaluatedUser->specialite }}</small>
                                                        @endif
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
                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
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
                                                @if($evaluation->consultation)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Liée
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('simple-evaluations.show', $evaluation) }}" 
                                                       class="btn btn-outline-primary" 
                                                       title="Voir les détails">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('simple-evaluations.professional', $evaluation->evaluatedUser) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Voir toutes les évaluations de ce professionnel">
                                                        <i class="bi bi-person-lines-fill"></i>
                                                    </a>
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
                        {{ $evaluations->links() }}
                    </div>
                @endif

            @else
                <!-- Aucune évaluation -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-star display-1 text-muted"></i>
                    </div>
                    <h3 class="text-muted">Aucune évaluation</h3>
                    <p class="text-muted mb-4">
                        Vous n'avez encore donné aucune évaluation.<br>
                        Partagez votre expérience avec les professionnels de santé !
                    </p>
                    <a href="{{ route('simple-evaluations.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        Donner ma première évaluation
                    </a>
                </div>
            @endif
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
@endsection
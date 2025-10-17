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
                        Détails de l'évaluation
                    </h1>
                    <p class="text-muted mb-0">Évaluation #{{ $evaluation->id }}</p>
                </div>
                <div>
                    <a href="{{ route('simple-evaluations.my-evaluations') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Mes évaluations
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informations principales -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Informations de l'évaluation
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Professionnel évalué</h6>
                                    <p class="mb-3">
                                        <i class="bi bi-person-badge me-2"></i>
                                        <strong>Dr. {{ $evaluation->evaluatedUser->name }}</strong><br>
                                        <small class="text-muted">
                                            {{ ucfirst($evaluation->type_evaluation) }}
                                            @if($evaluation->evaluatedUser->specialite)
                                                - {{ $evaluation->evaluatedUser->specialite }}
                                            @endif
                                        </small>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Note attribuée</h6>
                                    <p class="mb-3">
                                        {!! $evaluation->stars_html !!}
                                        <span class="ms-2 fs-5 fw-bold">{{ $evaluation->note }}/5</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Date d'évaluation</h6>
                                    <p class="mb-3">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        {{ $evaluation->created_at->format('d/m/Y à H:i') }}
                                        <small class="text-muted">
                                            ({{ $evaluation->created_at->diffForHumans() }})
                                        </small>
                                    </p>
                                </div>
                                @if($evaluation->consultation)
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Consultation liée</h6>
                                        <p class="mb-3">
                                            <i class="bi bi-calendar-check me-2"></i>
                                            {{ $evaluation->consultation->date_consultation->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($evaluation->commentaire)
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-2">Commentaire</h6>
                                        <div class="bg-light p-3 rounded">
                                            <i class="bi bi-quote text-muted me-2"></i>
                                            {{ $evaluation->commentaire }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistiques du professionnel -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Statistiques du professionnel
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $professionalStats = [
                                    'average_rating' => \App\Models\Evaluation::averageRatingForProfessional($evaluation->evaluated_user_id),
                                    'total_count' => \App\Models\Evaluation::countForProfessional($evaluation->evaluated_user_id),
                                    'medecin_rating' => \App\Models\Evaluation::averageRatingForProfessional($evaluation->evaluated_user_id, 'medecin'),
                                    'infirmier_rating' => \App\Models\Evaluation::averageRatingForProfessional($evaluation->evaluated_user_id, 'infirmier'),
                                ];
                            @endphp
                            
                            <div class="text-center mb-4">
                                <div class="display-4 text-warning fw-bold">
                                    {{ number_format($professionalStats['average_rating'], 1) }}
                                </div>
                                <div class="text-muted">Note moyenne générale</div>
                                <div class="mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($professionalStats['average_rating']))
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    Basé sur {{ $professionalStats['total_count'] }} évaluation(s)
                                </small>
                            </div>
                            
                            @if($professionalStats['medecin_rating'] > 0)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">En tant que médecin</span>
                                        <span class="badge bg-success">
                                            {{ number_format($professionalStats['medecin_rating'], 1) }}/5
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ ($professionalStats['medecin_rating'] / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($professionalStats['infirmier_rating'] > 0)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">En tant qu'infirmier</span>
                                        <span class="badge bg-info">
                                            {{ number_format($professionalStats['infirmier_rating'], 1) }}/5
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-info" 
                                             style="width: {{ ($professionalStats['infirmier_rating'] / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('simple-evaluations.professional', $evaluation->evaluatedUser) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>
                                    Voir toutes ses évaluations
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Actions disponibles</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('simple-evaluations.create') }}" 
                                   class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Nouvelle évaluation
                                </a>
                                
                                @if(auth()->user()->role === 'patient' && $evaluation->patient_id === auth()->id())
                                    <button class="btn btn-outline-danger" 
                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')) { /* Ajouter logique de suppression */ }">
                                        <i class="bi bi-trash me-1"></i>
                                        Supprimer
                                    </button>
                                @endif
                                
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Retour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
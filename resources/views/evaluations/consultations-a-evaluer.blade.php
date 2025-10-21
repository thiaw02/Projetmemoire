@extends('layouts.app')

@section('title', 'Consultations à évaluer')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-star-half-alt me-2 text-primary"></i>
                Consultations à évaluer
            </h1>
            <p class="text-muted mb-0">Évaluez vos consultations récentes pour nous aider à améliorer notre service</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary fs-6">{{ count($consultations) }} consultation(s) à évaluer</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($consultations) > 0)
        <div class="row">
            @foreach($consultations as $consultation)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 position-relative">
                        <!-- Badge nouveau -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-exclamation me-1"></i>
                                À évaluer
                            </span>
                        </div>

                        <div class="card-body p-4">
                            <!-- En-tête de la consultation -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-circle bg-primary text-white">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0">Dr. {{ $consultation->medecin->name }}</h5>
                                    <small class="text-muted">{{ $consultation->medecin->specialite ?? 'Médecine générale' }}</small>
                                </div>
                            </div>

                            <!-- Détails de la consultation -->
                            <div class="consultation-details mb-3">
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <p class="mb-2">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <strong>Date :</strong>
                                        </p>
                                        <p class="ms-4 text-muted small">
                                            {{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-2">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <strong>Heure :</strong>
                                        </p>
                                        <p class="ms-4 text-muted small">
                                            {{ \Carbon\Carbon::parse($consultation->date_consultation)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                @if($consultation->diagnostic)
                                    <div class="mt-3">
                                        <p class="mb-2">
                                            <i class="fas fa-diagnoses text-primary me-2"></i>
                                            <strong>Diagnostic :</strong>
                                        </p>
                                        <p class="ms-4 text-muted small">
                                            {{ Str::limit($consultation->diagnostic, 80) }}
                                        </p>
                                    </div>
                                @endif

                                @if($consultation->traitement)
                                    <div class="mt-3">
                                        <p class="mb-2">
                                            <i class="fas fa-pills text-primary me-2"></i>
                                            <strong>Traitement :</strong>
                                        </p>
                                        <p class="ms-4 text-muted small">
                                            {{ Str::limit($consultation->traitement, 80) }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Temps restant pour évaluer -->
                            <div class="time-remaining mb-3">
                                @php
                                    $daysLeft = \Carbon\Carbon::parse($consultation->date_consultation)->diffInDays(now()->addMonths(3));
                                    $timeLeft = 90 - \Carbon\Carbon::parse($consultation->date_consultation)->diffInDays(now());
                                @endphp
                                
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <small>
                                        @if($timeLeft > 0)
                                            <strong>{{ $timeLeft }} jours restants</strong> pour évaluer cette consultation
                                        @else
                                            Délai d'évaluation expiré
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <!-- Bouton d'évaluation -->
                            <div class="text-center">
                                @if($timeLeft > 0)
                                    <a href="{{ route('patient.evaluation.creer', $consultation->id) }}" 
                                       class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-star me-2"></i>
                                        Évaluer cette consultation
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="fas fa-clock me-2"></i>
                                        Délai d'évaluation expiré
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Pied de carte avec statut -->
                        <div class="card-footer bg-light border-0 py-2">
                            <small class="text-muted d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Consultation terminée
                                </span>
                                <span>
                                    Il y a {{ \Carbon\Carbon::parse($consultation->date_consultation)->diffForHumans() }}
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Informations utiles -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Pourquoi évaluer vos consultations ?
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        Améliorer la qualité des soins
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        Aider d'autres patients dans leur choix
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        Permettre aux médecins de s'améliorer
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        Contribuer à l'excellence médicale
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Attention :</strong> Vous disposez de <strong>3 mois</strong> après votre consultation pour la évaluer.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Aucune consultation à évaluer -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted mb-3">Aucune consultation à évaluer</h3>
                    <p class="text-muted mb-4">
                        Vous avez évalué toutes vos consultations récentes ou vous n'avez pas de consultation terminée récente.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('patient.consultations') }}" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i>
                            Voir mes consultations
                        </a>
                        <a href="{{ route('patient.rendezvous') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Prendre un rendez-vous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.consultation-details .row > div {
    border-right: 1px solid #e3e6f0;
}

.consultation-details .row > div:last-child {
    border-right: none;
}

.text-sm {
    font-size: 0.875rem;
}

.time-remaining .alert {
    border-radius: 8px;
}

@media (max-width: 768px) {
    .consultation-details .row > div {
        border-right: none;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .consultation-details .row > div:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
}
</style>
@endsection
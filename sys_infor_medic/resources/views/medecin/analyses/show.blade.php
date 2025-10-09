@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🔍 Détails de l'Analyse</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('medecin.analyses.edit', $analyse->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        <a href="{{ route('medecin.analyses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-clipboard-data"></i> Informations de l'analyse</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Type d'analyse</label>
                        <div class="form-control-plaintext fs-5 fw-semibold">
                            {{ $analyse->type_analyse }}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Date d'analyse</label>
                        <div class="form-control-plaintext">
                            @if($analyse->date_analyse)
                                <span class="fs-5">{{ \Carbon\Carbon::parse($analyse->date_analyse)->format('d/m/Y') }}</span><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($analyse->date_analyse)->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">État</label>
                        <div class="form-control-plaintext">
                            @php
                                $etats = [
                                    'programmee' => ['badge bg-primary', '📅 Programmée'],
                                    'en_cours' => ['badge bg-warning text-dark', '⏳ En cours'],
                                    'terminee' => ['badge bg-success', '✅ Terminée'],
                                    'annulee' => ['badge bg-secondary', '❌ Annulée']
                                ];
                                $etatInfo = $etats[$analyse->etat] ?? ['badge bg-light text-dark', $analyse->etat];
                            @endphp
                            <span class="{{ $etatInfo[0] }} fs-6">{{ $etatInfo[1] }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Médecin prescripteur</label>
                        <div class="form-control-plaintext">
                            @if($analyse->medecin)
                                <span class="fw-semibold">Dr {{ $analyse->medecin->name }}</span><br>
                                @if($analyse->medecin->email)
                                    <small class="text-muted">{{ $analyse->medecin->email }}</small>
                                @endif
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Résultats -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted">Résultats</label>
                        <div class="form-control-plaintext">
                            @if($analyse->resultats)
                                <div class="border rounded p-3 bg-light">
                                    <pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;">{{ $analyse->resultats }}</pre>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Les résultats ne sont pas encore disponibles.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Métadonnées -->
                <div class="row text-muted">
                    <div class="col-md-6">
                        <small>
                            <i class="bi bi-calendar-plus"></i> Créée le : {{ $analyse->created_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>
                            <i class="bi bi-pencil"></i> Modifiée le : {{ $analyse->updated_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informations patient -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-person-circle"></i> Patient</h6>
            </div>
            <div class="card-body">
                @if($analyse->patient)
                    <div class="text-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-fill text-success fs-2"></i>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <h5 class="fw-bold mb-1">{{ $analyse->patient->nom }} {{ $analyse->patient->prenom }}</h5>
                        @if($analyse->patient->date_naissance)
                            <small class="text-muted">
                                Né(e) le {{ \Carbon\Carbon::parse($analyse->patient->date_naissance)->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($analyse->patient->date_naissance)->age }} ans)
                            </small>
                        @endif
                    </div>
                    
                    <div class="small">
                        @if($analyse->patient->telephone)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone text-muted me-2"></i>
                                <span>{{ $analyse->patient->telephone }}</span>
                            </div>
                        @endif
                        
                        @if($analyse->patient->email)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-muted me-2"></i>
                                <span>{{ $analyse->patient->email }}</span>
                            </div>
                        @endif
                        
                        @if($analyse->patient->groupe_sanguin)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-droplet text-danger me-2"></i>
                                <span>Groupe : {{ $analyse->patient->groupe_sanguin }}</span>
                            </div>
                        @endif
                        
                        @if($analyse->patient->adresse)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-house text-muted me-2"></i>
                                <span>{{ $analyse->patient->adresse }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('medecin.patients.show', ['patientId' => $analyse->patient->id]) }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-folder2-open"></i> Ouvrir le dossier complet
                        </a>
                        <a href="{{ route('medecin.consultations', ['patient_id' => $analyse->patient->id]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-clipboard-plus"></i> Nouvelle consultation
                        </a>
                        <a href="{{ route('medecin.analyses.create', ['patient_id' => $analyse->patient->id]) }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="bi bi-plus-circle"></i> Nouvelle analyse
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Patient introuvable
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-lightning"></i> Actions rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($analyse->etat != 'terminee')
                        <form method="POST" action="{{ route('medecin.analyses.update', $analyse->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="patient_id" value="{{ $analyse->patient_id }}">
                            <input type="hidden" name="type_analyse" value="{{ $analyse->type_analyse }}">
                            <input type="hidden" name="date_analyse" value="{{ $analyse->date_analyse }}">
                            <input type="hidden" name="resultats" value="{{ $analyse->resultats }}">
                            <input type="hidden" name="etat" value="terminee">
                            <button type="submit" class="btn btn-success btn-sm w-100" 
                                    onclick="return confirm('Marquer cette analyse comme terminée ?')">
                                <i class="bi bi-check-circle"></i> Marquer terminée
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            onclick="confirmDelete()">
                        <i class="bi bi-trash"></i> Supprimer l'analyse
                    </button>
                    
                    <a href="{{ route('medecin.analyses.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list"></i> Toutes les analyses
                    </a>
                </div>
            </div>
        </div>
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
                <p>Êtes-vous sûr de vouloir supprimer cette analyse ?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Attention :</strong> Cette action est irréversible. Toutes les informations associées à cette analyse seront définitivement perdues.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('medecin.analyses.destroy', $analyse->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-refresh des données toutes les 2 minutes si l'analyse est en cours
@if($analyse->etat === 'en_cours')
setInterval(() => {
    window.location.reload();
}, 120000); // 2 minutes
@endif
</script>
@endpush
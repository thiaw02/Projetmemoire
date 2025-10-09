@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🔍 Détails du Dossier</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('dossier.edit', $dossier->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        <a href="{{ route('dossier.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<div class="row">
    <!-- Informations principales du dossier -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-clipboard-data"></i> Informations du dossier</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Statut</label>
                        <div class="form-control-plaintext">
                            @php
                                $statusConfig = [
                                    'En cours' => ['badge bg-primary', '🔄 En cours'],
                                    'Urgent' => ['badge bg-danger', '🚨 Urgent'],
                                    'Terminé' => ['badge bg-success', '✅ Terminé'],
                                    'En attente' => ['badge bg-warning text-dark', '⏳ En attente'],
                                    'Suivi nécessaire' => ['badge bg-info', '👁️ Suivi nécessaire']
                                ];
                                $statusInfo = $statusConfig[$dossier->statut] ?? ['badge bg-secondary', $dossier->statut];
                            @endphp
                            <span class="{{ $statusInfo[0] }} fs-6">{{ $statusInfo[1] }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Dernière mise à jour</label>
                        <div class="form-control-plaintext">
                            <span class="fs-6">{{ $dossier->updated_at->format('d/m/Y à H:i') }}</span><br>
                            <small class="text-muted">{{ $dossier->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    <!-- Observations -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted">Observations</label>
                        <div class="form-control-plaintext">
                            @if($dossier->observation)
                                <div class="border rounded p-3 bg-light">
                                    <pre class="mb-0" style="font-family: inherit; white-space: pre-wrap; word-wrap: break-word;">{{ $dossier->observation }}</pre>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Aucune observation enregistrée.
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
                            <i class="bi bi-calendar-plus"></i> Créé le : {{ $dossier->created_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>
                            <i class="bi bi-hash"></i> ID: {{ $dossier->id }}
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
                @if($dossier->patient)
                    <div class="text-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-fill text-success fs-2"></i>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <h5 class="fw-bold mb-1">{{ $dossier->patient->nom }} {{ $dossier->patient->prenom }}</h5>
                        @if($dossier->patient->date_naissance)
                            <small class="text-muted">
                                Né(e) le {{ \Carbon\Carbon::parse($dossier->patient->date_naissance)->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($dossier->patient->date_naissance)->age }} ans)
                            </small>
                        @endif
                    </div>
                    
                    <div class="small">
                        @if($dossier->patient->telephone)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone text-muted me-2"></i>
                                <span>{{ $dossier->patient->telephone }}</span>
                            </div>
                        @endif
                        
                        @if($dossier->patient->email)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-muted me-2"></i>
                                <span>{{ $dossier->patient->email }}</span>
                            </div>
                        @endif
                        
                        @if($dossier->patient->groupe_sanguin)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-droplet text-danger me-2"></i>
                                <span>Groupe : {{ $dossier->patient->groupe_sanguin }}</span>
                            </div>
                        @endif
                        
                        @if($dossier->patient->adresse)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-house text-muted me-2"></i>
                                <span>{{ $dossier->patient->adresse }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('suivi.create') }}?patient_id={{ $dossier->patient->id }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="bi bi-clipboard-plus"></i> Nouveau suivi
                        </a>
                        <a href="{{ route('dossier.create') }}?patient_id={{ $dossier->patient->id }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Nouveau dossier
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
                    @if($dossier->statut !== 'Terminé')
                        <form method="POST" action="{{ route('dossier.update', $dossier->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="patient_id" value="{{ $dossier->patient_id }}">
                            <input type="hidden" name="observation" value="{{ $dossier->observation }}">
                            <input type="hidden" name="statut" value="Terminé">
                            <button type="submit" class="btn btn-success btn-sm w-100" 
                                    onclick="return confirm('Marquer ce dossier comme terminé ?')">
                                <i class="bi bi-check-circle"></i> Marquer terminé
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Dossier terminé
                        </div>
                    @endif
                    
                    @if($dossier->statut !== 'Urgent')
                        <form method="POST" action="{{ route('dossier.update', $dossier->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="patient_id" value="{{ $dossier->patient_id }}">
                            <input type="hidden" name="observation" value="{{ $dossier->observation }}">
                            <input type="hidden" name="statut" value="Urgent">
                            <button type="submit" class="btn btn-danger btn-sm w-100" 
                                    onclick="return confirm('Marquer ce dossier comme urgent ?')">
                                <i class="bi bi-exclamation-triangle"></i> Marquer urgent
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            onclick="confirmDelete()">
                        <i class="bi bi-trash"></i> Supprimer le dossier
                    </button>
                    
                    <a href="{{ route('dossier.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list"></i> Tous les dossiers
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
                <p>Êtes-vous sûr de vouloir supprimer ce dossier ?</p>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Attention :</strong> Cette action est irréversible. Toutes les observations de ce dossier seront définitivement perdues.
                </div>
                @if($dossier->patient)
                    <p><strong>Patient concerné :</strong> {{ $dossier->patient->nom }} {{ $dossier->patient->prenom }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('dossier.destroy', $dossier->id) }}" style="display: inline;">
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

// Auto-refresh si le dossier est marqué comme urgent ou en cours
@if(in_array($dossier->statut, ['Urgent', 'En cours']))
setInterval(() => {
    // Recharger la page toutes les 2 minutes pour les dossiers actifs
    window.location.reload();
}, 120000); // 2 minutes
@endif

// Notification du statut
document.addEventListener('DOMContentLoaded', function() {
    const statut = '{{ $dossier->statut }}';
    
    if (statut === 'Urgent') {
        // Faire clignoter subtilement l'élément urgent
        const urgentBadge = document.querySelector('.badge.bg-danger');
        if (urgentBadge) {
            setInterval(() => {
                urgentBadge.style.opacity = urgentBadge.style.opacity === '0.5' ? '1' : '0.5';
            }, 1000);
        }
    }
});
</script>
@endpush
@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>✏️ Modifier le Dossier</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('dossier.show', $dossier->id) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i> Voir détails
        </a>
        <a href="{{ route('dossier.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<!-- Messages d'erreur -->
@if ($errors->any())
    <div class="alert alert-danger">
        <h6><i class="bi bi-exclamation-triangle"></i> Erreurs de validation :</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Modification du dossier</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('dossier.update', $dossier->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <!-- Sélection du patient -->
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Sélectionnez un patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" 
                                {{ (old('patient_id') ?? $dossier->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                                @if($patient->telephone)
                                    - {{ $patient->telephone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Statut -->
                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select" required>
                        <option value="">Choisir un statut</option>
                        <option value="En cours" {{ (old('statut') ?? $dossier->statut) == 'En cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="Urgent" {{ (old('statut') ?? $dossier->statut) == 'Urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                        <option value="Terminé" {{ (old('statut') ?? $dossier->statut) == 'Terminé' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="En attente" {{ (old('statut') ?? $dossier->statut) == 'En attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="Suivi nécessaire" {{ (old('statut') ?? $dossier->statut) == 'Suivi nécessaire' ? 'selected' : '' }}>👁️ Suivi nécessaire</option>
                    </select>
                </div>
                
                <!-- Observation -->
                <div class="col-12">
                    <label for="observation" class="form-label">Observation <span class="text-danger">*</span></label>
                    <textarea name="observation" id="observation" class="form-control" rows="6" 
                              placeholder="Décrivez les observations, soins prodigués, symptômes constatés..." required>{{ old('observation') ?? $dossier->observation }}</textarea>
                    <div class="form-text">
                        <i class="bi bi-info-circle"></i> Mettez à jour les observations selon l'évolution du patient
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Informations sur le dossier -->
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Patient actuel</h6>
                    @if($dossier->patient)
                        <div class="alert alert-info">
                            <strong>{{ $dossier->patient->nom }} {{ $dossier->patient->prenom }}</strong><br>
                            @if($dossier->patient->telephone)
                                <small>Tél: {{ $dossier->patient->telephone }}</small><br>
                            @endif
                            @if($dossier->patient->email)
                                <small>Email: {{ $dossier->patient->email }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">Patient non trouvé</div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary">Historique</h6>
                    <div class="small text-muted">
                        <div><i class="bi bi-calendar-plus"></i> Créé le : {{ $dossier->created_at->format('d/m/Y à H:i') }}</div>
                        <div><i class="bi bi-pencil"></i> Dernière modification : {{ $dossier->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- Suggestions d'observations -->
            <div class="row">
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="bi bi-lightbulb"></i> Ajouter à l'observation :</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary">Évolution</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('\n\n--- Mise à jour du ' + new Date().toLocaleDateString('fr-FR') + ' ---\n')">Nouvelle entrée</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Amélioration constatée. ')">Amélioration</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('État stable. ')">État stable</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Surveillance renforcée. ')">Surveillance</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">Nouveaux soins</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Contrôle effectué. ')">Contrôle</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Médication ajustée. ')">Ajustement</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Rééducation poursuivie. ')">Rééducation</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Consultation programmée. ')">Consultation</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">Fin de suivi</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Objectifs atteints. ')">Objectifs atteints</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Patient autonome. ')">Autonomie</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Sortie recommandée. ')">Sortie</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Transfert prévu. ')">Transfert</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dossier.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Sauvegarder les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function addToObservation(text) {
    const textarea = document.getElementById('observation');
    const currentValue = textarea.value;
    
    // Ajouter le texte à la fin
    const newValue = currentValue + text;
    textarea.value = newValue;
    textarea.focus();
    
    // Placer le curseur à la fin
    textarea.setSelectionRange(newValue.length, newValue.length);
}

document.addEventListener('DOMContentLoaded', function() {
    // Améliorer l'UX des suggestions
    document.querySelectorAll('.suggestion').forEach(badge => {
        badge.style.cursor = 'pointer';
        
        badge.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#0d6efd';
            this.style.color = 'white';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.color = '';
        });
    });
    
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const patientId = document.getElementById('patient_id').value;
        const statut = document.getElementById('statut').value;
        const observation = document.getElementById('observation').value.trim();
        
        if (!patientId || !statut || !observation) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }
        
        if (observation.length < 10) {
            e.preventDefault();
            alert('L\'observation doit contenir au moins 10 caractères.');
            document.getElementById('observation').focus();
            return;
        }
    });
    
    // Compteur de caractères
    const observationTextarea = document.getElementById('observation');
    const counter = document.createElement('div');
    counter.className = 'form-text text-end';
    counter.style.marginTop = '5px';
    observationTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = observationTextarea.value.length;
        counter.textContent = `${length}/1000 caractères`;
        counter.className = `form-text text-end ${length > 900 ? 'text-warning' : length > 950 ? 'text-danger' : ''}`;
    }
    
    observationTextarea.addEventListener('input', updateCounter);
    updateCounter();
    
    // Avertissement si le statut change vers "Terminé"
    const statutSelect = document.getElementById('statut');
    const originalStatut = statutSelect.value;
    
    statutSelect.addEventListener('change', function() {
        if (this.value === 'Terminé' && originalStatut !== 'Terminé') {
            if (!confirm('Vous allez marquer ce dossier comme terminé. Êtes-vous sûr ?')) {
                this.value = originalStatut;
            }
        }
    });
});
</script>
@endpush
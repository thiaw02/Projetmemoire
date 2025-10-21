@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>📝 Nouveau Dossier Patient</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('dossier.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
        <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-primary">
            <i class="bi bi-house"></i> Dashboard
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
    <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-clipboard-plus"></i> Informations du nouveau dossier</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('dossier.store') }}">
            @csrf
            
            <div class="row g-3">
                <!-- Sélection du patient -->
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Sélectionnez un patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                                @if($patient->telephone)
                                    - {{ $patient->telephone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Choisissez le patient pour ce dossier</div>
                </div>
                
                <!-- Statut -->
                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select" required>
                        <option value="">Choisir un statut</option>
                        <option value="En cours" {{ old('statut') == 'En cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="Urgent" {{ old('statut') == 'Urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                        <option value="Terminé" {{ old('statut') == 'Terminé' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="En attente" {{ old('statut') == 'En attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="Suivi nécessaire" {{ old('statut') == 'Suivi nécessaire' ? 'selected' : '' }}>👁️ Suivi nécessaire</option>
                    </select>
                </div>
                
                <!-- Observation -->
                <div class="col-12">
                    <label for="observation" class="form-label">Observation <span class="text-danger">*</span></label>
                    <textarea name="observation" id="observation" class="form-control" rows="6" 
                              placeholder="Décrivez les observations, soins prodigués, symptômes constatés..." required>{{ old('observation') }}</textarea>
                    <div class="form-text">
                        <i class="bi bi-info-circle"></i> Soyez précis dans vos observations : symptômes, soins prodigués, évolution, etc.
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Suggestions d'observations -->
            <div class="row">
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="bi bi-lightbulb"></i> Suggestions d'observations courantes :</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary">Signes vitaux</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Température prise : ')">Température</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Tension artérielle : ')">Tension</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Fréquence cardiaque : ')">FC</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Saturation O2 : ')">SpO2</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">Soins prodigués</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Pansement réalisé sur ')">Pansement</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Injection administrée : ')">Injection</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Médication donnée : ')">Médicament</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Toilette réalisée')">Toilette</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">Observations</h6>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Patient conscient et orienté. ')">Conscient</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Douleur évaluée à /10. ')">Douleur</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Mobilisation encouragée. ')">Mobilisation</span>
                                <span class="badge bg-light text-dark border suggestion" onclick="addToObservation('Hydratation surveillée. ')">Hydratation</span>
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
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Créer le dossier
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
    
    // Ajouter le texte à la fin avec un retour à la ligne si nécessaire
    const newValue = currentValue + (currentValue ? '\n' : '') + text;
    textarea.value = newValue;
    textarea.focus();
    
    // Placer le curseur à la fin
    textarea.setSelectionRange(newValue.length, newValue.length);
}

// Améliorer l'UX avec les suggestions
document.addEventListener('DOMContentLoaded', function() {
    // Rendre les suggestions cliquables visuellement
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
    
    // Validation en temps réel
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
            alert('L\'observation doit contenir au moins 10 caractères pour être utile.');
            document.getElementById('observation').focus();
            return;
        }
    });
    
    // Compteur de caractères pour l'observation
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
    updateCounter(); // Initial call
});
</script>
@endpush
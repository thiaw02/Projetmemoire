@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🧪 Nouvelle Analyse Médicale</h3>
    <a href="{{ route('medecin.analyses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour aux analyses
    </a>
</div>

<!-- Messages d'erreur -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-clipboard-plus"></i> Informations de l'analyse</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('medecin.analyses.store') }}">
            @csrf
            
            <div class="row g-3">
                <!-- Patient -->
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
                </div>
                
                <!-- Date d'analyse -->
                <div class="col-md-6">
                    <label for="date_analyse" class="form-label">Date d'analyse <span class="text-danger">*</span></label>
                    <input type="date" name="date_analyse" id="date_analyse" class="form-control" 
                           value="{{ old('date_analyse', date('Y-m-d')) }}" required>
                </div>
                
                <!-- Type d'analyse avec suggestions -->
                <div class="col-md-8">
                    <label for="type_analyse" class="form-label">Type d'analyse <span class="text-danger">*</span></label>
                    <input type="text" name="type_analyse" id="type_analyse" class="form-control" 
                           value="{{ old('type_analyse') }}" 
                           placeholder="Ex: Hémogramme complet, Glycémie, etc."
                           list="suggestions_analyses" required>
                    
                    <!-- Datalist avec suggestions -->
                    <datalist id="suggestions_analyses">
                        @foreach($typesAnalyses as $categorie => $types)
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ $categorie }} - {{ $type }}</option>
                            @endforeach
                        @endforeach
                    </datalist>
                </div>
                
                <!-- État -->
                <div class="col-md-4">
                    <label for="etat" class="form-label">État <span class="text-danger">*</span></label>
                    <select name="etat" id="etat" class="form-select" required>
                        <option value="programmee" {{ old('etat') == 'programmee' ? 'selected' : '' }}>📅 Programmée</option>
                        <option value="en_cours" {{ old('etat') == 'en_cours' ? 'selected' : '' }}>⏳ En cours</option>
                        <option value="terminee" {{ old('etat') == 'terminee' ? 'selected' : '' }}>✅ Terminée</option>
                        <option value="annulee" {{ old('etat') == 'annulee' ? 'selected' : '' }}>❌ Annulée</option>
                    </select>
                </div>
                
                <!-- Résultats -->
                <div class="col-12">
                    <label for="resultats" class="form-label">Résultats</label>
                    <textarea name="resultats" id="resultats" class="form-control" rows="4" 
                              placeholder="Saisissez les résultats de l'analyse...">{{ old('resultats') }}</textarea>
                    <div class="form-text">Laissez vide si les résultats ne sont pas encore disponibles.</div>
                </div>
                
                <!-- Remarques -->
                <div class="col-12">
                    <label for="remarques" class="form-label">Remarques</label>
                    <textarea name="remarques" id="remarques" class="form-control" rows="3" 
                              placeholder="Remarques ou instructions particulières...">{{ old('remarques') }}</textarea>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('medecin.analyses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Créer l'analyse
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Suggestions par catégorie -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Suggestions d'analyses par catégorie</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($typesAnalyses as $categorie => $types)
                <div class="col-md-4 mb-3">
                    <h6 class="text-primary">{{ $categorie }}</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($types as $type)
                            <span class="badge bg-light text-dark border suggestion-badge" 
                                  style="cursor: pointer;" 
                                  onclick="selectAnalyse('{{ $type }}')">
                                {{ $type }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Fonction pour sélectionner une analyse depuis les suggestions
function selectAnalyse(type) {
    document.getElementById('type_analyse').value = type;
    document.getElementById('type_analyse').focus();
}

// Améliorer l'expérience utilisateur avec les suggestions
document.addEventListener('DOMContentLoaded', function() {
    // Auto-complétion améliorée
    const typeAnalyse = document.getElementById('type_analyse');
    const suggestions = document.getElementById('suggestions_analyses');
    
    typeAnalyse.addEventListener('input', function() {
        // Ici on pourrait ajouter une logique d'auto-complétion plus avancée
    });
    
    // Validation en temps réel
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const patientId = document.getElementById('patient_id').value;
        const typeAnalyseValue = document.getElementById('type_analyse').value;
        const dateAnalyse = document.getElementById('date_analyse').value;
        
        if (!patientId || !typeAnalyseValue || !dateAnalyse) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }
        
        // Vérifier que la date n'est pas trop ancienne (plus de 1 an)
        const dateSelected = new Date(dateAnalyse);
        const oneYearAgo = new Date();
        oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
        
        if (dateSelected < oneYearAgo) {
            if (!confirm('La date sélectionnée est très ancienne (plus d\'un an). Voulez-vous continuer ?')) {
                e.preventDefault();
                return;
            }
        }
    });
    
    // Style pour les suggestions cliquables
    const suggestionBadges = document.querySelectorAll('.suggestion-badge');
    suggestionBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#0d6efd';
            this.style.color = 'white';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.color = '';
        });
    });
});
</script>
@endpush
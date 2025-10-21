@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>✏️ Modifier l'Analyse</h3>
    <div class="btn-group" role="group">
        <a href="{{ route('medecin.analyses.show', $analyse->id) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i> Voir détails
        </a>
        <a href="{{ route('medecin.analyses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour aux analyses
        </a>
    </div>
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
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Modification de l'analyse</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('medecin.analyses.update', $analyse->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <!-- Patient -->
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Sélectionnez un patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" 
                                {{ (old('patient_id') ?? $analyse->patient_id) == $patient->id ? 'selected' : '' }}>
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
                           value="{{ old('date_analyse') ?? ($analyse->date_analyse ? \Carbon\Carbon::parse($analyse->date_analyse)->format('Y-m-d') : '') }}" required>
                </div>
                
                <!-- Type d'analyse -->
                <div class="col-md-8">
                    <label for="type_analyse" class="form-label">Type d'analyse <span class="text-danger">*</span></label>
                    <input type="text" name="type_analyse" id="type_analyse" class="form-control" 
                           value="{{ old('type_analyse') ?? $analyse->type_analyse }}" 
                           placeholder="Ex: Hémogramme complet, Glycémie, etc." required>
                </div>
                
                <!-- État -->
                <div class="col-md-4">
                    <label for="etat" class="form-label">État <span class="text-danger">*</span></label>
                    <select name="etat" id="etat" class="form-select" required>
                        <option value="programmee" {{ (old('etat') ?? $analyse->etat) == 'programmee' ? 'selected' : '' }}>📅 Programmée</option>
                        <option value="en_cours" {{ (old('etat') ?? $analyse->etat) == 'en_cours' ? 'selected' : '' }}>⏳ En cours</option>
                        <option value="terminee" {{ (old('etat') ?? $analyse->etat) == 'terminee' ? 'selected' : '' }}>✅ Terminée</option>
                        <option value="annulee" {{ (old('etat') ?? $analyse->etat) == 'annulee' ? 'selected' : '' }}>❌ Annulée</option>
                    </select>
                </div>
                
                <!-- Résultats -->
                <div class="col-12">
                    <label for="resultats" class="form-label">Résultats</label>
                    <textarea name="resultats" id="resultats" class="form-control" rows="4" 
                              placeholder="Saisissez les résultats de l'analyse...">{{ old('resultats') ?? $analyse->resultats }}</textarea>
                    <div class="form-text">Laissez vide si les résultats ne sont pas encore disponibles.</div>
                </div>
            </div>
            
            <hr>
            
            <!-- Informations de suivi -->
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-calendar-plus"></i> Créée le : {{ $analyse->created_at->format('d/m/Y à H:i') }}
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="bi bi-pencil"></i> Dernière modification : {{ $analyse->updated_at->format('d/m/Y à H:i') }}
                    </small>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('medecin.analyses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Sauvegarder les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Historique des modifications (si disponible) -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Informations sur l'analyse</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Patient concerné</h6>
                @if($analyse->patient)
                    <p class="mb-2">
                        <strong>{{ $analyse->patient->nom }} {{ $analyse->patient->prenom }}</strong><br>
                        @if($analyse->patient->telephone)
                            <small class="text-muted">Tél: {{ $analyse->patient->telephone }}</small><br>
                        @endif
                        @if($analyse->patient->email)
                            <small class="text-muted">Email: {{ $analyse->patient->email }}</small>
                        @endif
                    </p>
                @else
                    <p class="text-muted">Patient non trouvé</p>
                @endif
            </div>
            
            <div class="col-md-6">
                <h6 class="text-primary">État actuel</h6>
                @php
                    $etats = [
                        'programmee' => ['text-primary', '📅 Programmée'],
                        'en_cours' => ['text-warning', '⏳ En cours'],
                        'terminee' => ['text-success', '✅ Terminée'],
                        'annulee' => ['text-secondary', '❌ Annulée']
                    ];
                    $etatInfo = $etats[$analyse->etat] ?? ['text-secondary', $analyse->etat];
                @endphp
                <p class="{{ $etatInfo[0] }} fw-bold">{{ $etatInfo[1] }}</p>
                
                @if($analyse->etat == 'terminee' && $analyse->resultats)
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Cette analyse est terminée et les résultats sont disponibles.
                    </div>
                @elseif($analyse->etat == 'programmee')
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Cette analyse est programmée et en attente de réalisation.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel
    const form = document.querySelector('form');
    const etatSelect = document.getElementById('etat');
    const resultatsTextarea = document.getElementById('resultats');
    
    // Logique pour l'état et les résultats
    etatSelect.addEventListener('change', function() {
        if (this.value === 'terminee') {
            resultatsTextarea.setAttribute('placeholder', 'Les résultats sont obligatoires pour une analyse terminée...');
            resultatsTextarea.focus();
        } else if (this.value === 'programmee') {
            resultatsTextarea.setAttribute('placeholder', 'Les résultats ne sont généralement pas encore disponibles...');
        } else if (this.value === 'en_cours') {
            resultatsTextarea.setAttribute('placeholder', 'Vous pouvez saisir des résultats partiels...');
        } else if (this.value === 'annulee') {
            resultatsTextarea.setAttribute('placeholder', 'Vous pouvez indiquer la raison de l\'annulation...');
        }
    });
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        const etat = etatSelect.value;
        const resultats = resultatsTextarea.value.trim();
        
        if (etat === 'terminee' && !resultats) {
            if (!confirm('Vous avez marqué cette analyse comme terminée mais aucun résultat n\'est renseigné. Voulez-vous continuer ?')) {
                e.preventDefault();
                resultatsTextarea.focus();
                return;
            }
        }
        
        if (etat === 'annulee' && !resultats) {
            if (!confirm('Vous avez marqué cette analyse comme annulée. Souhaitez-vous ajouter une raison dans les résultats ?')) {
                e.preventDefault();
                resultatsTextarea.focus();
                return;
            }
        }
    });
    
    // Déclencher la logique initiale
    etatSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-star-fill me-2"></i>
                        Évaluer un professionnel de santé
                    </h5>
                </div>
                
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('simple-evaluations.store') }}" method="POST">
                        @csrf
                        
                        <!-- Sélection du professionnel -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="evaluated_user_id" class="form-label required">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Professionnel à évaluer
                                </label>
                                <select class="form-select @error('evaluated_user_id') is-invalid @enderror" 
                                        id="evaluated_user_id" 
                                        name="evaluated_user_id" 
                                        required>
                                    <option value="">-- Sélectionner un professionnel --</option>
                                    @foreach($professionals as $professional)
                                        <option value="{{ $professional->id }}" 
                                                data-role="{{ $professional->role }}"
                                                {{ (old('evaluated_user_id') == $professional->id || 
                                                   ($consultation && $consultation->medecin_id == $professional->id)) ? 'selected' : '' }}>
                                            Dr. {{ $professional->name }} - {{ ucfirst($professional->role) }}
                                            @if($professional->specialite)
                                                ({{ $professional->specialite }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('evaluated_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type_evaluation" class="form-label required">
                                    <i class="bi bi-tag me-1"></i>
                                    Type d'évaluation
                                </label>
                                <select class="form-select @error('type_evaluation') is-invalid @enderror" 
                                        id="type_evaluation" 
                                        name="type_evaluation" 
                                        required>
                                    <option value="">-- Sélectionner le type --</option>
                                    <option value="medecin" {{ old('type_evaluation') == 'medecin' ? 'selected' : '' }}>
                                        <i class="bi bi-person-check"></i> Médecin
                                    </option>
                                    <option value="infirmier" {{ old('type_evaluation') == 'infirmier' ? 'selected' : '' }}>
                                        <i class="bi bi-heart-pulse"></i> Infirmier
                                    </option>
                                </select>
                                @error('type_evaluation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Note avec étoiles -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">
                                    <i class="bi bi-star me-1"></i>
                                    Note globale
                                </label>
                                <div class="star-rating mb-2" id="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star star" data-rating="{{ $i }}"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="note" id="note" value="{{ old('note') }}">
                                <small class="text-muted">
                                    Cliquez sur les étoiles pour donner une note de 1 à 5
                                </small>
                                @error('note')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Commentaire -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="commentaire" class="form-label">
                                    <i class="bi bi-chat-quote me-1"></i>
                                    Commentaire (facultatif)
                                </label>
                                <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                          id="commentaire" 
                                          name="commentaire" 
                                          rows="4" 
                                          placeholder="Partagez votre expérience avec ce professionnel...">{{ old('commentaire') }}</textarea>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <span id="char-count">0</span>/1000 caractères
                                    </small>
                                </div>
                                @error('commentaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Consultation liée (si applicable) -->
                        @if($consultation)
                            <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Cette évaluation est liée à votre consultation du 
                                {{ $consultation->date_consultation->format('d/m/Y à H:i') }}
                            </div>
                        @else
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="consultation_id" class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Consultation liée (facultatif)
                                    </label>
                                    <select class="form-select" id="consultation_id" name="consultation_id">
                                        <option value="">-- Aucune consultation spécifique --</option>
                                        <!-- Les consultations seront chargées via AJAX selon le professionnel sélectionné -->
                                    </select>
                                    <small class="text-muted">
                                        Sélectionnez d'abord un professionnel pour voir ses consultations
                                    </small>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Retour
                                    </a>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Enregistrer l'évaluation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star-rating {
    font-size: 2rem;
    color: #ddd;
}

.star-rating .star {
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating .star:hover,
.star-rating .star.active {
    color: #ffc107;
}

.star-rating .star:hover ~ .star {
    color: #ddd;
}

.required::after {
    content: " *";
    color: red;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du système d'étoiles
    const stars = document.querySelectorAll('.star');
    const noteInput = document.getElementById('note');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            noteInput.value = rating;
            
            // Mettre à jour l'affichage des étoiles
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('active');
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = this.dataset.rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
    });
    
    // Remettre l'état actuel au survol de sortie
    document.getElementById('star-rating').addEventListener('mouseleave', function() {
        const currentRating = noteInput.value;
        
        stars.forEach((s, index) => {
            if (index < currentRating) {
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });
    });
    
    // Initialiser les étoiles selon la valeur actuelle
    const currentRating = noteInput.value || 0;
    stars.forEach((s, index) => {
        if (index < currentRating) {
            s.classList.add('active');
            s.classList.remove('bi-star');
            s.classList.add('bi-star-fill');
        }
    });
    
    // Synchroniser le type d'évaluation avec le professionnel sélectionné
    const professionalSelect = document.getElementById('evaluated_user_id');
    const typeSelect = document.getElementById('type_evaluation');
    
    professionalSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.role) {
            typeSelect.value = selectedOption.dataset.role;
        }
    });
    
    // Compteur de caractères pour le commentaire
    const commentaireTextarea = document.getElementById('commentaire');
    const charCount = document.getElementById('char-count');
    
    function updateCharCount() {
        const count = commentaireTextarea.value.length;
        charCount.textContent = count;
        charCount.className = count > 1000 ? 'text-danger' : 'text-muted';
    }
    
    commentaireTextarea.addEventListener('input', updateCharCount);
    updateCharCount();
});
</script>
@endsection
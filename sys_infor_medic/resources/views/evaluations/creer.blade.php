@extends('layouts.app')

@section('title', 'Évaluer votre consultation')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-star-half-alt fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">Évaluez votre consultation</h3>
                            <p class="mb-0 opacity-75">Votre avis nous aide à améliorer la qualité des soins</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de la consultation -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Détails de votre consultation
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Médecin :</strong> Dr. {{ $consultation->medecin->name }}</p>
                            <p class="mb-2"><strong>Date :</strong> {{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Diagnostic :</strong> {{ $consultation->diagnostic ?? 'Non précisé' }}</p>
                            <p class="mb-2"><strong>Statut :</strong> 
                                <span class="badge bg-success">{{ ucfirst($consultation->statut) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire d'évaluation -->
            <form action="{{ route('evaluations.storer') }}" method="POST">
                @csrf
                <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

                <!-- Notes par aspects -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-4">
                            <i class="fas fa-star me-2"></i>
                            Notez les différents aspects (1 à 5 étoiles)
                        </h5>

                        <div class="row">
                            <!-- Compétence médicale -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user-md me-2 text-primary"></i>
                                    Compétence médicale
                                </label>
                                <div class="rating-stars" data-field="note_competence">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </span>
                                    @endfor
                                </div>
                                <input type="hidden" name="note_competence" class="rating-input" required>
                                @error('note_competence')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Communication -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comments me-2 text-primary"></i>
                                    Communication
                                </label>
                                <div class="rating-stars" data-field="note_communication">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </span>
                                    @endfor
                                </div>
                                <input type="hidden" name="note_communication" class="rating-input" required>
                                @error('note_communication')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ponctualité -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    Ponctualité
                                </label>
                                <div class="rating-stars" data-field="note_ponctualite">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </span>
                                    @endfor
                                </div>
                                <input type="hidden" name="note_ponctualite" class="rating-input" required>
                                @error('note_ponctualite')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Qualité d'écoute -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-ear-listen me-2 text-primary"></i>
                                    Qualité d'écoute
                                </label>
                                <div class="rating-stars" data-field="note_ecoute">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </span>
                                    @endfor
                                </div>
                                <input type="hidden" name="note_ecoute" class="rating-input" required>
                                @error('note_ecoute')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Disponibilité -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i>
                                    Disponibilité
                                </label>
                                <div class="rating-stars" data-field="note_disponibilite">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </span>
                                    @endfor
                                </div>
                                <input type="hidden" name="note_disponibilite" class="rating-input" required>
                                @error('note_disponibilite')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction générale -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="fas fa-thumbs-up me-2"></i>
                            Satisfaction générale
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Niveau de satisfaction</label>
                                <select name="niveau_satisfaction" class="form-select" required>
                                    <option value="">Choisissez...</option>
                                    <option value="très_insatisfait" {{ old('niveau_satisfaction') == 'très_insatisfait' ? 'selected' : '' }}>
                                        😡 Très insatisfait
                                    </option>
                                    <option value="insatisfait" {{ old('niveau_satisfaction') == 'insatisfait' ? 'selected' : '' }}>
                                        😕 Insatisfait
                                    </option>
                                    <option value="neutre" {{ old('niveau_satisfaction') == 'neutre' ? 'selected' : '' }}>
                                        😐 Neutre
                                    </option>
                                    <option value="satisfait" {{ old('niveau_satisfaction') == 'satisfait' ? 'selected' : '' }}>
                                        😊 Satisfait
                                    </option>
                                    <option value="très_satisfait" {{ old('niveau_satisfaction') == 'très_satisfait' ? 'selected' : '' }}>
                                        😍 Très satisfait
                                    </option>
                                </select>
                                @error('niveau_satisfaction')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Recommandation</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="recommande_medecin" 
                                               id="recommande_oui" value="1" {{ old('recommande_medecin') == '1' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-success fw-bold" for="recommande_oui">
                                            <i class="fas fa-thumbs-up me-1"></i> Oui, je recommande
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="recommande_medecin" 
                                               id="recommande_non" value="0" {{ old('recommande_medecin') == '0' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-danger fw-bold" for="recommande_non">
                                            <i class="fas fa-thumbs-down me-1"></i> Non, je ne recommande pas
                                        </label>
                                    </div>
                                </div>
                                @error('recommande_medecin')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="fas fa-comment-alt me-2"></i>
                            Vos commentaires (optionnel)
                        </h5>

                        <div class="mb-3">
                            <label for="commentaire_positif" class="form-label fw-bold text-success">
                                <i class="fas fa-plus-circle me-1"></i>
                                Ce que vous avez le plus apprécié
                            </label>
                            <textarea name="commentaire_positif" id="commentaire_positif" 
                                      class="form-control" rows="3" maxlength="1000"
                                      placeholder="Ex: L'écoute du médecin, les explications claires, l'accueil...">{{ old('commentaire_positif') }}</textarea>
                            <div class="form-text">Maximum 1000 caractères</div>
                            @error('commentaire_positif')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="commentaire_amelioration" class="form-label fw-bold text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Points à améliorer
                            </label>
                            <textarea name="commentaire_amelioration" id="commentaire_amelioration" 
                                      class="form-control" rows="3" maxlength="1000"
                                      placeholder="Ex: Temps d'attente, organisation, clarté des explications...">{{ old('commentaire_amelioration') }}</textarea>
                            <div class="form-text">Maximum 1000 caractères</div>
                            @error('commentaire_amelioration')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="commentaire_general" class="form-label fw-bold">
                                <i class="fas fa-comment me-1"></i>
                                Commentaire général
                            </label>
                            <textarea name="commentaire_general" id="commentaire_general" 
                                      class="form-control" rows="4" maxlength="1000"
                                      placeholder="Partagez votre expérience globale...">{{ old('commentaire_general') }}</textarea>
                            <div class="form-text">Maximum 1000 caractères</div>
                            @error('commentaire_general')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Options de confidentialité -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="fas fa-shield-alt me-2"></i>
                            Confidentialité
                        </h5>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="visible_publiquement" 
                                   id="visible_publiquement" value="1" 
                                   {{ old('visible_publiquement', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="visible_publiquement">
                                <strong>Autoriser la publication publique de cette évaluation</strong>
                            </label>
                            <div class="form-text">
                                <i class="fas fa-info-circle text-info me-1"></i>
                                Si cochée, votre évaluation pourra être consultée publiquement (anonymisée). 
                                Sinon, elle restera privée et ne sera visible que par le médecin et l'administration.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Soumettre mon évaluation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles personnalisés -->
<style>
.rating-stars {
    display: flex;
    gap: 5px;
    margin-top: 8px;
    margin-bottom: 10px;
}

.rating-stars .star {
    font-size: 1.5rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.rating-stars .star:hover,
.rating-stars .star.active {
    color: #ffc107;
}

.rating-stars .star i {
    transition: transform 0.1s ease;
}

.rating-stars .star:hover i {
    transform: scale(1.1);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du système d'étoiles
    const ratingContainers = document.querySelectorAll('.rating-stars');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.parentNode.querySelector('.rating-input');
        const fieldName = container.dataset.field;
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.value);
                input.value = rating;
                
                // Mettre à jour l'affichage des étoiles
                updateStars(stars, rating);
            });
            
            star.addEventListener('mouseover', function() {
                const rating = parseInt(this.dataset.value);
                updateStars(stars, rating);
            });
        });
        
        container.addEventListener('mouseleave', function() {
            const currentRating = parseInt(input.value) || 0;
            updateStars(stars, currentRating);
        });
    });
    
    function updateStars(stars, rating) {
        stars.forEach((star, index) => {
            const starIcon = star.querySelector('i');
            if (index < rating) {
                starIcon.className = 'fas fa-star';
                star.classList.add('active');
            } else {
                starIcon.className = 'far fa-star';
                star.classList.remove('active');
            }
        });
    }
    
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = ['note_competence', 'note_communication', 'note_ponctualite', 'note_ecoute', 'note_disponibilite'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = form.querySelector(`input[name="${field}"]`);
            if (!input.value) {
                isValid = false;
                const container = input.parentNode.querySelector('.rating-stars');
                container.style.border = '2px solid red';
                container.style.borderRadius = '4px';
                container.style.padding = '5px';
                
                setTimeout(() => {
                    container.style.border = 'none';
                    container.style.padding = '0';
                }, 3000);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez noter tous les aspects avant de soumettre votre évaluation.');
            window.scrollTo({top: 0, behavior: 'smooth'});
        }
    });
    
    // Compteurs de caractères
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const formText = textarea.parentNode.querySelector('.form-text');
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            formText.textContent = `${remaining} caractères restants (max ${maxLength})`;
            
            if (remaining < 50) {
                formText.className = 'form-text text-warning';
            } else {
                formText.className = 'form-text';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
});
</script>
@endsection
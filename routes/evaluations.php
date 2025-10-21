<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;

/*
|--------------------------------------------------------------------------
| Routes d'évaluation des médecins
|--------------------------------------------------------------------------
*/

// Routes pour les patients
Route::middleware(['auth', 'verified'])->prefix('patient')->name('patient.')->group(function () {
    // Consultations pouvant être évaluées
    Route::get('consultations-a-evaluer', [EvaluationController::class, 'consultationsAEvaluer'])
         ->name('consultations-a-evaluer');
    
    // Créer une évaluation
    Route::get('evaluation/{consultation}', [EvaluationController::class, 'creerEvaluation'])
         ->name('evaluation.creer');
    
    // Soumettre une évaluation
    Route::post('evaluation', [EvaluationController::class, 'storerEvaluation'])
         ->name('evaluation.storer');
});

// Routes publiques pour les évaluations de médecins
Route::prefix('medecin')->name('medecin.')->group(function () {
    // Afficher les évaluations publiques d'un médecin
    Route::get('{medecin}/evaluations', [EvaluationController::class, 'evaluationsMedecin'])
         ->name('evaluations');
    
    // API pour récupérer les statistiques d'un médecin
    Route::get('{medecin}/api/statistiques', [EvaluationController::class, 'apiStatistiquesMedecin'])
         ->name('api.statistiques');
    
    // API pour récupérer les évaluations d'un médecin
    Route::get('{medecin}/api/evaluations', [EvaluationController::class, 'apiEvaluationsMedecin'])
         ->name('api.evaluations');
    
    // Exporter les évaluations en PDF
    Route::get('{medecin}/evaluations/pdf', [EvaluationController::class, 'exporterEvaluationsPDF'])
         ->name('evaluations.pdf');
});

// Routes pour les médecins connectés
Route::middleware(['auth', 'verified'])->prefix('medecin')->name('medecin.')->group(function () {
    // Dashboard des évaluations pour le médecin connecté
    Route::get('mes-evaluations', [EvaluationController::class, 'dashboardMedecin'])
         ->name('dashboard.evaluations');
    
    // Répondre à une évaluation
    Route::post('evaluation/{evaluation}/repondre', [EvaluationController::class, 'repondreEvaluation'])
         ->name('evaluation.repondre');
});

// Routes pour les administrateurs
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestion des évaluations
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        // Liste de toutes les évaluations
        Route::get('/', function() {
            $evaluations = \App\Models\EvaluationMedecin::with(['patient', 'medecin', 'consultation'])
                                                          ->latest('date_evaluation')
                                                          ->paginate(20);
            
            return view('admin.evaluations.index', compact('evaluations'));
        })->name('index');
        
        // Modifier le statut d'une évaluation
        Route::patch('{evaluation}/statut', [EvaluationController::class, 'modifierStatut'])
             ->name('modifier-statut');
        
        // Statistiques générales des évaluations
        Route::get('statistiques', function() {
            $stats = [
                'total_evaluations' => \App\Models\EvaluationMedecin::count(),
                'evaluations_ce_mois' => \App\Models\EvaluationMedecin::whereMonth('date_evaluation', now()->month)->count(),
                'note_moyenne_globale' => \App\Models\EvaluationMedecin::visibles()->avg('note_globale'),
                'medecins_evalues' => \App\Models\EvaluationMedecin::distinct('medecin_id')->count(),
                'taux_recommandation' => (\App\Models\EvaluationMedecin::visibles()->where('recommande_medecin', true)->count() / max(\App\Models\EvaluationMedecin::visibles()->count(), 1)) * 100,
                'evaluations_par_statut' => \App\Models\EvaluationMedecin::selectRaw('statut, COUNT(*) as total')
                                                                       ->groupBy('statut')
                                                                       ->pluck('total', 'statut')
                                                                       ->toArray(),
                'evaluations_par_satisfaction' => \App\Models\EvaluationMedecin::selectRaw('niveau_satisfaction, COUNT(*) as total')
                                                                               ->groupBy('niveau_satisfaction')
                                                                               ->pluck('total', 'niveau_satisfaction')
                                                                               ->toArray()
            ];
            
            return view('admin.evaluations.statistiques', compact('stats'));
        })->name('statistiques');
        
        // Rapport détaillé d'un médecin
        Route::get('medecin/{medecin}', function(\App\Models\User $medecin) {
            $statistiques = \App\Models\EvaluationMedecin::statistiquesMedecin($medecin->id);
            $evaluations = \App\Models\EvaluationMedecin::pourMedecin($medecin->id)
                                                       ->with(['patient', 'consultation'])
                                                       ->latest('date_evaluation')
                                                       ->paginate(15);
            
            return view('admin.evaluations.medecin-detail', compact('medecin', 'statistiques', 'evaluations'));
        })->name('medecin-detail');
    });
});

// Routes API publiques (avec limitation de taux)
Route::middleware(['throttle:api'])->prefix('api/evaluations')->name('api.evaluations.')->group(function () {
    // Statistiques globales des évaluations
    Route::get('statistiques-globales', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'nombre_total_evaluations' => \App\Models\EvaluationMedecin::visibles()->count(),
                'note_moyenne_globale' => round(\App\Models\EvaluationMedecin::visibles()->avg('note_globale'), 1),
                'pourcentage_recommandations' => round((\App\Models\EvaluationMedecin::visibles()->where('recommande_medecin', true)->count() / max(\App\Models\EvaluationMedecin::visibles()->count(), 1)) * 100, 1),
                'medecins_les_mieux_notes' => \App\Models\User::whereHas('evaluationsVisibles')
                                                             ->with(['evaluationsVisibles' => function($query) {
                                                                 $query->selectRaw('medecin_id, AVG(note_globale) as moyenne, COUNT(*) as total')
                                                                       ->groupBy('medecin_id')
                                                                       ->having('total', '>=', 5)
                                                                       ->orderBy('moyenne', 'desc');
                                                             }])
                                                             ->take(10)
                                                             ->get()
                                                             ->map(function($medecin) {
                                                                 return [
                                                                     'nom' => 'Dr. ' . $medecin->name,
                                                                     'specialite' => $medecin->specialite ?? 'Médecine générale',
                                                                     'note_moyenne' => $medecin->note_moyenne,
                                                                     'nombre_evaluations' => $medecin->nombre_evaluations
                                                                 ];
                                                             })
            ]
        ]);
    })->name('statistiques-globales');
    
    // Top médecins par spécialité
    Route::get('top-medecins/{specialite?}', function($specialite = null) {
        $query = \App\Models\User::whereHas('evaluationsVisibles', function($query) {
            $query->selectRaw('medecin_id, AVG(note_globale) as moyenne, COUNT(*) as total')
                  ->groupBy('medecin_id')
                  ->having('total', '>=', 3);
        });
        
        if ($specialite) {
            $query->where('specialite', $specialite);
        }
        
        $medecins = $query->with(['evaluationsVisibles'])
                         ->get()
                         ->sortByDesc('note_moyenne')
                         ->take(20)
                         ->values();
        
        return response()->json([
            'success' => true,
            'specialite' => $specialite ?: 'Toutes spécialités',
            'data' => $medecins->map(function($medecin) {
                return [
                    'id' => $medecin->id,
                    'nom' => 'Dr. ' . $medecin->name,
                    'specialite' => $medecin->specialite ?? 'Médecine générale',
                    'note_moyenne' => $medecin->note_moyenne,
                    'nombre_evaluations' => $medecin->nombre_evaluations,
                    'pourcentage_recommandations' => $medecin->pourcentage_recommandations
                ];
            })
        ]);
    })->name('top-medecins');
});

// ===================== SYSTÈME D'ÉVALUATION SIMPLE =====================
// Routes pour le nouveau système d'évaluation simplifié
Route::middleware(['auth', 'verified'])->prefix('simple-evaluations')->name('simple-evaluations.')->group(function () {
    // Liste des évaluations (avec filtrage optionnel par professionnel)
    Route::get('/', [\App\Http\Controllers\SimpleEvaluationController::class, 'index'])->name('index');
    Route::get('/professional/{user}', [\App\Http\Controllers\SimpleEvaluationController::class, 'index'])->name('professional');
    
    // Formulaire de création d'évaluation
    Route::get('/create', [\App\Http\Controllers\SimpleEvaluationController::class, 'create'])->name('create');
    
    // Enregistrer une évaluation (patients uniquement)
    Route::post('/', [\App\Http\Controllers\SimpleEvaluationController::class, 'store'])->name('store');
    
    // Afficher une évaluation
    Route::get('/{evaluation}', [\App\Http\Controllers\SimpleEvaluationController::class, 'show'])->name('show');
    
    // Mes évaluations (pour les patients)
    Route::get('/my/evaluations', [\App\Http\Controllers\SimpleEvaluationController::class, 'myEvaluations'])->name('my-evaluations');
});

// Dashboard pour les professionnels (médecins/infirmiers)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-evaluations-dashboard', [\App\Http\Controllers\SimpleEvaluationController::class, 'professionalDashboard'])
         ->name('simple-evaluations.professional-dashboard')
         ->middleware('role:medecin,infirmier');
         
    // Route de test temporaire pour déboguer
    Route::get('/test-evaluations', function() {
        $user = auth()->user();
        return response()->json([
            'user_role' => $user->role,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'evaluations_count' => \App\Models\Evaluation::countForProfessional($user->id),
            'average_rating' => \App\Models\Evaluation::averageRatingForProfessional($user->id)
        ]);
    })->name('test-evaluations')->middleware('role:medecin,infirmier');
});

// Dashboard pour les administrateurs
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/evaluations-dashboard', [\App\Http\Controllers\SimpleEvaluationController::class, 'adminDashboard'])
         ->name('simple-evaluations.admin-dashboard');
});

// API pour les statistiques des professionnels
Route::middleware(['throttle:api'])->prefix('api/simple-evaluations')->name('api.simple-evaluations.')->group(function () {
    // Statistiques d'un professionnel
    Route::get('/professional/{user}/stats', [\App\Http\Controllers\SimpleEvaluationController::class, 'professionalStats'])->name('professional-stats');
});

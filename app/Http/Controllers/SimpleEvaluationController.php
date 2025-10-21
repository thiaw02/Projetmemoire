<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\Consultations;
use App\Http\Requests\StoreEvaluationRequest;
use Illuminate\Http\Request;

class SimpleEvaluationController extends Controller
{
    /**
     * Affiche la liste des évaluations pour un professionnel
     */
    public function index(Request $request, User $user = null)
    {
        $query = Evaluation::with(['patient', 'evaluatedUser']);
        
        // Si un utilisateur est spécifié, filtrer ses évaluations
        if ($user) {
            $query->forProfessional($user->id);
        }
        
        // Filtrage par type d'évaluation
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }
        
        // Filtrage par note
        if ($request->filled('note')) {
            $query->where('note', $request->note);
        }
        
        // Recherche par commentaire
        if ($request->filled('search')) {
            $query->where('commentaire', 'like', '%' . $request->search . '%');
        }
        
        $evaluations = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistiques si on affiche pour un professionnel spécifique
        $stats = null;
        if ($user) {
            $stats = [
                'average_rating' => Evaluation::averageRatingForProfessional($user->id),
                'total_count' => Evaluation::countForProfessional($user->id),
                'medecin_rating' => Evaluation::averageRatingForProfessional($user->id, 'medecin'),
                'infirmier_rating' => Evaluation::averageRatingForProfessional($user->id, 'infirmier'),
            ];
        }
        
        return view('simple-evaluations.index', compact('evaluations', 'user', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'une évaluation
     */
    public function create(Request $request)
    {
        // Récupérer les professionnels (médecins et infirmiers)
        $professionals = User::whereIn('role', ['medecin', 'infirmier'])
                            ->orderBy('name')
                            ->get();
        
        // Si consultation_id est fourni, pré-sélectionner le professionnel
        $consultation = null;
        if ($request->filled('consultation_id')) {
            $consultation = Consultations::find($request->consultation_id);
        }
        
        return view('simple-evaluations.create', compact('professionals', 'consultation'));
    }

    /**
     * Enregistre une nouvelle évaluation
     */
    public function store(StoreEvaluationRequest $request)
    {
        // Vérifier que l'utilisateur évalué est bien un professionnel
        $professional = User::find($request->evaluated_user_id);
        if (!in_array($professional->role, ['medecin', 'infirmier'])) {
            return redirect()->back()->withErrors(['evaluated_user_id' => 'Seuls les médecins et infirmiers peuvent être évalués.']);
        }
        
        // Vérifier que le patient n'a pas déjà évalué ce professionnel pour cette consultation
        if ($request->consultation_id) {
            $existingEvaluation = Evaluation::where('patient_id', auth()->id())
                                          ->where('evaluated_user_id', $request->evaluated_user_id)
                                          ->where('consultation_id', $request->consultation_id)
                                          ->first();
            
            if ($existingEvaluation) {
                return redirect()->back()->withErrors(['consultation_id' => 'Vous avez déjà évalué ce professionnel pour cette consultation.']);
            }
        }
        
        $evaluation = Evaluation::create([
            'patient_id' => auth()->id(),
            'evaluated_user_id' => $request->evaluated_user_id,
            'type_evaluation' => $request->type_evaluation,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'consultation_id' => $request->consultation_id,
        ]);
        
        return redirect()->route('simple-evaluations.show', $evaluation)
                        ->with('success', 'Votre évaluation a été enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une évaluation
     */
    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['patient', 'evaluatedUser', 'consultation']);
        
        // Vérifier les permissions
        if (auth()->user()->role === 'patient' && $evaluation->patient_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette évaluation.');
        }
        
        return view('simple-evaluations.show', compact('evaluation'));
    }

    /**
     * Affiche les évaluations d'un patient (ses propres évaluations)
     */
    public function myEvaluations()
    {
        $evaluations = Evaluation::with(['evaluatedUser', 'consultation'])
                                ->byPatient(auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->paginate(8); // Pagination optimisée pour les patients
        
        return view('simple-evaluations.my-evaluations', compact('evaluations'));
    }

    /**
     * Dashboard pour les professionnels (médecins/infirmiers)
     */
    public function professionalDashboard()
    {
        $user = auth()->user();
        
        // Debug - Log des informations utilisateur
        \Log::info('professionalDashboard accessed', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_name' => $user->name
        ]);
        
        // Vérifier que l'utilisateur est un professionnel
        if (!in_array($user->role, ['medecin', 'infirmier'])) {
            \Log::error('Access denied for user', ['user_role' => $user->role]);
            abort(403, 'Accès réservé aux professionnels de santé.');
        }
        
        // Statistiques principales
        try {
            $stats = [
                'average_rating' => Evaluation::averageRatingForProfessional($user->id) ?? 0,
                'total_count' => Evaluation::countForProfessional($user->id) ?? 0,
                'this_month' => Evaluation::where('evaluated_user_id', $user->id)
                                        ->whereMonth('created_at', now()->month)
                                        ->count(),
                'evolution' => 0, // Simplifié temporairement
                'ratings_breakdown' => [],
            ];
        } catch (\Exception $e) {
            \Log::error('Error calculating stats', ['error' => $e->getMessage()]);
            $stats = [
                'average_rating' => 0,
                'total_count' => 0,
                'this_month' => 0,
                'evolution' => 0,
                'ratings_breakdown' => [],
            ];
        }
        
        // Répartition des notes
        try {
            for ($i = 1; $i <= 5; $i++) {
                $count = Evaluation::where('evaluated_user_id', $user->id)->where('note', $i)->count();
                $stats['ratings_breakdown'][$i] = $count;
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating ratings breakdown', ['error' => $e->getMessage()]);
            for ($i = 1; $i <= 5; $i++) {
                $stats['ratings_breakdown'][$i] = 0;
            }
        }
        
        // Calculer le rang du professionnel (simplifié)
        try {
            $totalProfessionals = User::whereIn('role', ['medecin', 'infirmier'])->count();
            $stats['rank'] = 1; // Simplifié temporairement
            $stats['total_professionals'] = $totalProfessionals;
        } catch (\Exception $e) {
            \Log::error('Error calculating rank', ['error' => $e->getMessage()]);
            $stats['rank'] = 1;
            $stats['total_professionals'] = 1;
        }
        
        // Évaluations récentes
        try {
            $evaluations = Evaluation::with(['patient', 'consultation'])
                                    ->where('evaluated_user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10); // Réduit à 10 pour une pagination plus fluide
        } catch (\Exception $e) {
            \Log::error('Error fetching evaluations', ['error' => $e->getMessage()]);
            $evaluations = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }
        
        return view('simple-evaluations.professional-dashboard', compact('stats', 'evaluations'));
    }
    
    /**
     * Dashboard pour les administrateurs
     */
    public function adminDashboard()
    {
        // Vérifier que l'utilisateur est admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs.');
        }
        
        // Statistiques globales
        $globalStats = [
            'average_rating' => Evaluation::avg('note'),
            'total_evaluations' => Evaluation::count(),
            'evaluated_professionals' => Evaluation::distinct('evaluated_user_id')->count(),
            'this_month' => Evaluation::whereMonth('created_at', now()->month)->count(),
            'rating_distribution' => [],
        ];
        
        // Répartition des notes globales
        for ($i = 1; $i <= 5; $i++) {
            $globalStats['rating_distribution'][$i] = Evaluation::where('note', $i)->count();
        }
        
        // Classement des professionnels
        $professionalRanking = User::whereIn('role', ['medecin', 'infirmier'])
                                  ->get()
                                  ->map(function($professional) {
                                      $lastEvaluation = Evaluation::forProfessional($professional->id)
                                                               ->latest('created_at')
                                                               ->first();
                                      
                                      return [
                                          'id' => $professional->id,
                                          'name' => $professional->name,
                                          'role' => $professional->role,
                                          'average_rating' => Evaluation::averageRatingForProfessional($professional->id),
                                          'total_count' => Evaluation::countForProfessional($professional->id),
                                          'last_evaluation' => $lastEvaluation,
                                          'last_evaluation_timestamp' => $lastEvaluation ? $lastEvaluation->created_at->timestamp : 0,
                                      ];
                                  })
                                  ->sortByDesc('average_rating')
                                  ->values();
        
        // Évaluations récentes avec pagination optimisée
        $recentEvaluations = Evaluation::with(['patient', 'evaluatedUser', 'consultation'])
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(15); // Pagination plus gérable
        
        return view('simple-evaluations.admin-dashboard', compact('globalStats', 'professionalRanking', 'recentEvaluations'));
    }
    
    /**
     * Calculer l'évolution de la note ce mois vs le mois précédent
     */
    private function calculateEvolution($userId)
    {
        $thisMonth = Evaluation::forProfessional($userId)
                              ->whereMonth('created_at', now()->month)
                              ->avg('note');
        
        $lastMonth = Evaluation::forProfessional($userId)
                              ->whereMonth('created_at', now()->subMonth()->month)
                              ->avg('note');
        
        if (!$thisMonth || !$lastMonth) {
            return 0;
        }
        
        return round($thisMonth - $lastMonth, 1);
    }

    /**
     * API : Retourne les statistiques d'évaluation pour un professionnel
     */
    public function professionalStats(User $user)
    {
        if (!in_array($user->role, ['medecin', 'infirmier'])) {
            return response()->json(['error' => 'Cet utilisateur n\'est pas un professionnel'], 400);
        }
        
        $stats = [
            'average_rating' => Evaluation::averageRatingForProfessional($user->id),
            'total_count' => Evaluation::countForProfessional($user->id),
            'ratings_breakdown' => [],
        ];
        
        // Répartition des notes
        for ($i = 1; $i <= 5; $i++) {
            $count = Evaluation::forProfessional($user->id)->where('note', $i)->count();
            $stats['ratings_breakdown'][$i] = $count;
        }
        
        // Évaluations par type
        $stats['by_type'] = [
            'medecin' => [
                'average' => Evaluation::averageRatingForProfessional($user->id, 'medecin'),
                'count' => Evaluation::countForProfessional($user->id, 'medecin'),
            ],
            'infirmier' => [
                'average' => Evaluation::averageRatingForProfessional($user->id, 'infirmier'),
                'count' => Evaluation::countForProfessional($user->id, 'infirmier'),
            ],
        ];
        
        return response()->json($stats);
    }
}
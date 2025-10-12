<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluationMedecin;
use App\Models\Consultations;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    /**
     * Afficher le formulaire d'évaluation pour une consultation
     */
    public function creerEvaluation($consultationId)
    {
        $consultation = Consultations::with(['patient', 'medecin'])
                                   ->findOrFail($consultationId);

        // Vérifier si la consultation peut être évaluée
        if (!$consultation->peutEtreEvaluee()) {
            return redirect()->back()->with('error', 'Cette consultation ne peut plus être évaluée.');
        }

        // Vérifier si l'utilisateur connecté est le patient de cette consultation
        $patient = Patient::where('user_id', Auth::id())->first();
        if (!$patient || $consultation->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à évaluer cette consultation.');
        }

        return view('evaluations.creer', compact('consultation'));
    }

    /**
     * Enregistrer une nouvelle évaluation
     */
    public function storerEvaluation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consultation_id' => 'required|exists:consultations,id',
            'note_competence' => 'required|numeric|min:1|max:5',
            'note_communication' => 'required|numeric|min:1|max:5',
            'note_ponctualite' => 'required|numeric|min:1|max:5',
            'note_ecoute' => 'required|numeric|min:1|max:5',
            'note_disponibilite' => 'required|numeric|min:1|max:5',
            'commentaire_positif' => 'nullable|string|max:1000',
            'commentaire_amelioration' => 'nullable|string|max:1000',
            'commentaire_general' => 'nullable|string|max:1000',
            'recommande_medecin' => 'required|boolean',
            'niveau_satisfaction' => 'required|in:très_insatisfait,insatisfait,neutre,satisfait,très_satisfait',
            'visible_publiquement' => 'boolean'
        ], [
            'note_competence.required' => 'La note de compétence est obligatoire',
            'note_competence.min' => 'La note doit être comprise entre 1 et 5',
            'note_competence.max' => 'La note doit être comprise entre 1 et 5',
            'note_communication.required' => 'La note de communication est obligatoire',
            'recommande_medecin.required' => 'Veuillez indiquer si vous recommandez ce médecin',
            'niveau_satisfaction.required' => 'Le niveau de satisfaction est obligatoire'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $consultation = Consultations::findOrFail($request->consultation_id);
        
        // Vérifications de sécurité
        $patient = Patient::where('user_id', Auth::id())->first();
        if (!$patient || $consultation->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }

        if (!$consultation->peutEtreEvaluee()) {
            return redirect()->back()->with('error', 'Cette consultation ne peut plus être évaluée.');
        }

        // Créer l'évaluation
        $evaluation = EvaluationMedecin::create([
            'patient_id' => $patient->id,
            'medecin_id' => $consultation->medecin_id,
            'consultation_id' => $consultation->id,
            'note_competence' => $request->note_competence,
            'note_communication' => $request->note_communication,
            'note_ponctualite' => $request->note_ponctualite,
            'note_ecoute' => $request->note_ecoute,
            'note_disponibilite' => $request->note_disponibilite,
            'commentaire_positif' => $request->commentaire_positif,
            'commentaire_amelioration' => $request->commentaire_amelioration,
            'commentaire_general' => $request->commentaire_general,
            'recommande_medecin' => $request->recommande_medecin,
            'niveau_satisfaction' => $request->niveau_satisfaction,
            'visible_publiquement' => $request->has('visible_publiquement'),
            'statut' => 'soumise',
            'date_evaluation' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return redirect()->route('patient.consultations')
                        ->with('success', 'Votre évaluation a été enregistrée avec succès. Merci pour votre retour !');
    }

    /**
     * Afficher les évaluations d'un médecin
     */
    public function evaluationsMedecin($medecinId)
    {
        $medecin = User::findOrFail($medecinId);
        
        // Statistiques générales
        $statistiques = EvaluationMedecin::statistiquesMedecin($medecinId);
        
        // Évaluations récentes visibles publiquement
        $evaluations = EvaluationMedecin::visibles()
                                       ->pourMedecin($medecinId)
                                       ->with(['patient', 'consultation'])
                                       ->latest('date_evaluation')
                                       ->paginate(10);

        return view('evaluations.medecin', compact('medecin', 'statistiques', 'evaluations'));
    }

    /**
     * Afficher le dashboard des évaluations pour un médecin connecté
     */
    public function dashboardMedecin()
    {
        $medecin = Auth::user();
        
        // Statistiques générales
        $statistiques = EvaluationMedecin::statistiquesMedecin($medecin->id);
        
        // Évaluations récentes (toutes, y compris non publiques)
        $evaluationsRecentes = EvaluationMedecin::pourMedecin($medecin->id)
                                               ->with(['patient', 'consultation'])
                                               ->latest('date_evaluation')
                                               ->take(10)
                                               ->get();

        // Points forts et à améliorer
        $pointsForts = [];
        $pointsAmeliorer = [];
        
        foreach ($evaluationsRecentes as $evaluation) {
            $pointsForts = array_merge($pointsForts, $evaluation->getPointsForts());
            $pointsAmeliorer = array_merge($pointsAmeliorer, $evaluation->getPointsAmeliorer());
        }

        // Tendance des notes (6 derniers mois)
        $tendanceNotes = $this->getTendanceNotes($medecin->id);

        return view('evaluations.dashboard-medecin', compact(
            'medecin', 
            'statistiques', 
            'evaluationsRecentes', 
            'pointsForts', 
            'pointsAmeliorer',
            'tendanceNotes'
        ));
    }

    /**
     * Modifier le statut d'une évaluation (pour les admins)
     */
    public function modifierStatut(Request $request, $evaluationId)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,soumise,validee,archivee'
        ]);

        $evaluation = EvaluationMedecin::findOrFail($evaluationId);
        $evaluation->update(['statut' => $request->statut]);

        return redirect()->back()->with('success', 'Statut de l\'évaluation mis à jour.');
    }

    /**
     * Répondre à une évaluation (pour les médecins)
     */
    public function repondreEvaluation(Request $request, $evaluationId)
    {
        $request->validate([
            'reponse_medecin' => 'required|string|max:1000'
        ]);

        $evaluation = EvaluationMedecin::findOrFail($evaluationId);
        
        // Vérifier que c'est bien le médecin concerné
        if ($evaluation->medecin_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }

        // Ajouter la colonne reponse_medecin à la migration si nécessaire
        $evaluation->update(['reponse_medecin' => $request->reponse_medecin]);

        return redirect()->back()->with('success', 'Votre réponse a été enregistrée.');
    }

    /**
     * Afficher les consultations pouvant être évaluées par un patient
     */
    public function consultationsAEvaluer()
    {
        $patient = Patient::where('user_id', Auth::id())->first();
        
        if (!$patient) {
            return redirect()->back()->with('error', 'Patient non trouvé.');
        }

        $consultations = Consultations::with(['medecin', 'evaluation'])
                                     ->where('patient_id', $patient->id)
                                     ->where('statut', 'terminee')
                                     ->where('date_consultation', '>=', now()->subMonths(3))
                                     ->whereDoesntHave('evaluation')
                                     ->latest('date_consultation')
                                     ->get();

        return view('evaluations.consultations-a-evaluer', compact('consultations'));
    }

    /**
     * API: Obtenir les statistiques d'un médecin
     */
    public function apiStatistiquesMedecin($medecinId)
    {
        $statistiques = EvaluationMedecin::statistiquesMedecin($medecinId);
        
        return response()->json([
            'success' => true,
            'data' => $statistiques
        ]);
    }

    /**
     * API: Obtenir les évaluations publiques d'un médecin
     */
    public function apiEvaluationsMedecin($medecinId)
    {
        $evaluations = EvaluationMedecin::visibles()
                                       ->pourMedecin($medecinId)
                                       ->with(['patient:id,nom,prenom', 'consultation:id,date_consultation'])
                                       ->latest('date_evaluation')
                                       ->get()
                                       ->map(function ($evaluation) {
                                           return [
                                               'id' => $evaluation->id,
                                               'patient_initiales' => substr($evaluation->patient->prenom, 0, 1) . '. ' . substr($evaluation->patient->nom, 0, 1) . '.',
                                               'note_globale' => $evaluation->note_globale,
                                               'note_avec_etoiles' => $evaluation->note_avec_etoiles,
                                               'niveau_satisfaction' => $evaluation->niveau_satisfaction_fr,
                                               'commentaire_general' => $evaluation->commentaire_general,
                                               'recommande_medecin' => $evaluation->recommande_medecin,
                                               'date_evaluation' => $evaluation->date_evaluation->format('d/m/Y'),
                                               'date_consultation' => $evaluation->consultation->date_consultation
                                           ];
                                       });

        return response()->json([
            'success' => true,
            'data' => $evaluations
        ]);
    }

    /**
     * Obtenir la tendance des notes sur les 6 derniers mois
     */
    private function getTendanceNotes($medecinId)
    {
        $tendance = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $debut = now()->subMonths($i)->startOfMonth();
            $fin = now()->subMonths($i)->endOfMonth();
            
            $note = EvaluationMedecin::visibles()
                                   ->pourMedecin($medecinId)
                                   ->whereBetween('date_evaluation', [$debut, $fin])
                                   ->avg('note_globale');

            $tendance[] = [
                'mois' => $debut->format('M Y'),
                'note' => $note ? round($note, 1) : null,
                'nombre_evaluations' => EvaluationMedecin::visibles()
                                                        ->pourMedecin($medecinId)
                                                        ->whereBetween('date_evaluation', [$debut, $fin])
                                                        ->count()
            ];
        }

        return $tendance;
    }

    /**
     * Exporter les évaluations d'un médecin en PDF
     */
    public function exporterEvaluationsPDF($medecinId)
    {
        $medecin = User::findOrFail($medecinId);
        $statistiques = EvaluationMedecin::statistiquesMedecin($medecinId);
        $evaluations = EvaluationMedecin::visibles()
                                       ->pourMedecin($medecinId)
                                       ->with(['patient', 'consultation'])
                                       ->latest('date_evaluation')
                                       ->get();

        $data = [
            'medecin' => $medecin,
            'statistiques' => $statistiques,
            'evaluations' => $evaluations,
            'date_generation' => now()
        ];

        // Utiliser le service PDF existant pour générer un rapport d'évaluations
        $pdfService = app(\App\Services\PDFGeneratorService::class);
        return $pdfService->generateRapportStatsPDF($data);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Analyses;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyseController extends Controller
{
    /**
     * Afficher la liste des analyses récentes
     */
    public function index(Request $request)
    {
        $medecinId = Auth::id();
        
        $query = Analyses::with(['patient', 'medecin'])
            ->where('medecin_id', $medecinId)
            ->orderBy('date_analyse', 'desc');
        
        // Filtres
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->filled('type_analyse')) {
            $query->where('type_analyse', 'like', '%' . $request->type_analyse . '%');
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_analyse', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_analyse', '<=', $request->date_fin);
        }
        
        $analyses = $query->paginate(20)->withQueryString();
        
        // Patients pour le filtre
        $patients = Patient::whereHas('consultations', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        })->orWhereHas('analyses', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        })->orderBy('nom')->get();
        
        // Statistiques rapides
        $stats = [
            'total' => Analyses::where('medecin_id', $medecinId)->count(),
            'ce_mois' => Analyses::where('medecin_id', $medecinId)
                ->whereYear('date_analyse', now()->year)
                ->whereMonth('date_analyse', now()->month)
                ->count(),
            'en_attente' => Analyses::where('medecin_id', $medecinId)
                ->where('etat', 'en_attente')
                ->count(),
            'terminees' => Analyses::where('medecin_id', $medecinId)
                ->where('etat', 'terminee')
                ->count(),
        ];
        
        return view('medecin.analyses.index', compact('analyses', 'patients', 'stats'));
    }
    
    /**
     * Afficher le formulaire de création d'une nouvelle analyse
     */
    public function create()
    {
        $medecinId = Auth::id();
        
        // Patients ayant consulté ce médecin
        $patients = Patient::whereHas('consultations', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        })->orderBy('nom')->get();
        
        // Types d'analyses courantes
        $typesAnalyses = [
            'Analyses sanguines' => [
                'Hémogramme complet',
                'Glycémie',
                'Cholestérol',
                'Créatinine',
                'Urée',
                'Transaminases (ALAT/ASAT)',
                'Bilirubine',
                'CRP',
                'VS (Vitesse de sédimentation)'
            ],
            'Analyses urinaires' => [
                'ECBU',
                'Protéinurie',
                'Créatininurie',
                'Microalbuminurie'
            ],
            'Analyses bactériologiques' => [
                'Hémoculture',
                'Coproculture',
                'Prélèvement gorge',
                'Prélèvement vaginal'
            ],
            'Analyses hormonales' => [
                'TSH',
                'T3/T4',
                'Cortisol',
                'Insuline',
                'HbA1c'
            ],
            'Autres analyses' => [
                'Électrophorèse des protéines',
                'Marqueurs tumoraux',
                'Sérologie',
                'Parasitologie'
            ]
        ];
        
        return view('medecin.analyses.create', compact('patients', 'typesAnalyses'));
    }
    
    /**
     * Enregistrer une nouvelle analyse
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type_analyse' => 'required|string|max:255',
            'date_analyse' => 'required|date',
            'resultats' => 'nullable|string',
            'etat' => 'required|in:programmee,en_cours,terminee,annulee',
            'remarques' => 'nullable|string|max:1000'
        ]);
        
        $analyse = Analyses::create([
            'patient_id' => $request->patient_id,
            'medecin_id' => Auth::id(),
            'type_analyse' => $request->type_analyse,
            'date_analyse' => $request->date_analyse,
            'resultats' => $request->resultats,
            'etat' => $request->etat,
        ]);
        
        // Si des remarques sont ajoutées, on peut les stocker dans un champ séparé si nécessaire
        
        return redirect()->route('medecin.analyses.index')
            ->with('success', 'Analyse ajoutée avec succès.');
    }
    
    /**
     * Afficher les détails d'une analyse
     */
    public function show($id)
    {
        $analyse = Analyses::with(['patient', 'medecin'])
            ->where('id', $id)
            ->where('medecin_id', Auth::id())
            ->firstOrFail();
            
        return view('medecin.analyses.show', compact('analyse'));
    }
    
    /**
     * Afficher le formulaire d'édition d'une analyse
     */
    public function edit($id)
    {
        $analyse = Analyses::where('id', $id)
            ->where('medecin_id', Auth::id())
            ->firstOrFail();
            
        $medecinId = Auth::id();
        $patients = Patient::whereHas('consultations', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        })->orderBy('nom')->get();
        
        return view('medecin.analyses.edit', compact('analyse', 'patients'));
    }
    
    /**
     * Mettre à jour une analyse
     */
    public function update(Request $request, $id)
    {
        $analyse = Analyses::where('id', $id)
            ->where('medecin_id', Auth::id())
            ->firstOrFail();
            
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type_analyse' => 'required|string|max:255',
            'date_analyse' => 'required|date',
            'resultats' => 'nullable|string',
            'etat' => 'required|in:programmee,en_cours,terminee,annulee'
        ]);
        
        $analyse->update([
            'patient_id' => $request->patient_id,
            'type_analyse' => $request->type_analyse,
            'date_analyse' => $request->date_analyse,
            'resultats' => $request->resultats,
            'etat' => $request->etat,
        ]);
        
        return redirect()->route('medecin.analyses.index')
            ->with('success', 'Analyse mise à jour avec succès.');
    }
    
    /**
     * Supprimer une analyse
     */
    public function destroy($id)
    {
        $analyse = Analyses::where('id', $id)
            ->where('medecin_id', Auth::id())
            ->firstOrFail();
            
        $analyse->delete();
        
        return redirect()->route('medecin.analyses.index')
            ->with('success', 'Analyse supprimée avec succès.');
    }
    
    /**
     * Exporter les analyses en CSV
     */
    public function exportCsv(Request $request)
    {
        $medecinId = Auth::id();
        
        $query = Analyses::with(['patient', 'medecin'])
            ->where('medecin_id', $medecinId);
            
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->filled('type_analyse')) {
            $query->where('type_analyse', 'like', '%' . $request->type_analyse . '%');
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_analyse', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_analyse', '<=', $request->date_fin);
        }
        
        $analyses = $query->orderBy('date_analyse', 'desc')->get();
        
        $filename = 'analyses_' . Auth::user()->name . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($analyses) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Date',
                'Patient',
                'Type d\'analyse',
                'Résultats',
                'État',
                'Médecin'
            ], ';');
            
            // Données
            foreach ($analyses as $analyse) {
                fputcsv($file, [
                    $analyse->date_analyse ? Carbon::parse($analyse->date_analyse)->format('d/m/Y') : '',
                    $analyse->patient ? $analyse->patient->nom . ' ' . $analyse->patient->prenom : '',
                    $analyse->type_analyse,
                    $analyse->resultats,
                    $analyse->etat,
                    $analyse->medecin ? $analyse->medecin->name : ''
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Exporter les analyses en PDF
     */
    public function exportPdf(Request $request)
    {
        $medecinId = Auth::id();
        
        $query = Analyses::with(['patient', 'medecin'])
            ->where('medecin_id', $medecinId);
            
        // Appliquer les filtres
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->filled('type_analyse')) {
            $query->where('type_analyse', 'like', '%' . $request->type_analyse . '%');
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_analyse', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_analyse', '<=', $request->date_fin);
        }
        
        $analyses = $query->orderBy('date_analyse', 'desc')->get();
        $medecin = Auth::user();
        
        $data = [
            'analyses' => $analyses,
            'medecin' => $medecin,
            'dateExport' => now(),
            'filtres' => $request->only(['patient_id', 'type_analyse', 'date_debut', 'date_fin'])
        ];
        
        // Si DomPDF est installé
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('medecin.analyses.export_pdf', $data);
            $filename = 'analyses_' . $medecin->name . '_' . date('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        }
        
        // Sinon, export HTML
        $html = view('medecin.analyses.export_pdf', $data)->render();
        $filename = 'analyses_' . $medecin->name . '_' . date('Y-m-d') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * API pour récupérer les analyses d'un patient (pour le temps réel)
     */
    public function getPatientAnalyses($patientId)
    {
        $analyses = Analyses::where('patient_id', $patientId)
            ->where('medecin_id', Auth::id())
            ->orderBy('date_analyse', 'desc')
            ->take(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'analyses' => $analyses
        ]);
    }
}
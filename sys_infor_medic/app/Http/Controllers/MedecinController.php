<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultations;
use App\Models\Dossier_medicaux;
use App\Models\Ordonnances;
use App\Models\Patient;

class MedecinController extends Controller
{
    // Tableau de bord du médecin
    public function dashboard()
    {
        $medecin = Auth::user();

        $consultations = Consultations::where('medecin_id', $medecin->id)
            ->where('date_consultation', '>=', now())
            ->orderBy('date_consultation')
            ->take(5)
            ->get();

        $dossiers = Dossier_medicaux::orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $patients = Patient::orderBy('nom')->get();

        $notifications = collect();

        return view('medecin.dashboard', compact('medecin', 'consultations', 'dossiers', 'patients', 'notifications'));
    }

    // Liste des dossiers patients
    public function dossierpatient()
    {
        $patients = Dossier_medicaux::with('patient')->paginate(10);
        return view('medecin.dossierpatient', compact('patients'));
    }

    // Vue des consultations (calendrier + liste)
    public function consultations()
    {
        $medecin = Auth::user();

        $consultations = Consultations::where('medecin_id', $medecin->id)
            ->with('patient')
            ->orderBy('date_consultation', 'desc')
            ->paginate(10);

        $patients = Patient::select('id', 'nom', 'prenom')
            ->orderBy('nom')
            ->get();

        return view('medecin.consultations', compact('consultations', 'patients'));
    }

    // Vue des ordonnances
    public function ordonnances()
    {
        $medecin = Auth::user();

        $ordonnances = Ordonnances::where('medecin_id', $medecin->id)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('medecin.ordonnances', compact('ordonnances'));
    }

    // Retourner toutes les consultations pour FullCalendar
    public function getConsultations()
    {
        $medecin = Auth::user();

        $consultations = Consultations::where('medecin_id', $medecin->id)
            ->with('patient')
            ->get();

        $events = $consultations->map(function ($c) {
            return [
                'id' => $c->id,
                'title' => ($c->patient->nom ?? 'Patient inconnu') . ' - ' . ($c->symptomes ?? 'Consultation'),
                'start' => $c->date_consultation,
                'end' => $c->date_consultation, // LIGNE AJOUTÉE
                'extendedProps' => [
                    'statut'     => $c->statut,
                    'diagnostic' => $c->diagnostic,
                    'traitement' => $c->traitement,
                    'symptomes'  => $c->symptomes,
                    'patient_id' => $c->patient_id,
                ],
            ];
        });

        return response()->json($events);
    }

    // Enregistrer une nouvelle consultation (Ajax)
    public function storeConsultation(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string|max:255',
            'diagnostic' => 'nullable|string|max:255',
            'traitement' => 'nullable|string|max:255',
            'statut' => 'required|string',
        ]);

        $consultation = Consultations::create([
            'patient_id' => $request->patient_id,
            'medecin_id' => Auth::id(),
            'date_consultation' => $request->date_consultation,
            'symptomes' => $request->symptomes,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'statut' => $request->input('statut', 'En attente'),
        ]);

        return response()->json($consultation);
    }

    // Mettre à jour une consultation existante (Ajax)
    public function updateConsultation(Request $request, $id)
    {
        $request->validate([
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string|max:255',
            'diagnostic' => 'nullable|string|max:255',
            'traitement' => 'nullable|string|max:255',
            'statut' => 'required|string',
        ]);

        $consultation = Consultations::findOrFail($id);

        $consultation->update([
            'date_consultation' => $request->date_consultation,
            'symptomes' => $request->symptomes,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'statut' => $request->statut,
        ]);

        return response()->json($consultation);
    }

    // Supprimer une consultation (Ajax)
    public function deleteConsultation($id)
    {
        $consultation = Consultations::findOrFail($id);
        $consultation->delete();

        return response()->json(['success' => true]);
    }
}
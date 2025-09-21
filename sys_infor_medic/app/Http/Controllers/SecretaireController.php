<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Admissions;
use App\Models\User;
use Carbon\Carbon;

class SecretaireController extends Controller
{
    public function dashboard()
    {
        $totalPatients = Patient::count();
        $months = [];
        $rendezvousData = [];
        $admissionsData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');
            $rendezvousData[] = Rendez_vous::whereYear('date', $month->year)
                                           ->whereMonth('date', $month->month)
                                           ->count();
            $admissionsData[] = Admissions::whereYear('date_admission', $month->year)
                                         ->whereMonth('date_admission', $month->month)
                                         ->count();
        }

        return view('secretaire.dashboard', compact('totalPatients','months','rendezvousData','admissionsData'));
    }

    public function dossiersAdmin()
    {
        $patients = Patient::with('dossier_administratifs')->get();
        return view('secretaire.dossieradmin', compact('patients'));
    }

    public function rendezvous()
    {
        $rendezvous = Rendez_vous::with('patient', 'medecin')->get();
        $patients = Patient::all();
        $medecins = User::where('role','medecin')->get();
        return view('secretaire.rendezvous', compact('rendezvous','patients','medecins'));
    }

    public function storeRdv(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'heure' => 'required',
            'motif' => 'nullable|string|max:255',
        ]);

        Rendez_vous::create([
            'user_id' => $request->patient_id,
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous planifié avec succès.');
    }

    public function confirmRdv($id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        $rdv->statut = 'confirmé';
        $rdv->save();
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous confirmé.');
    }

    public function cancelRdv($id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        $rdv->statut = 'annulé';
        $rdv->save();
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous annulé.');
    }

    public function admissions()
    {
        $admissions = Admissions::with('patient')->get();
        $patients = Patient::all(); 
        return view('secretaire.admissions', compact('admissions', 'patients'));
    }
    // Ajouter ces méthodes

public function storePatient(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'nullable|email',
        'telephone' => 'nullable|string|max:20',
        'sexe' => 'nullable|string',
        'date_naissance' => 'nullable|date',
        'adresse' => 'nullable|string',
        'groupe_sanguin' => 'nullable|string',
        'antecedents' => 'nullable|string',
    ]);

    Patient::create($request->all());

    return redirect()->route('secretaire.dossiersAdmin')->with('success', 'Patient ajouté avec succès.');
}

public function updatePatient(Request $request, $id)
{
    $patient = Patient::findOrFail($id);

    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'nullable|email',
        'telephone' => 'nullable|string|max:20',
        'sexe' => 'nullable|string',
        'date_naissance' => 'nullable|date',
        'adresse' => 'nullable|string',
        'groupe_sanguin' => 'nullable|string',
        'antecedents' => 'nullable|string',
    ]);

    $patient->update($request->all());

    return redirect()->route('secretaire.dossiersAdmin')->with('success', 'Patient modifié avec succès.');
}
// Ajouter une nouvelle admission
public function storeAdmission(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date_admission' => 'required|date',
        'motif' => 'required|string|max:255',
    ]);

    Admissions::create([
        'patient_id' => $request->patient_id,
        'date_admission' => $request->date_admission,
        'motif' => $request->motif,
    ]);

    return redirect()->route('secretaire.admissions')->with('success', 'Admission ajoutée avec succès.');
}

// Mettre à jour une admission existante
public function updateAdmission(Request $request, $id)
{
    $admission = Admissions::findOrFail($id);

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date_admission' => 'required|date',
        'motif' => 'required|string|max:255',
    ]);

    $admission->update([
        'patient_id' => $request->patient_id,
        'date_admission' => $request->date_admission,
        'motif' => $request->motif,
    ]);

    return redirect()->route('secretaire.admissions')->with('success', 'Admission mise à jour avec succès.');
}

}


<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Liste utilisateurs (tous) avec filtres
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        if ($request->filled('active') && in_array($request->active, ['0','1'], true)) {
            $query->where('active', (bool)$request->active);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q){
                $qq->where('name','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            });
        }
        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'role' => $request->role ?? 'all',
                'active' => $request->active ?? 'all',
                'q' => $request->q ?? '',
            ],
        ]);
    }

    // Export CSV des utilisateurs filtrés
    public function exportCsv(Request $request)
    {
        $query = User::query();
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        if ($request->filled('active') && in_array($request->active, ['0','1'], true)) {
            $query->where('active', (bool)$request->active);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q){
                $qq->where('name','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            });
        }
        $rows = $query->orderBy('name')->get(['name','email','role','active','created_at']);
        $filename = 'utilisateurs_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Nom','Email','Rôle','Actif','Créé le'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name,
                    $r->email,
                    $r->role,
                    $r->active ? '1' : '0',
                    optional($r->created_at)->format('Y-m-d H:i:s'),
                ], ';');
            }
            fclose($out);
        }, $filename, $headers);
    }

    // Liste utilisateurs (hors patients) - conservé pour dashboard
    public function dashboard()
    {
        $users = User::where('role', '!=', 'patient')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|in:admin,medecin,infirmier,secretaire',
            'password'   => 'required|string|min:6|confirmed',
            'active'     => 'nullable|boolean',
            // Champs optionnels
            'specialite' => 'nullable|string|max:255',
            'pro_phone'  => 'nullable|string|max:255',
            'matricule'  => 'nullable|string|max:255',
            'cabinet'    => 'nullable|string|max:255',
            'horaires'   => 'nullable|string|max:2000',
        ]);
        $data['password'] = bcrypt($data['password']);
        // Nettoyage selon rôle
        if ($data['role'] !== 'medecin') {
            unset($data['specialite'], $data['matricule'], $data['cabinet'], $data['horaires']);
        }
        if (!in_array($data['role'], ['secretaire','infirmier','admin'])) {
            unset($data['pro_phone']);
        }
        $data['active'] = (bool)($request->input('active', true));
        User::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur ajouté');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => "required|email|unique:users,email,{$id}",
'role'       => 'required|in:admin,medecin,infirmier,secretaire',
            'password'   => 'nullable|string|min:6|confirmed',
            'active'     => 'nullable|boolean',
            // Champs optionnels
            'specialite' => 'nullable|string|max:255',
            'pro_phone'  => 'nullable|string|max:255',
            'matricule'  => 'nullable|string|max:255',
            'cabinet'    => 'nullable|string|max:255',
            'horaires'   => 'nullable|string|max:2000',
        ]);
        if ($data['password'] ?? false) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        // Nettoyage selon rôle
        if ($data['role'] !== 'medecin') {
            unset($data['specialite'], $data['matricule'], $data['cabinet'], $data['horaires']);
        }
        if (!in_array($data['role'], ['secretaire','infirmier','admin'])) {
            unset($data['pro_phone']);
        }
        $data['active'] = (bool)($request->input('active', $user->active));
        $user->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur modifié');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur supprimé');
    }

    // Patients
public function patientsList(\Illuminate\Http\Request $request)
    {
        $query = User::where('role', 'patient');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q){
                $qq->where('name','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            });
        }
        if ($request->filled('active') && in_array($request->active, ['0','1'], true)) {
            $query->where('active', (bool)$request->active);
        }
        $patients = $query->orderBy('name')->get();
        return view('admin.patients.index', compact('patients'));
    }

    public function createPatient()
    {
        $secretaires = User::where('role','secretaire')->orderBy('name')->get();
        return view('admin.patients.create', compact('secretaires'));
    }

    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'nom'             => ['required','string','max:255'],
            'prenom'          => ['required','string','max:255'],
            'email'           => ['required','email','unique:users,email'],
            'telephone'       => ['nullable','string','max:255'],
            'sexe'            => ['required','string','in:Masculin,Féminin'],
            'date_naissance'  => ['required','date'],
            'adresse'         => ['nullable','string','max:255'],
            'groupe_sanguin'  => ['nullable','string','max:255'],
            'antecedents'     => ['nullable','string'],
            'password'        => ['nullable','string','min:6','confirmed'],
        ]);

        $password = $validated['password'] ?? \Illuminate\Support\Str::random(8);

        $user = User::create([
            'name'     => $validated['prenom'].' '.$validated['nom'],
            'email'    => $validated['email'],
            'password' => bcrypt($password),
            'role'     => 'patient',
        ]);

        // Créer / lier la fiche Patient
        $numero_dossier = 'PAT' . now()->format('Ymd') . str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $user->patient()->create([
            'numero_dossier'  => $numero_dossier,
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'user_id'        => $user->id,
            'secretary_user_id' => $request->input('secretary_user_id') ?: null,
            'sexe'           => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'adresse'        => $validated['adresse'] ?? null,
            'email'          => $validated['email'],
            'telephone'      => $validated['telephone'] ?? null,
            'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
            'antecedents'    => $validated['antecedents'] ?? null,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Patient ajouté avec succès');
    }

    public function editPatient($id)
    {
        $patient = User::findOrFail($id);
        $secretaires = User::where('role','secretaire')->orderBy('name')->get();
        return view('admin.patients.edit', compact('patient','secretaires'));
    }

    public function updatePatient(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'nom'             => ['required','string','max:255'],
            'prenom'          => ['required','string','max:255'],
            'email'           => ["required","email","unique:users,email,{$id}"],
            'telephone'       => ['nullable','string','max:255'],
            'sexe'            => ['required','string','in:Masculin,Féminin'],
            'date_naissance'  => ['required','date'],
            'adresse'         => ['nullable','string','max:255'],
            'groupe_sanguin'  => ['nullable','string','max:255'],
            'antecedents'     => ['nullable','string'],
            'password'        => ['nullable','string','min:6','confirmed'],
        ]);

        $user->name  = $validated['prenom'].' '.$validated['nom'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        // Mettre à jour / créer la fiche patient associée
        $patient = $user->patient ?: $user->patient()->create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'user_id' => $user->id,
            'sexe' => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'email' => $validated['email'],
        ]);
        $patient->update([
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'sexe'           => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'adresse'        => $validated['adresse'] ?? null,
            'email'          => $validated['email'],
            'telephone'      => $validated['telephone'] ?? null,
            'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
            'antecedents'    => $validated['antecedents'] ?? null,
            'secretary_user_id' => $request->input('secretary_user_id') ?: $patient->secretary_user_id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Patient modifié avec succès');
    }

    public function destroyPatient($id)
    {
        User::destroy($id);
        return redirect()->route('admin.dashboard')->with('success', 'Patient supprimé');
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'role' => 'required|in:admin,medecin,infirmier,secretaire,patient',
        ]);
        $user->update(['role' => $data['role']]);
        return redirect()->route('admin.dashboard')->with('success', 'Rôle mis à jour');
    }

    public function updateActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'active' => 'required|boolean',
        ]);
        $before = ['active' => (bool)$user->active];
        $user->update(['active' => (bool)$validated['active']]);
        $after = ['active' => (bool)$user->active];
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_active_updated',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'changes' => ['before' => $before, 'after' => $after],
        ]);
        return redirect()->route('admin.dashboard')->with('success', 'Statut du compte mis à jour');
    }
}

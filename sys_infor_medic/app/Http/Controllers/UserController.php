<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HasPagination;

class UserController extends Controller
{
    use HasPagination;
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
        $users = $query->with(['nurses:id,name','doctors:id,name'])->orderBy('name')->paginate(20)->withQueryString();
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
            // Informations personnelles
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'     => 'nullable|in:Masculin,Féminin,Autre',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            // Informations professionnelles
            'department' => 'nullable|string|max:255',
            'hire_date'  => 'nullable|date|before_or_equal:today',
            'salary'     => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string|max:1000',
            // Champs spécifiques par rôle
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
            // Informations personnelles
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'     => 'nullable|in:Masculin,Féminin,Autre',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            // Informations professionnelles
            'department' => 'nullable|string|max:255',
            'hire_date'  => 'nullable|date|before_or_equal:today',
            'salary'     => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string|max:1000',
            // Champs spécifiques par rôle
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
        $user = User::findOrFail($id);
        
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        // Soft delete (sécurisé)
        $user->delete();
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Utilisateur supprimé avec succès. Il peut être restauré si nécessaire.');
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
        $patients = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('admin.patients.index', [
            'patients' => $patients,
            'filters' => [
                'q' => $request->q ?? '',
                'active' => $request->active ?? 'all',
            ],
        ]);
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
        $user = User::findOrFail($id);
        
        // Vérifier que c'est bien un patient
        if ($user->role !== 'patient') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Seuls les comptes patients peuvent être supprimés via cette méthode.');
        }
        
        // Soft delete (sécurisé pour les dossiers médicaux)
        $user->delete();
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Patient supprimé avec succès. Le dossier peut être restauré si nécessaire.');
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
            'event_type' => 'update',
            'severity' => 'medium',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'changes' => ['before' => $before, 'after' => $after],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('admin.dashboard')->with('success', 'Statut du compte mis à jour');
    }
    
    /**
     * Afficher la liste des utilisateurs supprimés (corbeille)
     */
    public function trashed(Request $request)
    {
        $query = User::onlyTrashed();
        
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q){
                $qq->where('name','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            });
        }
        
        $trashedUsers = $query->orderBy('deleted_at', 'desc')->paginate(20)->withQueryString();
        
        return view('admin.users.trashed', [
            'users' => $trashedUsers,
            'filters' => [
                'role' => $request->role ?? 'all',
                'q' => $request->q ?? '',
            ],
        ]);
    }
    
    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if (!$user->trashed()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cet utilisateur n\'est pas supprimé.');
        }
        
        $user->restore();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur restauré avec succès.');
    }
    
    /**
     * Supprimer définitivement un utilisateur
     */
    public function forceDestroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Empêcher la suppression définitive de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.trashed')
                ->with('error', 'Vous ne pouvez pas supprimer définitivement votre propre compte.');
        }
        
        // Supprimer définitivement (irréversible !)
        $user->forceDelete();
        
        return redirect()->route('admin.users.trashed')
            ->with('success', 'Utilisateur supprimé définitivement. Cette action est irréversible.');
    }
}

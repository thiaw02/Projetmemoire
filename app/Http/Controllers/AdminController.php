<?php

namespace App\Http\Controllers;

use App\Models\{User, Patient, Rendez_vous, Consultations, RolePermission};
use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Mail};
use App\Mail\InscriptionConfirmee;

class AdminController extends BaseController
{
    public function dashboard()
    {
        try {
            // Obtenir toutes les données via le service optimisé
            $dashboardData = DataService::getAdminDashboardStats();
            
            // Utilisateurs pour affichage avec pagination - prioriser les rôles administratifs
            $users = User::with(['nurses:id,name', 'doctors:id,name'])
                ->select('id', 'name', 'email', 'role', 'active', 'created_at', 'specialite')
                ->orderByRaw("CASE 
                    WHEN role = 'admin' THEN 1 
                    WHEN role = 'medecin' THEN 2 
                    WHEN role = 'secretaire' THEN 3 
                    WHEN role = 'infirmier' THEN 4 
                    ELSE 5 END")
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'users_page')
                ->withQueryString();
            
            // Patients pour l'onglet "Gérer patients" (pagination dédiée)
            $patients = User::where('role','patient')
                ->orderBy('name')
                ->paginate(10, ['*'], 'patients_page')
                ->withQueryString();

            // Debug : vérifier si les utilisateurs sont récupérés
            if ($users->isEmpty()) {
                \Log::warning('Aucun utilisateur trouvé pour le dashboard admin');
            } else {
                \Log::info('Utilisateurs récupérés pour dashboard : ' . $users->count());
            }

            // Extraction des données
            $userStats = $dashboardData['user_stats'];
            $monthlyData = $dashboardData['monthly_data'];
            $kpis = $dashboardData['kpis'];
            
            // Format legacy pour compatibilité avec la vue
            $rolesCount = [
                'admin' => $userStats['admin']['total'] ?? 0,
                'medecin' => $userStats['medecin']['total'] ?? 0,
                'infirmier' => $userStats['infirmier']['total'] ?? 0,
                'secretaire' => $userStats['secretaire']['total'] ?? 0,
                'patient' => $userStats['patient']['total'] ?? 0,
            ];

            // Utiliser les données du service pour les graphiques
            $months = $monthlyData['months'];
            $rendezvousCounts = $monthlyData['rdv_total'];
            $admissionsCounts = array_fill(0, count($months), 0); // Non implémenté
            $consultationsCounts = $monthlyData['consultations'];
            $patientsCounts = $monthlyData['patients'];
            $rdvPendingSeries = $monthlyData['rdv_pending'];
            $rdvConfirmedSeries = $monthlyData['rdv_confirmed'];
            $rdvCancelledSeries = $monthlyData['rdv_cancelled'];

            // Répartition des statuts de RDV avec cache
            $rdvStatusCounts = $this->cacheRemember('rdv_status_counts', function() {
                return Rendez_vous::select('statut', DB::raw('COUNT(*) as total'))
                    ->groupBy('statut')
                    ->pluck('total','statut')
                    ->toArray();
            }, 600);

            // Log de l'action
            $this->logAction('dashboard_access');
            
            // Appliquer la matrice de permissions demandée (idempotent)
            $this->applyConfiguredPermissions();

            // Modules et permissions pour la vue (regroupement lisible)
            $permissionModules = [
                [
                    'icon' => 'bi-gear',
                    'title' => 'Administration',
                    'permissions' => [
                        ['key' => 'users.manage', 'label' => 'Gérer les utilisateurs'],
                        ['key' => 'roles.manage', 'label' => 'Gérer les rôles & permissions'],
                    ],
                ],
                [
                    'icon' => 'bi-clipboard2-pulse',
                    'title' => 'Médical',
                    'permissions' => [
                        ['key' => 'medical.view_all', 'label' => 'Voir dossiers médicaux'],
                        ['key' => 'medical.edit', 'label' => 'Modifier dossiers médicaux'],
                        ['key' => 'prescriptions.create', 'label' => 'Émettre des prescriptions'],
                        ['key' => 'soins.record', 'label' => 'Enregistrer soins'],
                        ['key' => 'vitals.record', 'label' => 'Suivre constantes vitales'],
                        ['key' => 'diagnostics.view', 'label' => 'Voir diagnostics'],
                    ],
                ],
                [
                    'icon' => 'bi-calendar-check',
                    'title' => 'Rendez-vous & Admissions',
                    'permissions' => [
                        ['key' => 'consultations.schedule', 'label' => 'Planifier des consultations'],
                        ['key' => 'admissions.manage', 'label' => 'Gérer les admissions'],
                    ],
                ],
                [
                    'icon' => 'bi-person-hearts',
                    'title' => 'Patients',
                    'permissions' => [
                        ['key' => 'patients.create', 'label' => 'Créer patients'],
                        ['key' => 'self.view', 'label' => 'Voir son propre dossier'],
                        ['key' => 'self.download', 'label' => 'Télécharger ordonnances/résultats'],
                        ['key' => 'rdv.request', 'label' => 'Demander un rendez-vous'],
                    ],
                ],
            ];

            // Permissions effectives par rôle (pour affichage)
            $rolePermissions = $this->getConfiguredRolePermissions();

            return view('admin.dashboard', compact(
                'users', 'patients', 'rolesCount', 'months', 'rendezvousCounts', 
                'admissionsCounts', 'consultationsCounts', 'patientsCounts',
                'rdvPendingSeries', 'rdvConfirmedSeries', 'rdvCancelledSeries',
                'kpis', 'rdvStatusCounts', 'permissionModules', 'rolePermissions'
            ));
            
        } catch (\Exception $e) {
            return $this->handleException($e, ['action' => 'admin_dashboard']);
        }
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'role' => 'required|string',
            'password' => 'required|string',
        ]);

        $password = $data['password'];
        $data['password'] = bcrypt($data['password']);

        // Nettoyage selon rôle et génération matricule
        if ($data['role'] !== 'medecin') {
            unset($data['specialite'], $data['cabinet'], $data['horaires']);
        }
        if (!in_array($data['role'], ['secretaire','infirmier','admin'])) {
            unset($data['pro_phone']);
        }
        if (in_array($data['role'], ['medecin','secretaire','infirmier'], true)) {
            $prefixMap = ['medecin' => 'MED', 'secretaire' => 'SEC', 'infirmier' => 'INF'];
            $prefix = $prefixMap[$data['role']] ?? 'USR';
            do {
                $candidate = $prefix.'-'.now()->format('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            } while (\App\Models\User::withTrashed()->where('matricule', $candidate)->exists());
            $data['matricule'] = $candidate;
        } else {
            unset($data['matricule']);
        }
        $data['active'] = (bool)($request->input('active', true));

        // Si un utilisateur soft-supprimé existe avec le même email, le supprimer définitivement
        if (!empty($data['email'])) {
            $trashed = \App\Models\User::withTrashed()->where('email', $data['email'])->first();
            if ($trashed && $trashed->trashed()) { try { $trashed->forceDelete(); } catch (\Throwable $e) { /* ignore */ } }
        }

        $user = User::create($data);

        // Envoyer les informations de compte par email
        try {
            if (!empty($user->email)) {
                $matr = $user->matricule ?? '—';
                Mail::to($user->email)->send(new InscriptionConfirmee($matr, $user->email, (string)$password, $user->role, now()->format('d/m/Y H:i')));
            }
        } catch (\Throwable $e) { Log::warning('Email creation user failed: '.$e->getMessage()); }

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur ajouté');
    }

    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string',
            'numero_dossier' => 'required|string',
            'sexe' => 'required|string',
            'date_naissance' => 'required|date',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string',
            'groupe_sanguin' => 'nullable|string',
            'antecedents' => 'nullable|string',
            'secretary_user_id' => 'nullable|integer',
            'services' => 'nullable|array',
        ]);

        $password = $validated['password'];

        // Si un utilisateur soft-supprimé existe avec le même email, le supprimer définitivement
        if (!empty($validated['email'])) {
            $trashed = \App\Models\User::withTrashed()->where('email', $validated['email'])->first();
            if ($trashed && $trashed->trashed()) { try { $trashed->forceDelete(); } catch (\Throwable $e) { /* ignore */ } }
        }
        $user = User::create([
            'name' => $validated['prenom'].' '.$validated['nom'],
            'email' => $validated['email'],
            'password' => bcrypt($password),
            'role' => 'patient',
        ]);

        $patient = $user->patient()->create([
            'numero_dossier' => $validated['numero_dossier'],
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'user_id' => $user->id,
            'secretary_user_id' => $request->input('secretary_user_id') ?: null,
            'sexe' => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'adresse' => $validated['adresse'] ?? null,
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
            'antecedents' => $validated['antecedents'] ?? null,
        ]);

        if (!empty($validated['services'])) {
            $patient->services()->sync($validated['services']);
        }

        // Envoyer les informations d'inscription au patient par email
        try {
            if (!empty($user->email)) {
                Mail::to($user->email)->send(new InscriptionConfirmee($validated['numero_dossier'], $user->email, (string)$password, 'patient', now()->format('d/m/Y H:i')));
            }
        } catch (\Throwable $e) { Log::warning('Email creation patient failed: '.$e->getMessage()); }

        return redirect()->route('admin.dashboard')->with('success', 'Patient ajouté avec succès');
    }

    private function getConfiguredRolePermissions(): array
    {
        // Matrice conforme à votre demande
        $allPerms = [
            'users.manage','roles.manage','medical.view_all','medical.edit','prescriptions.create',
            'consultations.schedule','patients.create','admissions.manage','soins.record','vitals.record',
            'diagnostics.view','self.view','self.download','rdv.request'
        ];

        $matrix = [
            'admin' => array_fill_keys($allPerms, true),
            'medecin' => [
                'users.manage' => false,
                'roles.manage' => false,
                'medical.view_all' => true,
                'medical.edit' => true,
                'prescriptions.create' => true,
                'consultations.schedule' => true,
                'patients.create' => false,
                'admissions.manage' => false,
                'soins.record' => false,
                'vitals.record' => false,
                'diagnostics.view' => true,
                'self.view' => true,
                'self.download' => true,
                'rdv.request' => false,
            ],
            'infirmier' => [
                'users.manage' => false,
                'roles.manage' => false,
                'medical.view_all' => true,
                'medical.edit' => false,
                'prescriptions.create' => false,
                'consultations.schedule' => false,
                'patients.create' => false,
                'admissions.manage' => false,
                'soins.record' => true,
                'vitals.record' => true,
                'diagnostics.view' => false,
                'self.view' => true,
                'self.download' => true,
                'rdv.request' => false,
            ],
            'secretaire' => [
                'users.manage' => false,
                'roles.manage' => false,
                'medical.view_all' => false,
                'medical.edit' => false,
                'prescriptions.create' => false,
                'consultations.schedule' => true,
                'patients.create' => true,
                'admissions.manage' => true,
                'soins.record' => false,
                'vitals.record' => false,
                'diagnostics.view' => false,
                'self.view' => true,
                'self.download' => false,
                'rdv.request' => false,
            ],
            'patient' => [
                'users.manage' => false,
                'roles.manage' => false,
                'medical.view_all' => false,
                'medical.edit' => false,
                'prescriptions.create' => false,
                'consultations.schedule' => false,
                'patients.create' => false,
                'admissions.manage' => false,
                'soins.record' => false,
                'vitals.record' => false,
                'diagnostics.view' => false,
                'self.view' => true,
                'self.download' => true,
                'rdv.request' => true,
            ],
        ];

        // Compléter les trous pour chaque rôle
        foreach ($matrix as $role => $perms) {
            foreach ($allPerms as $p) { if (!array_key_exists($p, $perms)) $matrix[$role][$p] = false; }
        }
        return $matrix;
    }

    private function applyConfiguredPermissions(): void
    {
        $matrix = $this->getConfiguredRolePermissions();
        foreach ($matrix as $role => $perms) {
            foreach ($perms as $key => $allowed) {
                RolePermission::updateOrCreate(
                    ['role' => $role, 'permission' => $key],
                    ['allowed' => (bool)$allowed]
                );
            }
        }
    }
    
    public function savePermissions(Request $request)
    {
        try {
            $this->checkPermission('admin.permissions');
            
            // Nouveau format: permits[role][permission]=on|off
            $permits = $request->input('permits', []);
            if (!is_array($permits)) { $permits = []; }

            // Rôles reconnus
            $roles = ['admin', 'secretaire', 'medecin', 'infirmier', 'patient'];
            $changed = false;
            foreach ($roles as $role) {
                $roleSet = $permits[$role] ?? [];
                if (!is_array($roleSet)) $roleSet = [];
                foreach ($roleSet as $permKey => $val) {
                    RolePermission::updateOrCreate(
                        ['role' => $role, 'permission' => $permKey],
                        ['allowed' => (bool)$val]
                    );
                    $changed = true;
                }
            }

            if ($changed) {
                $this->logAction('permissions_updated', null, ['roles' => $roles]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Permissions mises à jour avec succès.');
                
        } catch (\Exception $e) {
            return $this->handleException($e, ['action' => 'save_permissions']);
        }
    }
}

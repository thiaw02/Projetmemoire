<?php

namespace App\Http\Controllers;

use App\Models\{User, Patient, Rendez_vous, Consultations, RolePermission};
use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends BaseController
{
    public function dashboard()
    {
        try {
            // Obtenir toutes les données via le service optimisé
            $dashboardData = DataService::getAdminDashboardStats();
            
            // Utilisateurs récents pour affichage
            $users = $this->cacheRemember('admin_recent_users', function() {
                return User::select('id', 'name', 'role', 'active', 'created_at')
                    ->latest()
                    ->limit(50)
                    ->get();
            }, 300);

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
            
            // Modules de permissions pour la vue
            $permissionModules = $this->getPermissionModules();
            
            return view('admin.dashboard', compact(
                'users', 'rolesCount', 'months', 'rendezvousCounts', 
                'admissionsCounts', 'consultationsCounts', 'patientsCounts',
                'rdvPendingSeries', 'rdvConfirmedSeries', 'rdvCancelledSeries',
                'kpis', 'rdvStatusCounts', 'permissionModules'
            ));
            
        } catch (\Exception $e) {
            return $this->handleException($e, ['action' => 'admin_dashboard']);
        }
    }
    
    private function getPermissionModules()
    {
        return [
            'icon' => 'bi-shield-check',
            'title' => 'Indispensables',
            'permissions' => [
                ['key' => 'patients.view', 'label' => 'Voir patients'],
                ['key' => 'patients.create', 'label' => 'Créer patient'],
                ['key' => 'rdv.view', 'label' => 'Voir rendez-vous'],
                ['key' => 'rdv.create', 'label' => 'Créer rendez-vous'],
                ['key' => 'consultations.view', 'label' => 'Voir consultations'],
                ['key' => 'consultations.create', 'label' => 'Créer consultation'],
                ['key' => 'ordonnances.create', 'label' => 'Émettre ordonnance'],
            ],
        ];
    }
    
    public function savePermissions(Request $request)
    {
        try {
            $this->checkPermission('admin.permissions');
            
            $validationError = $this->validateRequest($request, [
                'levels' => 'required|array',
            ]);
            
            if ($validationError) {
                return $validationError;
            }
            
            $submitted = $request->input('levels', []);
            $roles = ['admin', 'secretaire', 'medecin', 'infirmier', 'patient'];
            
            if (isset($submitted['Indispensables'])) {
                $essentialPerms = [
                    'patients.view', 'patients.create', 'rdv.view', 'rdv.create',
                    'consultations.view', 'consultations.create', 'ordonnances.create'
                ];
                
                foreach ($roles as $role) {
                    $level = $submitted['Indispensables'][$role] ?? 'none';
                    foreach ($essentialPerms as $permKey) {
                        $allowed = ($level === 'full') || 
                                  ($level === 'read' && str_ends_with($permKey, '.view'));
                        
                        RolePermission::updateOrCreate(
                            ['role' => $role, 'permission' => $permKey],
                            ['allowed' => (bool)$allowed]
                        );
                    }
                }
                
                $this->logAction('permissions_updated', null, ['roles' => $roles]);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Permissions mises à jour avec succès.');
                
        } catch (\Exception $e) {
            return $this->handleException($e, ['action' => 'save_permissions']);
        }
    }
}

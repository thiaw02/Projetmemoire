<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::all();

        // Comptes par rôle
        $rolesCount = [
            'admin' => User::where('role','admin')->count(),
            'medecin' => User::where('role','medecin')->count(),
            'infirmier' => User::where('role','infirmier')->count(),
            'secretaire' => User::where('role','secretaire')->count(),
            'patient' => User::where('role','patient')->count(),
        ];

        // Séries mensuelles (jusqu'à 12 derniers mois: mois courant inclus)
        $months = [];
        $rendezvousCounts = [];
        $admissionsCounts = [];
        $consultationsCounts = [];
        $patientsCounts = [];
        $rdvPendingSeries = [];
        $rdvConfirmedSeries = [];
        $rdvCancelledSeries = [];
        for ($i=11; $i>=0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $months[] = $m->format('M');
            $rendezvousCounts[] = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)->count();
            $admissionsCounts[] = Admissions::whereYear('date_admission', $m->year)->whereMonth('date_admission', $m->month)->count();
            $consultationsCounts[] = Consultations::whereYear('date_consultation', $m->year)->whereMonth('date_consultation', $m->month)->count();
            $patientsCounts[] = Patient::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            // RDV par statut
            $pending = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['en_attente','en attente','pending'])->count();
            $confirmed = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['confirmé','confirme','confirmee','confirmée'])->count();
            $cancelled = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['annulé','annule','annulee','annulée','cancelled','canceled'])->count();
            $rdvPendingSeries[] = $pending;
            $rdvConfirmedSeries[] = $confirmed;
            $rdvCancelledSeries[] = $cancelled;
        }

        // KPI
        $kpis = [
            'totalUsers' => User::count(),
            'totalPatients' => $rolesCount['patient'],
            'rdvThisMonth' => Rendez_vous::whereYear('date', now()->year)->whereMonth('date', now()->month)->count(),
            'consultsThisMonth' => Consultations::whereYear('date_consultation', now()->year)->whereMonth('date_consultation', now()->month)->count(),
        ];

        // Répartition des statuts de RDV (tous)
        $rdvStatusCounts = Rendez_vous::select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total','statut')
            ->toArray();

        // Permissions disponibles et état actuel
        $availablePermissions = [
            ['key' => 'manage_users', 'label' => 'Gérer les utilisateurs'],
            ['key' => 'manage_patients', 'label' => 'Gérer les patients'],
            ['key' => 'view_stats', 'label' => 'Voir les statistiques'],
            ['key' => 'manage_rdv', 'label' => 'Gérer les rendez-vous'],
            ['key' => 'manage_consultations', 'label' => 'Gérer les consultations'],
        ];
        $rolePermissions = [];
        foreach (['admin','secretaire','medecin','infirmier','patient'] as $role) {
            $rolePermissions[$role] = [];
        }
        foreach (RolePermission::all() as $rp) {
            $rolePermissions[$rp->role][$rp->permission] = (bool)$rp->allowed;
        }

        return view('admin.dashboard', compact(
            'users','rolesCount','months','rendezvousCounts','admissionsCounts','consultationsCounts','patientsCounts','kpis','availablePermissions','rolePermissions','rdvStatusCounts','rdvPendingSeries','rdvConfirmedSeries','rdvCancelledSeries'
        ));
    }

    public function savePermissions(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'array',
        ]);
        $submitted = $validated['permissions'] ?? [];
        $roles = ['admin','secretaire','medecin','infirmier','patient'];
        $keys = ['manage_users','manage_patients','view_stats','manage_rdv','manage_consultations'];

        foreach ($roles as $role) {
            foreach ($keys as $perm) {
                $allowed = isset($submitted[$role]) && array_key_exists($perm, $submitted[$role]);
                RolePermission::updateOrCreate(
                    ['role' => $role, 'permission' => $perm],
                    ['allowed' => $allowed]
                );
            }
        }
        return redirect()->route('admin.dashboard')->with('success', 'Permissions mises à jour.');
    }
}

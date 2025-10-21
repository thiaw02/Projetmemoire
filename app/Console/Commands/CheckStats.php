<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use App\Models\Order;
use App\Models\Ordonnances;
use App\Models\Analyses;

class CheckStats extends Command
{
    protected $signature = 'stats:check';
    protected $description = 'Vérifier les statistiques des données';

    public function handle()
    {
        $this->info('=== STATISTIQUES DES DONNÉES ===');
        
        $totalUsers = User::count();
        $this->line("Total utilisateurs: $totalUsers");
        
        $rolesCount = [
            'admin' => User::where('role', 'admin')->count(),
            'secretaire' => User::where('role', 'secretaire')->count(),
            'medecin' => User::where('role', 'medecin')->count(),
            'infirmier' => User::where('role', 'infirmier')->count(),
            'patient' => User::where('role', 'patient')->count(),
        ];
        
        foreach ($rolesCount as $role => $count) {
            $this->line("  - $role: $count");
        }
        
        $this->line("Patients (table): " . Patient::count());
        $this->line("Rendez-vous: " . Rendez_vous::count());
        $this->line("Consultations: " . Consultations::count());
        $this->line("Admissions: " . Admissions::count());
        $this->line("Ordonnances: " . Ordonnances::count());
        $this->line("Analyses: " . Analyses::count());
        $this->line("Commandes: " . Order::count());
        
        // Statistiques récentes
        $this->info("\n=== DONNÉES RÉCENTES (30 jours) ===");
        $date30 = now()->subDays(30);
        
        $this->line("RDV récents: " . Rendez_vous::where('created_at', '>=', $date30)->count());
        $this->line("Consultations récentes: " . Consultations::where('created_at', '>=', $date30)->count());
        $this->line("Admissions récentes: " . Admissions::where('created_at', '>=', $date30)->count());
        $this->line("Paiements payés récents: " . Order::where('status', 'paid')->where('created_at', '>=', $date30)->count());
        
        // Vérifier les relations médecin-infirmier
        $this->info("\n=== RELATIONS MÉDECIN-INFIRMIER ===");
        $medecinsAvecInfirmiers = User::where('role', 'medecin')->whereHas('nurses')->count();
        $this->line("Médecins avec infirmiers: $medecinsAvecInfirmiers");
    }
}
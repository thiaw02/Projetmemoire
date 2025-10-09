<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STATISTIQUES DES DONNÉES ===\n";
echo "Utilisateurs: " . App\Models\User::count() . "\n";
echo "- Admins: " . App\Models\User::where('role', 'admin')->count() . "\n";
echo "- Secrétaires: " . App\Models\User::where('role', 'secretaire')->count() . "\n"; 
echo "- Médecins: " . App\Models\User::where('role', 'medecin')->count() . "\n";
echo "- Infirmiers: " . App\Models\User::where('role', 'infirmier')->count() . "\n";
echo "- Patients: " . App\Models\User::where('role', 'patient')->count() . "\n";

echo "\nPatients (table): " . App\Models\Patient::count() . "\n";
echo "Rendez-vous: " . App\Models\Rendez_vous::count() . "\n";
echo "Consultations: " . App\Models\Consultations::count() . "\n";
echo "Admissions: " . App\Models\Admissions::count() . "\n";
echo "Ordonnances: " . App\Models\Ordonnances::count() . "\n";
echo "Analyses: " . App\Models\Analyses::count() . "\n";
echo "Commandes: " . App\Models\Order::count() . "\n";
echo "Items de commande: " . App\Models\OrderItem::count() . "\n";

echo "\n=== STATISTIQUES RÉCENTES (30 derniers jours) ===\n";
$date30 = now()->subDays(30);
echo "RDV récents: " . App\Models\Rendez_vous::where('created_at', '>=', $date30)->count() . "\n";
echo "Consultations récentes: " . App\Models\Consultations::where('created_at', '>=', $date30)->count() . "\n";
echo "Admissions récentes: " . App\Models\Admissions::where('created_at', '>=', $date30)->count() . "\n";
echo "Commandes payées récentes: " . App\Models\Order::where('status', 'paid')->where('created_at', '>=', $date30)->count() . "\n";
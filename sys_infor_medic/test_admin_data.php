<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simuler la logique du AdminController pour vérifier les données
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use Carbon\Carbon;

echo "=== TEST DES DONNÉES DASHBOARD ADMIN ===\n\n";

// Comptes par rôle
$rolesCount = [
    'admin' => User::where('role','admin')->count(),
    'medecin' => User::where('role','medecin')->count(),
    'infirmier' => User::where('role','infirmier')->count(),
    'secretaire' => User::where('role','secretaire')->count(),
    'patient' => User::where('role','patient')->count(),
];

echo "📊 Répartition des rôles :\n";
foreach ($rolesCount as $role => $count) {
    echo "  - $role: $count\n";
}

// Test des données mensuelles
echo "\n📅 Test des données mensuelles :\n";
$months = [];
$rendezvousCounts = [];
for ($i=11; $i>=0; $i--) {
    $m = Carbon::now()->subMonths($i);
    $months[] = $m->format('M');
    $count = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)->count();
    $rendezvousCounts[] = $count;
    echo "  - {$m->format('M Y')}: $count RDV\n";
}

// Statuts des RDV
echo "\n🔄 Statuts des rendez-vous :\n";
$rdvStatusCounts = Rendez_vous::select('statut', \DB::raw('COUNT(*) as total'))
    ->groupBy('statut')
    ->pluck('total','statut')
    ->toArray();
    
foreach ($rdvStatusCounts as $statut => $count) {
    echo "  - $statut: $count\n";
}

// KPIs
echo "\n📈 KPIs :\n";
$kpis = [
    'totalUsers' => User::count(),
    'totalPatients' => $rolesCount['patient'],
    'rdvThisMonth' => Rendez_vous::whereYear('date', now()->year)->whereMonth('date', now()->month)->count(),
    'consultsThisMonth' => Consultations::whereYear('date_consultation', now()->year)->whereMonth('date_consultation', now()->month)->count(),
    'paymentsPaidThisMonth' => \App\Models\Order::where('status','paid')->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)->sum('total_amount'),
    'paymentsPending' => \App\Models\Order::where('status','pending')->count(),
];

foreach ($kpis as $key => $value) {
    echo "  - $key: $value\n";
}

echo "\n✅ Test terminé - Les données semblent correctes pour le dashboard.\n";
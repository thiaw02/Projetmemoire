<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class EnhanceStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Amélioration des données statistiques...');
        
        // Créer plus de données étalées sur 12 mois pour les graphiques
        $this->createMonthlyData();
        
        // Ajouter plus de paiements avec différents statuts
        $this->createMorePayments();
        
        $this->command->info('✅ Données statistiques améliorées !');
    }
    
    private function createMonthlyData()
    {
        $this->command->info('📅 Création de données mensuelles...');
        
        $patients = User::where('role', 'patient')->get();
        $medecins = User::where('role', 'medecin')->get();
        $patientsTable = Patient::all();
        
        // Créer des données pour chaque mois des 12 derniers mois
        for ($month = 11; $month >= 0; $month--) {
            $date = Carbon::now()->subMonths($month);
            $monthName = $date->format('Y-m');
            
            // Nombre d'entrées par mois (plus récent = plus d'activité)
            $activityMultiplier = $month <= 3 ? 2 : 1; // Plus d'activité les 3 derniers mois
            
            // RDV du mois
            $rdvCount = rand(15, 30) * $activityMultiplier;
            for ($i = 0; $i < $rdvCount; $i++) {
                $rdvDate = $date->copy()->addDays(rand(0, 28));
                
                Rendez_vous::create([
                    'user_id' => $patients->random()->id,
                    'medecin_id' => $medecins->random()->id,
                    'date' => $rdvDate->format('Y-m-d'),
                    'heure' => sprintf('%02d:%02d', rand(8, 17), rand(0, 3) * 15),
                    'motif' => ['Consultation générale', 'Contrôle', 'Urgence', 'Suivi'][rand(0, 3)],
                    'statut' => ['en_attente', 'confirmé', 'annulé', 'terminé'][rand(0, 3)],
                    'created_at' => $rdvDate,
                    'updated_at' => $rdvDate,
                ]);
            }
            
            // Consultations du mois
            $consultCount = rand(20, 40) * $activityMultiplier;
            for ($i = 0; $i < $consultCount; $i++) {
                $consultDate = $date->copy()->addDays(rand(0, 28));
                
                Consultations::create([
                    'patient_id' => $patientsTable->random()->id,
                    'medecin_id' => $medecins->random()->id,
                    'date_consultation' => $consultDate->format('Y-m-d'),
                    'symptomes' => 'Symptômes divers',
                    'diagnostic' => ['Hypertension', 'Diabète', 'Grippe', 'Gastrite'][rand(0, 3)],
                    'traitement' => 'Traitement adapté',
                    'statut' => ['En attente', 'En cours', 'Terminée'][rand(0, 2)],
                    'created_at' => $consultDate,
                    'updated_at' => $consultDate,
                ]);
            }
            
            // Admissions du mois
            $admissionCount = rand(5, 15) * $activityMultiplier;
            for ($i = 0; $i < $admissionCount; $i++) {
                $admissionDate = $date->copy()->addDays(rand(0, 28));
                
                Admissions::create([
                    'patient_id' => $patientsTable->random()->id,
                    'date_admission' => $admissionDate->format('Y-m-d'),
                    'date_sortie' => rand(0, 1) ? $admissionDate->copy()->addDays(rand(1, 10)) : null,
                    'motif_admission' => ['Urgence', 'Chirurgie', 'Surveillance', 'Traitement'][rand(0, 3)],
                    'service' => ['Urgences', 'Chirurgie', 'Cardiologie', 'Médecine'][rand(0, 3)],
                    'observations' => 'Observations médicales',
                    'created_at' => $admissionDate,
                    'updated_at' => $admissionDate,
                ]);
            }
            
            // Nouveaux patients du mois (simuler inscription)
            if ($month <= 6) { // Nouveaux patients seulement pour les 6 derniers mois
                $newPatientCount = rand(2, 8);
                for ($i = 0; $i < $newPatientCount; $i++) {
                    $patientDate = $date->copy()->addDays(rand(0, 28));
                    
                    // Générer un identifiant unique avec timestamp
                    $timestamp = $patientDate->timestamp;
                    $uniqueId = $timestamp . rand(100, 999);
                    
                    $user = User::create([
                        'name' => "Patient $uniqueId",
                        'email' => "patient.$uniqueId@test.com",
                        'password' => 'password123',
                        'role' => 'patient',
                        'active' => true,
                        'created_at' => $patientDate,
                        'updated_at' => $patientDate,
                    ]);
                    
                    Patient::create([
                        'numero_dossier' => 'PA' . $uniqueId,
                        'nom' => 'Nom' . $uniqueId,
                        'prenom' => 'Prénom' . substr($uniqueId, -3),
                        'user_id' => $user->id,
                        'secretary_user_id' => User::where('role', 'secretaire')->inRandomOrder()->first()->id,
                        'sexe' => rand(0, 1) ? 'M' : 'F',
                        'date_naissance' => Carbon::now()->subYears(rand(20, 70)),
                        'adresse' => 'Adresse test',
                        'email' => $user->email,
                        'telephone' => '77' . rand(1000000, 9999999),
                        'groupe_sanguin' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-'][rand(0, 5)],
                        'created_at' => $patientDate,
                        'updated_at' => $patientDate,
                    ]);
                }
            }
        }
    }
    
    private function createMorePayments()
    {
        $this->command->info('💳 Création de paiements additionnels...');
        
        $users = User::where('role', 'patient')->get();
        $patients = Patient::all();
        
        // Créer des paiements pour chaque mois avec différents statuts
        for ($month = 11; $month >= 0; $month--) {
            $date = Carbon::now()->subMonths($month);
            $paymentsCount = rand(10, 25);
            
            for ($i = 0; $i < $paymentsCount; $i++) {
                $paymentDate = $date->copy()->addDays(rand(0, 28));
                $user = $users->random();
                $patient = $patients->where('user_id', $user->id)->first();
                
                // Distribution des statuts : 70% payé, 20% en attente, 10% annulé
                $rand = rand(1, 100);
                if ($rand <= 70) {
                    $status = 'paid';
                    $paidAt = $paymentDate;
                } elseif ($rand <= 90) {
                    $status = 'pending';
                    $paidAt = null;
                } else {
                    $status = 'cancelled';
                    $paidAt = null;
                }
                
                $services = [
                    'Consultation générale' => 15000,
                    'Consultation spécialisée' => 25000,
                    'Analyses médicales' => 35000,
                    'Vaccination' => 10000,
                    'Échographie' => 20000,
                    'Radiographie' => 18000,
                    'Bilan sanguin' => 30000,
                    'Hospitalisation (jour)' => 50000,
                    'Urgences' => 40000,
                ];
                
                $serviceLabel = array_rand($services);
                $amount = $services[$serviceLabel];
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'patient_id' => $patient ? $patient->id : null,
                    'currency' => 'XOF',
                    'total_amount' => $amount,
                    'status' => $status,
                    'provider' => ['wave', 'orange_money', 'cash'][rand(0, 2)],
                    'provider_ref' => 'REF' . rand(100000, 999999),
                    'paid_at' => $paidAt,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate,
                ]);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => 'service_medical',
                    'item_id' => rand(1, 100),
                    'label' => $serviceLabel,
                    'amount' => $amount,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate,
                ]);
            }
        }
    }
}
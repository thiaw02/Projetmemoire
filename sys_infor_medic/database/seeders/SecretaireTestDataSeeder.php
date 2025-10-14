<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Admissions;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class SecretaireTestDataSeeder extends Seeder
{
    public function run()
    {
        // Créer quelques patients de test
        $patients = [];
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'name' => fake()->firstName() . ' ' . fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('password'),
                'role' => 'patient',
                'active' => true,
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName(),
                'sexe' => fake()->randomElement(['Masculin', 'Féminin']),
                'date_naissance' => fake()->date(),
                'email' => $user->email,
                'telephone' => fake()->phoneNumber(),
                'adresse' => fake()->address(),
                'groupe_sanguin' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'numero_dossier' => 'PAT' . now()->format('Ymd') . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            ]);

            $patients[] = ['user' => $user, 'patient' => $patient];
        }

        // Récupérer des médecins existants ou en créer
        $medecins = User::where('role', 'medecin')->take(3)->get();
        if ($medecins->count() === 0) {
            for ($i = 0; $i < 3; $i++) {
                $medecins[] = User::create([
                    'name' => 'Dr. ' . fake()->lastName(),
                    'email' => 'medecin' . ($i + 1) . '@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'medecin',
                    'active' => true,
                ]);
            }
        }

        // Créer des rendez-vous variés
        foreach ($patients as $index => $patientData) {
            $rdvCount = fake()->numberBetween(0, 4);
            
            for ($j = 0; $j < $rdvCount; $j++) {
                $status = fake()->randomElement([
                    'en_attente', 'en_attente', 'en_attente', // Plus de rendez-vous en attente pour test
                    'confirmé', 'confirmé',
                    'terminé', 'annulé'
                ]);
                
                $date = fake()->dateTimeBetween('-2 months', '+2 months');
                
                Rendez_vous::create([
                    'user_id' => $patientData['user']->id,
                    'medecin_id' => fake()->randomElement($medecins)->id,
                    'date' => $date->format('Y-m-d'),
                    'heure' => fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00']),
                    'motif' => fake()->randomElement([
                        'Consultation générale',
                        'Contrôle de routine',
                        'Douleurs abdominales',
                        'Suivi médical',
                        'Vaccination',
                        'Examen de santé',
                        null // Quelques RDV sans motif
                    ]),
                    'statut' => $status,
                    'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
                ]);
            }
        }

        // Créer quelques admissions
        for ($i = 0; $i < 10; $i++) {
            $patient = fake()->randomElement($patients)['patient'];
            $dateAdmission = fake()->dateTimeBetween('-4 months', 'now');
            $dateSortie = fake()->boolean(70) ? fake()->dateTimeBetween($dateAdmission, 'now') : null;

            Admissions::create([
                'patient_id' => $patient->id,
                'date_admission' => $dateAdmission,
                'date_sortie' => $dateSortie,
                'motif_admission' => fake()->randomElement([
                    'Urgence médicale',
                    'Intervention chirurgicale',
                    'Observation',
                    'Traitement',
                ]),
                'created_at' => $dateAdmission,
            ]);
        }

        // Créer des commandes de paiement
        foreach (fake()->randomElements($patients, 15) as $patientData) {
            $orderCount = fake()->numberBetween(1, 3);
            
            for ($k = 0; $k < $orderCount; $k++) {
                $status = fake()->randomElement(['paid', 'paid', 'paid', 'pending', 'failed']);
                $amount = fake()->randomElement([5000, 10000, 7000, 15000]);
                $createdAt = fake()->dateTimeBetween('-3 months', 'now');
                $paidAt = $status === 'paid' ? fake()->dateTimeBetween($createdAt, 'now') : null;

                $order = Order::create([
                    'user_id' => $patientData['user']->id,
                    'patient_id' => $patientData['patient']->id,
                    'currency' => 'XOF',
                    'total_amount' => $amount,
                    'status' => $status,
                    'provider' => fake()->randomElement(['wave', 'orangemoney']),
                    'provider_ref' => 'TEST_' . time() . '_' . $k,
                    'created_at' => $createdAt,
                    'paid_at' => $paidAt,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => fake()->randomElement(['consultation', 'analyse', 'acte']),
                    'label' => fake()->randomElement([
                        'Consultation générale',
                        'Analyse sanguine',
                        'Radio thorax',
                        'Échographie',
                        'Acte médical'
                    ]),
                    'amount' => $amount,
                    'ticket_number' => 'TKT-' . strtoupper(substr(fake()->randomElement(['consultation', 'analyse', 'acte']), 0, 1)) . '-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                ]);
            }
        }

        $this->command->info('Données de test créées avec succès :');
        $this->command->info('- ' . count($patients) . ' patients');
        $this->command->info('- ' . Rendez_vous::count() . ' rendez-vous');
        $this->command->info('- ' . Admissions::count() . ' admissions');
        $this->command->info('- ' . Order::count() . ' commandes');
    }
}
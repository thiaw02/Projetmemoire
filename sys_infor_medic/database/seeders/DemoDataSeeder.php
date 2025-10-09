<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Analyses;
use App\Models\Ordonnances;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Création des données de démonstration...');
        
        // Vider les données existantes pour éviter les doublons
        $this->command->info('🧹 Nettoyage des données existantes...');
        $this->clearExistingData();
        
        // 1. Créer des utilisateurs de différents rôles
        $this->createUsers();
        
        // 2. Créer des patients
        $this->createPatients();
        
        // 3. Créer des relations médecin-infirmier
        $this->createMedecinInfirmierRelations();
        
        // 4. Créer des rendez-vous (sur plusieurs mois pour les stats)
        $this->createRendezVous();
        
        // 5. Créer des consultations
        $this->createConsultations();
        
        // 6. Créer des admissions
        $this->createAdmissions();
        
        // 7. Créer des ordonnances et analyses
        $this->createOrdonnancesAndAnalyses();
        
        // 8. Créer des commandes de paiement
        $this->createOrders();
        
        $this->command->info('✅ Données de démonstration créées avec succès !');
    }
    
    private function clearExistingData()
    {
        // Supprimer en respectant les contraintes de clés étrangères
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        Order::truncate();
        Analyses::truncate();
        Ordonnances::truncate();
        Consultations::truncate();
        Admissions::truncate();
        Rendez_vous::truncate();
        \DB::table('medecin_infirmier')->truncate();
        Patient::truncate();
        User::where('email', '!=', 'admin@medical.com')->delete(); // Garder l'admin principal
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    private function createUsers()
    {
        $this->command->info('👥 Création des utilisateurs...');
        
        // Admin principal
        User::firstOrCreate([
            'email' => 'admin@medical.com'
        ], [
            'name' => 'Administrateur Principal',
            'password' => 'password123',
            'role' => 'admin',
            'active' => true,
            'matricule' => 'ADM001'
        ]);
        
        // Secrétaires
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Secrétaire $i",
                'email' => "secretaire$i@medical.com",
                'password' => 'password123',
                'role' => 'secretaire',
                'active' => true,
                'matricule' => "SEC00$i",
                'pro_phone' => "77" . rand(100, 999) . rand(10, 99) . rand(10, 99)
            ]);
        }
        
        // Médecins avec spécialités
        $specialites = [
            'Cardiologie', 'Neurologie', 'Pédiatrie', 'Gynécologie', 
            'Orthopédie', 'Dermatologie', 'Pneumologie', 'Gastroentérologie'
        ];
        
        for ($i = 1; $i <= 8; $i++) {
            User::create([
                'name' => "Dr. Médecin $i",
                'email' => "medecin$i@medical.com",
                'password' => 'password123',
                'role' => 'medecin',
                'specialite' => $specialites[$i-1],
                'active' => true,
                'matricule' => "MED00$i",
                'pro_phone' => "77" . rand(100, 999) . rand(10, 99) . rand(10, 99),
                'cabinet' => "Cabinet " . chr(64 + $i),
                'horaires' => '08:00-17:00'
            ]);
        }
        
        // Infirmiers
        for ($i = 1; $i <= 12; $i++) {
            User::create([
                'name' => "Infirmier(e) $i",
                'email' => "infirmier$i@medical.com", 
                'password' => 'password123',
                'role' => 'infirmier',
                'active' => true,
                'matricule' => "INF0" . str_pad($i, 2, '0', STR_PAD_LEFT),
                'pro_phone' => "77" . rand(100, 999) . rand(10, 99) . rand(10, 99)
            ]);
        }
    }
    
    private function createPatients()
    {
        $this->command->info('👤 Création des patients...');
        
        $prenoms = [
            'M' => ['Mamadou', 'Moussa', 'Abdoulaye', 'Ibrahima', 'Cheikh', 'Omar', 'Ousmane', 'Alioune'],
            'F' => ['Aïssatou', 'Fatou', 'Aminata', 'Khadija', 'Mariame', 'Ndeye', 'Bineta', 'Coumba']
        ];
        $noms = ['Diop', 'Ndiaye', 'Fall', 'Sarr', 'Ba', 'Sy', 'Seck', 'Wade', 'Gueye', 'Diouf', 'Faye', 'Kane'];
        $groupesSanguins = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        for ($i = 1; $i <= 50; $i++) {
            $sexe = rand(0, 1) ? 'M' : 'F';
            $prenom = $prenoms[$sexe][array_rand($prenoms[$sexe])];
            $nom = $noms[array_rand($noms)];
            
            // Créer d'abord l'utilisateur patient
            $user = User::create([
                'name' => "$prenom $nom",
                'email' => strtolower($prenom . '.' . $nom . $i) . '@patient.com',
                'password' => 'password123',
                'role' => 'patient',
                'active' => true
            ]);
            
            // Puis créer le patient lié
            Patient::create([
                'numero_dossier' => 'P' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nom' => $nom,
                'prenom' => $prenom,
                'user_id' => $user->id,
                'secretary_user_id' => User::where('role', 'secretaire')->inRandomOrder()->first()->id,
                'sexe' => $sexe,
                'date_naissance' => Carbon::now()->subYears(rand(18, 80))->subDays(rand(0, 365)),
                'adresse' => 'Dakar, Quartier ' . rand(1, 20),
                'email' => $user->email,
                'telephone' => '77' . rand(100, 999) . rand(10, 99) . rand(10, 99),
                'groupe_sanguin' => $groupesSanguins[array_rand($groupesSanguins)],
                'antecedents' => rand(0, 1) ? 'Hypertension, Diabète' : null
            ]);
        }
    }
    
    private function createMedecinInfirmierRelations()
    {
        $this->command->info('🔗 Création des relations médecin-infirmier...');
        
        $medecins = User::where('role', 'medecin')->get();
        $infirmiers = User::where('role', 'infirmier')->get();
        
        foreach ($medecins as $medecin) {
            // Affecter 1-3 infirmiers à chaque médecin
            $nbInfirmiers = rand(1, 3);
            $infimiersAleatoires = $infirmiers->random($nbInfirmiers);
            
            foreach ($infimiersAleatoires as $infirmier) {
                $medecin->nurses()->syncWithoutDetaching([$infirmier->id]);
            }
        }
    }
    
    private function createRendezVous()
    {
        $this->command->info('📅 Création des rendez-vous...');
        
        $patients = User::where('role', 'patient')->get();
        $medecins = User::where('role', 'medecin')->get();
        $statuts = ['en_attente', 'confirmé', 'annulé', 'terminé'];
        $motifs = [
            'Consultation générale', 'Contrôle médical', 'Suivi traitement', 
            'Douleurs abdominales', 'Maux de tête', 'Vaccination',
            'Bilan de santé', 'Renouvellement ordonnance'
        ];
        
        // Créer des RDV sur les 12 derniers mois pour les stats
        $startDate = Carbon::now()->subMonths(12);
        $endDate = Carbon::now()->addMonths(2);
        
        for ($i = 0; $i < 300; $i++) {
            $date = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            // Plus de RDV récents
            if ($date->isAfter(Carbon::now()->subMonths(3))) {
                $weight = 3; // 3x plus de chance d'être sélectionné
            } else {
                $weight = 1;
            }
            
            if (rand(1, 4) <= $weight) {
                Rendez_vous::create([
                    'user_id' => $patients->random()->id,
                    'medecin_id' => $medecins->random()->id,
                    'date' => $date->format('Y-m-d'),
                    'heure' => sprintf('%02d:%02d', rand(8, 17), rand(0, 3) * 15),
                    'motif' => $motifs[array_rand($motifs)],
                    'statut' => $statuts[array_rand($statuts)]
                ]);
            }
        }
    }
    
    private function createConsultations()
    {
        $this->command->info('🩺 Création des consultations...');
        
        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        $diagnostics = [
            'Hypertension artérielle', 'Diabetes type 2', 'Grippe saisonnière',
            'Gastrite', 'Migraine', 'Asthme', 'Arthrite', 'Bronchite',
            'Sinusite', 'Eczéma', 'Anxiété', 'Lombalgie'
        ];
        $traitements = [
            'Repos et hydratation', 'Antibiotiques 7 jours', 'Anti-inflammatoires',
            'Régime alimentaire adapté', 'Kinésithérapie', 'Suivi spécialisé',
            'Médicaments à vie', 'Contrôle dans 1 mois'
        ];
        
        // Consultations sur les 12 derniers mois
        for ($i = 0; $i < 200; $i++) {
            $date = Carbon::now()->subDays(rand(0, 365));
            
            Consultations::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'date_consultation' => $date,
                'symptomes' => 'Symptômes divers selon diagnostic',
                'diagnostic' => $diagnostics[array_rand($diagnostics)],
                'traitement' => $traitements[array_rand($traitements)],
                'statut' => ['En attente', 'En cours', 'Terminée'][rand(0, 2)]
            ]);
        }
    }
    
    private function createAdmissions()
    {
        $this->command->info('🏥 Création des admissions...');
        
        $patients = Patient::all();
        $services = ['Cardiologie', 'Urgences', 'Chirurgie', 'Pédiatrie', 'Maternité', 'Médecine Interne'];
        $motifs = [
            'Intervention chirurgicale', 'Surveillance post-opératoire', 'Traitement intensif',
            'Accouchement', 'Crise cardiaque', 'Pneumonie sévère', 'Fracture complexe'
        ];
        
        // Admissions sur les 12 derniers mois
        for ($i = 0; $i < 80; $i++) {
            $dateAdmission = Carbon::now()->subDays(rand(0, 365));
            $dateSortie = rand(0, 1) ? $dateAdmission->copy()->addDays(rand(1, 15)) : null;
            
            Admissions::create([
                'patient_id' => $patients->random()->id,
                'date_admission' => $dateAdmission,
                'date_sortie' => $dateSortie,
                'motif_admission' => $motifs[array_rand($motifs)],
                'service' => $services[array_rand($services)],
                'observations' => 'Patient admis pour surveillance et traitement.'
            ]);
        }
    }
    
    private function createOrdonnancesAndAnalyses()
    {
        $this->command->info('💊 Création des ordonnances et analyses...');
        
        $patients = Patient::all();
        $medecins = User::where('role', 'medecin')->get();
        
        $medicaments = [
            'Paracétamol 500mg', 'Ibuprofène 400mg', 'Amoxicilline 1g',
            'Oméprazole 20mg', 'Metformine 850mg', 'Lisinopril 10mg',
            'Atorvastatine 20mg', 'Aspirine 100mg', 'Salbutamol inhaleur'
        ];
        
        $analysesTypes = [
            'Bilan sanguin complet', 'Glycémie à jeun', 'Cholestérol total',
            'Créatinine', 'TSH', 'NFS plaquettes', 'Bilan hépatique',
            'Électrocardiogramme', 'Radiographie thorax'
        ];
        
        // Ordonnances
        for ($i = 0; $i < 150; $i++) {
            Ordonnances::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'date_ordonnance' => Carbon::now()->subDays(rand(0, 365)),
                'medicaments' => $medicaments[array_rand($medicaments)] . ', ' . $medicaments[array_rand($medicaments)],
                'dosage' => '2 fois par jour pendant 7 jours'
            ]);
        }
        
        // Analyses
        for ($i = 0; $i < 100; $i++) {
            Analyses::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'type_analyse' => $analysesTypes[array_rand($analysesTypes)],
                'date_analyse' => Carbon::now()->subDays(rand(0, 365)),
                'resultats' => 'Résultats dans les normes',
                'etat' => rand(0, 1) ? 'termine' : 'en_cours'
            ]);
        }
    }
    
    private function createOrders()
    {
        $this->command->info('💳 Création des commandes de paiement...');
        
        $users = User::where('role', 'patient')->get();
        $patients = Patient::all();
        $providers = ['wave', 'orange_money', 'cash'];
        $statuses = ['paid', 'pending', 'cancelled'];
        $services = [
            'Consultation générale' => 15000,
            'Consultation spécialisée' => 25000,
            'Analyses médicales' => 35000,
            'Vaccination' => 10000,
            'Échographie' => 20000,
            'Radiographie' => 18000,
            'Bilan sanguin' => 30000
        ];
        
        // Commandes sur les 12 derniers mois
        for ($i = 0; $i < 120; $i++) {
            $date = Carbon::now()->subDays(rand(0, 365));
            $user = $users->random();
            $patient = $patients->where('user_id', $user->id)->first();
            $provider = $providers[array_rand($providers)];
            $status = $statuses[array_rand($statuses)];
            
            // Plus de paiements réussis
            if (rand(1, 10) <= 7) {
                $status = 'paid';
            }
            
            $serviceLabel = array_rand($services);
            $amount = $services[$serviceLabel];
            
            $order = Order::create([
                'user_id' => $user->id,
                'patient_id' => $patient ? $patient->id : null,
                'currency' => 'XOF',
                'total_amount' => $amount,
                'status' => $status,
                'provider' => $provider,
                'provider_ref' => 'TXN' . rand(100000, 999999),
                'paid_at' => $status === 'paid' ? $date : null,
                'created_at' => $date,
                'updated_at' => $date
            ]);
            
            // Créer l'item de commande
            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => 'service_medical',
                'item_id' => rand(1, 100),
                'label' => $serviceLabel,
                'amount' => $amount
            ]);
        }
    }
}

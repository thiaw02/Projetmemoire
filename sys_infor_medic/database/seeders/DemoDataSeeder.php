<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Ordonnances;
use App\Models\Analyses;
use App\Models\Admissions;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Secrétaire
        $secretaire = User::firstOrCreate(
            ['email' => 'secretaire@example.com'],
            ['name' => 'Secrétaire', 'password' => Hash::make('password'), 'role' => 'secretaire']
        );

        // Médecins
        $med1 = User::firstOrCreate(
            ['email' => 'medecin1@example.com'],
            ['name' => 'Dr. Diop', 'password' => Hash::make('password'), 'role' => 'medecin', 'specialite' => 'Généraliste']
        );
        $med2 = User::firstOrCreate(
            ['email' => 'medecin2@example.com'],
            ['name' => 'Dr. Sow', 'password' => Hash::make('password'), 'role' => 'medecin', 'specialite' => 'Cardiologue']
        );

        // Infirmier
        $inf = User::firstOrCreate(
            ['email' => 'infirmier@example.com'],
            ['name' => 'Infirmier', 'password' => Hash::make('password'), 'role' => 'infirmier']
        );

        // Patients (users + patients)
        $patUser1 = User::firstOrCreate(
            ['email' => 'patient1@example.com'],
            ['name' => 'Patient 1', 'password' => Hash::make('password'), 'role' => 'patient']
        );
        $pat1 = Patient::firstOrCreate(
            ['user_id' => $patUser1->id],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Awa',
                'email' => $patUser1->email,
                'sexe' => 'Féminin',
                'date_naissance' => '1995-06-12',
                'adresse' => 'Dakar',
                'telephone' => '771234567',
                'groupe_sanguin' => 'O+',
                'secretary_user_id' => $secretaire->id,
            ]
        );

        $patUser2 = User::firstOrCreate(
            ['email' => 'patient2@example.com'],
            ['name' => 'Patient 2', 'password' => Hash::make('password'), 'role' => 'patient']
        );
        $pat2 = Patient::firstOrCreate(
            ['user_id' => $patUser2->id],
            [
                'nom' => 'Diop',
                'prenom' => 'Moussa',
                'email' => $patUser2->email,
                'sexe' => 'Masculin',
                'date_naissance' => '1990-01-20',
                'adresse' => 'Thiès',
                'telephone' => '781112233',
                'groupe_sanguin' => 'A-',
                'secretary_user_id' => $secretaire->id,
            ]
        );

        // Rendez-vous (user_id = users.id du patient)
        Rendez_vous::firstOrCreate([
            'user_id' => $patUser1->id,
            'medecin_id' => $med1->id,
            'date' => Carbon::now()->addDays(3)->toDateString(),
            'heure' => '10:30',
        ], [
            'motif' => 'Consultation générale',
            'statut' => 'en_attente',
        ]);
        Rendez_vous::firstOrCreate([
            'user_id' => $patUser1->id,
            'medecin_id' => $med2->id,
            'date' => Carbon::now()->subDays(7)->toDateString(),
            'heure' => '15:00',
        ], [
            'motif' => 'Douleur thoracique',
            'statut' => 'confirmé',
        ]);

        // Consultations
        $c1 = Consultations::firstOrCreate([
            'patient_id' => $pat1->id,
            'medecin_id' => $med1->id,
            'date_consultation' => Carbon::now()->subDays(10)->toDateString(),
        ], [
            'symptomes' => 'Fièvre',
            'diagnostic' => 'Paludisme',
            'traitement' => 'ACT',
            'statut' => 'Terminée',
        ]);

        // Ordonnances liées
        Ordonnances::firstOrCreate([
            'patient_id' => $pat1->id,
            'medecin_id' => $med1->id,
            'date_ordonnance' => Carbon::now()->subDays(9)->toDateString(),
        ], [
            'contenu' => 'Paracétamol 500mg, 3x/jour'
        ]);

        // Analyses
        Analyses::firstOrCreate([
            'patient_id' => $pat1->id,
            'medecin_id' => $med2->id,
            'date_analyse' => Carbon::now()->subDays(8)->toDateString(),
        ], [
            'type_analyse' => 'Bilan sanguin',
            'resultats' => 'Hémoglobine normale'
        ]);

        // Admissions
        Admissions::firstOrCreate([
            'patient_id' => $pat1->id,
            'date_admission' => Carbon::now()->subDays(20)->toDateString(),
        ], [
            'motif_admission' => 'Observation',
            'service' => 'Médecine',
            'observations' => 'RAS'
        ]);
    }
}

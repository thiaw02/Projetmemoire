<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier qu'il y a des patients et professionnels
        $patientsCount = User::where('role', 'patient')->count();
        $professionalsCount = User::whereIn('role', ['medecin', 'infirmier'])->count();
        
        if ($patientsCount === 0 || $professionalsCount === 0) {
            $this->command->info('Aucun patient ou professionnel trouvé. Veuillez d\'abord créer des utilisateurs.');
            return;
        }
        
        $this->command->info('Création des évaluations...');
        
        // Créer 50 évaluations normales
        Evaluation::factory(40)->create();
        
        // Créer 8 évaluations excellentes
        Evaluation::factory(8)->excellent()->create();
        
        // Créer 2 évaluations faibles
        Evaluation::factory(2)->poor()->create();
        
        $this->command->info('50 évaluations créées avec succès !');
        
        // Afficher quelques statistiques
        $totalEvaluations = Evaluation::count();
        $averageRating = Evaluation::avg('note');
        $evaluationsByType = [
            'medecin' => Evaluation::where('type_evaluation', 'medecin')->count(),
            'infirmier' => Evaluation::where('type_evaluation', 'infirmier')->count(),
        ];
        
        $this->command->info("Statistiques:");
        $this->command->info("- Total évaluations: {$totalEvaluations}");
        $this->command->info("- Note moyenne: " . round($averageRating, 2));
        $this->command->info("- Évaluations médecins: {$evaluationsByType['medecin']}");
        $this->command->info("- Évaluations infirmiers: {$evaluationsByType['infirmier']}");
    }
}

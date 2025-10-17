<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Consultations;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Récupérer des patients et professionnels existants
        $patients = User::where('role', 'patient')->pluck('id')->toArray();
        $professionals = User::whereIn('role', ['medecin', 'infirmier'])->pluck('id')->toArray();
        $professionalUser = User::whereIn('role', ['medecin', 'infirmier'])->inRandomOrder()->first();
        
        $comments = [
            'Très bon professionnel, à l\'écoute et compétent.',
            'Excellent service, je recommande vivement.',
            'Professionnel très attentionné et disponible.',
            'Bonne prise en charge, merci beaucoup.',
            'Service correct, rien à redire.',
            'Très satisfait de la consultation.',
            'Professionnel compétent et rassurant.',
            'Excellente écoute et bons conseils.',
            'Très bon suivi médical.',
            'Je recommande sans hésitation.',
            null, // Parfois pas de commentaire
        ];
        
        return [
            'patient_id' => $this->faker->randomElement($patients),
            'evaluated_user_id' => $professionalUser ? $professionalUser->id : $this->faker->randomElement($professionals),
            'type_evaluation' => $professionalUser ? $professionalUser->role : $this->faker->randomElement(['medecin', 'infirmier']),
            'note' => $this->faker->numberBetween(1, 5),
            'commentaire' => $this->faker->randomElement($comments),
            'consultation_id' => $this->faker->boolean(30) ? Consultations::inRandomOrder()->first()?->id : null,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
    
    /**
     * Indicate that the evaluation has an excellent rating.
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => 5,
            'commentaire' => $this->faker->randomElement([
                'Excellent professionnel ! Je le recommande vivement.',
                'Service exceptionnel, très professionnel.',
                'Parfait en tous points, merci beaucoup.',
                'Un professionnel remarquable, très satisfait.',
            ])
        ]);
    }
    
    /**
     * Indicate that the evaluation has a poor rating.
     */
    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => $this->faker->numberBetween(1, 2),
            'commentaire' => $this->faker->randomElement([
                'Service décevant, je ne recommande pas.',
                'Pas très satisfait de la prestation.',
                'Peut mieux faire au niveau de l\'écoute.',
                'Expérience mitigée, des améliorations à apporter.',
            ])
        ]);
    }
}

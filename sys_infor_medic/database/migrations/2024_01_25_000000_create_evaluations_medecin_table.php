<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations_medecin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('consultation_id');
            
            // Notes sur 5 pour différents aspects
            $table->decimal('note_competence', 2, 1)->comment('Note de 1 à 5 pour la compétence médicale');
            $table->decimal('note_communication', 2, 1)->comment('Note de 1 à 5 pour la communication');
            $table->decimal('note_ponctualite', 2, 1)->comment('Note de 1 à 5 pour la ponctualité');
            $table->decimal('note_ecoute', 2, 1)->comment('Note de 1 à 5 pour la qualité d\'écoute');
            $table->decimal('note_disponibilite', 2, 1)->comment('Note de 1 à 5 pour la disponibilité');
            
            // Note globale calculée automatiquement
            $table->decimal('note_globale', 2, 1)->comment('Moyenne des notes sur 5');
            
            // Commentaires du patient
            $table->text('commentaire_positif')->nullable();
            $table->text('commentaire_amelioration')->nullable();
            $table->text('commentaire_general')->nullable();
            
            // Recommandations
            $table->boolean('recommande_medecin')->default(true)->comment('Le patient recommande-t-il ce médecin ?');
            $table->enum('niveau_satisfaction', ['très_insatisfait', 'insatisfait', 'neutre', 'satisfait', 'très_satisfait'])->default('satisfait');
            
            // Statut de l'évaluation
            $table->enum('statut', ['en_attente', 'soumise', 'validee', 'archivee'])->default('soumise');
            $table->boolean('visible_publiquement')->default(true);
            
            // Métadonnées
            $table->timestamp('date_evaluation')->useCurrent();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            
            $table->timestamps();
            
            // Index et contraintes
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            
            // Un patient ne peut évaluer qu'une seule fois une consultation
            $table->unique(['patient_id', 'consultation_id'], 'unique_evaluation_consultation');
            
            // Index pour les recherches
            $table->index(['medecin_id', 'statut']);
            $table->index(['note_globale']);
            $table->index(['date_evaluation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations_medecin');
    }
};
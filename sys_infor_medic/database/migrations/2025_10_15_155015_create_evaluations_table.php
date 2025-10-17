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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluated_user_id')->constrained('users')->onDelete('cascade'); // médecin ou infirmier évalué
            $table->enum('type_evaluation', ['medecin', 'infirmier']);
            $table->integer('note')->unsigned()->between(1, 5); // Note de 1 à 5 étoiles
            $table->text('commentaire')->nullable();
            $table->foreignId('consultation_id')->nullable()->constrained()->onDelete('set null'); // Lié à une consultation si applicable
            $table->timestamps();
            
            // Un patient ne peut évaluer qu'une seule fois le même professionnel pour la même consultation
            $table->unique(['patient_id', 'evaluated_user_id', 'consultation_id']);
            
            // Index pour les recherches
            $table->index(['evaluated_user_id', 'type_evaluation']);
            $table->index(['patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

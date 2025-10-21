<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id')->nullable();
            // Schéma harmonisé + rétro-compatibilité
            $table->string('type_analyse')->nullable();  // nouveau
            $table->string('resultats')->nullable();      // nouveau
            $table->date('date_analyse')->nullable();     // nouveau
            $table->string('etat')->nullable();           // nouveau
            // Anciennes colonnes (compatibilité)
            $table->string('type')->nullable();
            $table->string('resultat')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
            $table->foreign('medecin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};

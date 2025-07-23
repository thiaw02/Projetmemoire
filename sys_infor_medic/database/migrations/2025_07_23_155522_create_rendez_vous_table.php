<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();

            // Patient (utilisateur qui prend rendez-vous)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Médecin (aussi dans users)
            $table->foreignId('medecin_id')->constrained('users')->onDelete('cascade');

            $table->date('date');
            $table->time('heure');
            $table->string('motif')->nullable();
            $table->enum('statut', ['en_attente', 'confirmé', 'annulé', 'terminé'])->default('en_attente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};

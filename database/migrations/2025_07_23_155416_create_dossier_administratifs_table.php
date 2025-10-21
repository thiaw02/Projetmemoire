<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossier_administratifs', function (Blueprint $table) {
            $table->id();

            // Lien vers patient (si nécessaire)
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // Exemple de champs administratifs
            $table->string('numero_dossier')->unique()->nullable();
            $table->date('date_ouverture')->nullable();
            $table->string('statut')->nullable(); // ex: actif, clos, en attente
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_administratifs');
    }
};

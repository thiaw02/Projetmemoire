<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acte_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('users')->onDelete('cascade');
            $table->string('type_acte'); // exemple : "consultation", "chirurgie", etc.
            $table->text('description')->nullable();
            $table->date('date_acte');
            $table->decimal('cout', 8, 2)->nullable(); // si applicable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acte_medicaux');
    }
};

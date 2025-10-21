<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();

            // Lien vers patient
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            $table->date('date_admission');
            $table->date('date_sortie')->nullable();
            $table->string('motif_admission')->nullable();
            $table->string('service')->nullable(); // ex: cardiologie, chirurgie, etc.
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};

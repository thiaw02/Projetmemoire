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
        Schema::table('evaluations_medecin', function (Blueprint $table) {
            $table->text('reponse_medecin')->nullable()->after('commentaire_general');
            $table->timestamp('date_reponse')->nullable()->after('reponse_medecin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations_medecin', function (Blueprint $table) {
            $table->dropColumn(['reponse_medecin', 'date_reponse']);
        });
    }
};
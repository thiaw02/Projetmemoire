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
        Schema::table('evaluations', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà avant de les ajouter
            if (!Schema::hasColumn('evaluations', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('evaluations', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
        
        // Mettre à jour les enregistrements existants qui ont NULL
        DB::table('evaluations')->whereNull('created_at')->update([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire car nous ne voulons pas supprimer les timestamps
    }
};




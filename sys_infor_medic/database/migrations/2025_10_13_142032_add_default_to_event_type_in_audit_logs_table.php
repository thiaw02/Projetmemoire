<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Modifier la colonne event_type pour avoir une valeur par défaut
            $table->string('event_type')->default('update')->change();
            
            // S'assurer que severity a aussi une valeur par défaut
            $table->string('severity')->default('low')->change();
        });
        
        // Mettre à jour les enregistrements existants qui n'ont pas d'event_type
        DB::table('audit_logs')
            ->whereNull('event_type')
            ->orWhere('event_type', '')
            ->update([
                'event_type' => 'update',
                'severity' => 'low'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Retirer les valeurs par défaut
            $table->string('event_type')->default(null)->change();
            $table->string('severity')->default(null)->change();
        });
    }
};

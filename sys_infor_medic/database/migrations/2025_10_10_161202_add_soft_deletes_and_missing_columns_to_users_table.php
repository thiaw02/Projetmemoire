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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter la colonne pour les soft deletes
            $table->softDeletes();
            
            // Ajouter les colonnes manquantes définies dans le modèle User
            if (!Schema::hasColumn('users', 'pro_phone')) {
                $table->string('pro_phone')->nullable()->after('specialite');
            }
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->after(Schema::hasColumn('users', 'pro_phone') ? 'pro_phone' : 'specialite');
            }
            if (!Schema::hasColumn('users', 'cabinet')) {
                $table->string('cabinet')->nullable()->after(Schema::hasColumn('users', 'matricule') ? 'matricule' : (Schema::hasColumn('users', 'pro_phone') ? 'pro_phone' : 'specialite'));
            }
            if (!Schema::hasColumn('users', 'horaires')) {
                $table->text('horaires')->nullable()->after(Schema::hasColumn('users', 'cabinet') ? 'cabinet' : (Schema::hasColumn('users', 'matricule') ? 'matricule' : (Schema::hasColumn('users', 'pro_phone') ? 'pro_phone' : 'specialite')));
            }
            // avatar_url existe déjà, on la laisse
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées (sauf avatar_url qui existait déjà)
            $table->dropSoftDeletes();
            
            $columnsToRemove = [];
            if (Schema::hasColumn('users', 'pro_phone')) {
                $columnsToRemove[] = 'pro_phone';
            }
            if (Schema::hasColumn('users', 'matricule')) {
                $columnsToRemove[] = 'matricule';
            }
            if (Schema::hasColumn('users', 'cabinet')) {
                $columnsToRemove[] = 'cabinet';
            }
            if (Schema::hasColumn('users', 'horaires')) {
                $columnsToRemove[] = 'horaires';
            }
            if (Schema::hasColumn('users', 'active')) {
                $columnsToRemove[] = 'active';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};

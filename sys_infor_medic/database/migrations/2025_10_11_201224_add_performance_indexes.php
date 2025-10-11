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
        // Index pour les utilisateurs
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'active']); // Filtrage par rôle et statut
            $table->index('email'); // Recherche par email
        });

        // Index pour les patients
        Schema::table('patients', function (Blueprint $table) {
            $table->index('user_id'); // Relation avec users
            $table->index('numero_dossier'); // Recherche par numéro
            $table->index('created_at'); // Tri par date de création
        });

        // Index pour les rendez-vous
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->index(['user_id', 'date']); // Patient + date
            $table->index(['medecin_id', 'date']); // Médecin + date
            $table->index(['statut', 'date']); // Statut + date
            $table->index(['date', 'heure']); // Tri par datetime
        });

        // Index pour les consultations
        Schema::table('consultations', function (Blueprint $table) {
            $table->index(['patient_id', 'date_consultation']); // Patient + date
            $table->index(['medecin_id', 'date_consultation']); // Médecin + date
            $table->index('date_consultation'); // Tri par date
        });

        // Index pour les ordonnances
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->index(['patient_id', 'created_at']); // Patient + date
            $table->index(['medecin_id', 'created_at']); // Médecin + date
        });

        // Index pour les analyses
        Schema::table('analyses', function (Blueprint $table) {
            $table->index(['patient_id', 'date_analyse']); // Patient + date
            $table->index('date_analyse'); // Tri par date
        });

        // Index pour les logs d'audit
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']); // Utilisateur + date
            $table->index(['event_type', 'severity']); // Type + sévérité
            $table->index('created_at'); // Tri par date
            $table->index('expires_at'); // Nettoyage automatique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les index
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'active']);
            $table->dropIndex(['email']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['numero_dossier']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
            $table->dropIndex(['medecin_id', 'date']);
            $table->dropIndex(['statut', 'date']);
            $table->dropIndex(['date', 'heure']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'date_consultation']);
            $table->dropIndex(['medecin_id', 'date_consultation']);
            $table->dropIndex(['date_consultation']);
        });

        Schema::table('ordonnances', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'created_at']);
            $table->dropIndex(['medecin_id', 'created_at']);
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'date_analyse']);
            $table->dropIndex(['date_analyse']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['event_type', 'severity']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['expires_at']);
        });
    }
};

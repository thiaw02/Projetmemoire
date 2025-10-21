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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Ajouter de nouvelles colonnes pour optimiser les audits
            $table->string('event_type', 50)->after('action'); // create, update, delete, login, etc.
            $table->string('severity', 20)->default('low')->after('event_type'); // low, medium, high, critical
            $table->ipAddress('ip_address')->nullable()->after('user_id');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->json('metadata')->nullable()->after('changes'); // informations contextuelles
            $table->timestamp('expires_at')->nullable()->after('updated_at');
            
            // Optimiser les index existants et en ajouter de nouveaux
            $table->dropIndex(['auditable_type','auditable_id']);
            
            // Index composites optimisés pour les requêtes courantes
            $table->index(['user_id', 'created_at'], 'audit_user_date_idx');
            $table->index(['event_type', 'severity', 'created_at'], 'audit_event_severity_date_idx');
            $table->index(['auditable_type', 'auditable_id', 'created_at'], 'audit_entity_date_idx');
            $table->index(['created_at', 'severity'], 'audit_date_severity_idx');
            $table->index(['expires_at'], 'audit_expires_idx');
            $table->index(['ip_address', 'created_at'], 'audit_ip_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Supprimer les nouveaux index
            $table->dropIndex('audit_user_date_idx');
            $table->dropIndex('audit_event_severity_date_idx');
            $table->dropIndex('audit_entity_date_idx');
            $table->dropIndex('audit_date_severity_idx');
            $table->dropIndex('audit_expires_idx');
            $table->dropIndex('audit_ip_date_idx');
            
            // Restaurer l'ancien index
            $table->index(['auditable_type','auditable_id']);
            
            // Supprimer les nouvelles colonnes
            $table->dropColumn([
                'event_type',
                'severity',
                'ip_address',
                'user_agent',
                'metadata',
                'expires_at'
            ]);
        });
    }
};

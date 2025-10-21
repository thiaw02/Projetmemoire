<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pro_phone')) {
                $table->string('pro_phone')->nullable()->after('specialite');
            }
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->after('pro_phone');
            }
            if (!Schema::hasColumn('users', 'cabinet')) {
                $table->string('cabinet')->nullable()->after('matricule');
            }
            if (!Schema::hasColumn('users', 'horaires')) {
                $table->text('horaires')->nullable()->after('cabinet');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'horaires')) $table->dropColumn('horaires');
            if (Schema::hasColumn('users', 'cabinet')) $table->dropColumn('cabinet');
            if (Schema::hasColumn('users', 'matricule')) $table->dropColumn('matricule');
            if (Schema::hasColumn('users', 'pro_phone')) $table->dropColumn('pro_phone');
        });
    }
};

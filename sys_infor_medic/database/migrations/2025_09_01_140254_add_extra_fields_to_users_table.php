<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone')->nullable()->after('email');
            $table->string('adresse')->nullable()->after('telephone');
            $table->date('date_naissance')->nullable()->after('adresse');
            $table->enum('sexe', ['Homme', 'Femme'])->nullable()->after('date_naissance');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'adresse', 'date_naissance', 'sexe']);
        });
    }
};

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
            // Informations personnelles
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['Masculin', 'Féminin', 'Autre'])->nullable()->after('date_of_birth');
            $table->string('emergency_contact', 255)->nullable()->after('gender');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
            
            // Informations professionnelles
            $table->string('department', 255)->nullable()->after('emergency_phone');
            $table->date('hire_date')->nullable()->after('department');
            $table->decimal('salary', 10, 2)->nullable()->after('hire_date');
            $table->text('notes')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address', 
                'date_of_birth',
                'gender',
                'emergency_contact',
                'emergency_phone',
                'department',
                'hire_date',
                'salary',
                'notes'
            ]);
        });
    }
};


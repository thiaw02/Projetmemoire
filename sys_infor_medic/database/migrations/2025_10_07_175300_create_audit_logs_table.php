<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // ex: consultation_updated, ordonnance_updated
            $table->string('auditable_type'); // App\\Models\\Consultations, App\\Models\\Ordonnances
            $table->unsignedBigInteger('auditable_id');
            $table->json('changes')->nullable(); // {"before":{...},"after":{...}}
            $table->timestamps();
            $table->index(['auditable_type','auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medecin_infirmier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('infirmier_id');
            $table->timestamps();

            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('infirmier_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['medecin_id','infirmier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medecin_infirmier');
    }
};

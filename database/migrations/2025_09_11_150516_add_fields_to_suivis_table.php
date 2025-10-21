<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration
{
    public function up()
    {
        Schema::table('suivis', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->after('id');
            $table->integer('temperature')->nullable()->after('patient_id');
            $table->string('tension')->nullable()->after('temperature');

            // Optionnel : clé étrangère
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('suivis', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['patient_id', 'temperature', 'tension']);
        });
    }
};

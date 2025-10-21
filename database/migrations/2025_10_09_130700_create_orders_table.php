<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('currency', 8)->default('XOF');
            $table->bigInteger('total_amount');
            $table->string('status', 20)->default('pending');
            $table->string('provider', 40)->nullable();
            $table->string('provider_ref', 191)->nullable();
            $table->string('payment_url', 191)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['patient_id']);
            $table->index(['status']);
            $table->index(['provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

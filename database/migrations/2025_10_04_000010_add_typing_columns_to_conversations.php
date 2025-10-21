<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'typing_user_one_at')) {
                $table->timestamp('typing_user_one_at')->nullable()->after('user_two_id');
            }
            if (!Schema::hasColumn('conversations', 'typing_user_two_at')) {
                $table->timestamp('typing_user_two_at')->nullable()->after('typing_user_one_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'typing_user_two_at')) $table->dropColumn('typing_user_two_at');
            if (Schema::hasColumn('conversations', 'typing_user_one_at')) $table->dropColumn('typing_user_one_at');
        });
    }
};

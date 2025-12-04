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
            $table->string('api_key', 64)->unique()->nullable()->after('user_tier');
            $table->string('app_key', 64)->unique()->nullable()->after('api_key');
            $table->index('api_key');
            $table->index('app_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['api_key']);
            $table->dropIndex(['app_key']);
            $table->dropColumn(['api_key', 'app_key']);
        });
    }
};

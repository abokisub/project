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
            $table->string('username')->unique()->nullable()->after('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('username');
            $table->string('state')->nullable()->after('gender');
            $table->string('city')->nullable()->after('state');
            $table->text('street_address')->nullable()->after('city');
            
            // Add index for username for faster lookups
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropColumn(['username', 'gender', 'state', 'city', 'street_address']);
        });
    }
};


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
            // Remove old boolean role fields
            $table->dropColumn(['is_admin', 'is_agent', 'is_merchant']);
            
            // Add user_tier field (tier1, tier2, tier3, tier4, tier5)
            // Note: Actual role assignment will be handled by Spatie Permission package
            // This field is for quick reference and fee calculation
            $table->string('user_tier')->default('tier1')->after('kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_agent')->default(false);
            $table->boolean('is_merchant')->default(false);
            $table->dropColumn('user_tier');
        });
    }
};

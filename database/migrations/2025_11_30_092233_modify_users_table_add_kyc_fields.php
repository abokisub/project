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
        // This migration is now redundant as we've added these fields directly to the users table
        // Keeping it empty to avoid errors
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

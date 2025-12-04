<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            
            // Migrate existing data (split name into first_name and last_name)
            // This will be handled in the model or a separate data migration
        });
        
        // Migrate existing name data to first_name and last_name
        DB::statement("UPDATE users SET first_name = SUBSTRING_INDEX(name, ' ', 1), last_name = SUBSTRING_INDEX(name, ' ', -1) WHERE name IS NOT NULL AND name != ''");
        
        Schema::table('users', function (Blueprint $table) {
            // Make first_name and last_name required after migration
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            
            // Drop old name column
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add name column back
            $table->string('name')->after('id');
        });
        
        // Migrate data back
        DB::statement("UPDATE users SET name = CONCAT(first_name, ' ', last_name) WHERE first_name IS NOT NULL AND last_name IS NOT NULL");
        
        Schema::table('users', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};

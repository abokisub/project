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
        Schema::create('fee_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained()->onDelete('cascade');
            $table->decimal('old_fee_percentage', 5, 2)->nullable();
            $table->decimal('new_fee_percentage', 5, 2)->nullable();
            $table->decimal('old_fee_fixed', 15, 2)->nullable();
            $table->decimal('new_fee_fixed', 15, 2)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index('fee_id');
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_history');
    }
};

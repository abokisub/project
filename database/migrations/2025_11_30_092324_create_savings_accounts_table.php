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
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('type', ['regular', 'target', 'round_up', 'auto_save'])->default('regular');
            $table->enum('status', ['active', 'locked', 'completed', 'closed'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};

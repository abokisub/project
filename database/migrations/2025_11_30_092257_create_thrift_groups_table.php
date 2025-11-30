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
        Schema::create('thrift_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('contribution_amount', 15, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->json('rotation_order')->nullable(); // Array of user IDs in payout order
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('total_members')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('organizer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thrift_groups');
    }
};

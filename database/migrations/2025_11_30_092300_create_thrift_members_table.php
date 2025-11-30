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
        Schema::create('thrift_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thrift_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('position'); // Position in rotation order
            $table->decimal('total_contributed', 15, 2)->default(0);
            $table->date('next_payout_date')->nullable();
            $table->boolean('has_received_payout')->default(false);
            $table->enum('status', ['active', 'inactive', 'removed'])->default('active');
            $table->timestamps();
            
            $table->unique(['thrift_group_id', 'user_id']);
            $table->index('thrift_group_id');
            $table->index('user_id');
            $table->index('next_payout_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thrift_members');
    }
};

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
        Schema::create('thrift_auto_debits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thrift_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('next_debit_date');
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->timestamps();
            
            $table->unique(['thrift_group_id', 'user_id']);
            $table->index('next_debit_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thrift_auto_debits');
    }
};

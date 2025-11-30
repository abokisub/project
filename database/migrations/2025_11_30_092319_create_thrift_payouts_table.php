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
        Schema::create('thrift_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thrift_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('payout_date');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('thrift_group_id');
            $table->index('recipient_id');
            $table->index('payout_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thrift_payouts');
    }
};

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_from_id')->nullable()->constrained('wallets')->onDelete('set null');
            $table->foreignId('wallet_to_id')->nullable()->constrained('wallets')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('type'); // transfer, deposit, withdrawal, thrift, savings, payment, etc.
            $table->enum('status', ['pending', 'processing', 'settled', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->json('meta')->nullable();
            $table->boolean('offline_flag')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->index('wallet_from_id');
            $table->index('wallet_to_id');
            $table->index('reference');
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

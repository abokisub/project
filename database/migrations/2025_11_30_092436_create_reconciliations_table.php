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
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('external_transaction_id')->nullable();
            $table->foreignId('internal_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->enum('status', ['pending', 'matched', 'mismatched', 'resolved'])->default('pending');
            $table->decimal('diff', 15, 2)->nullable();
            $table->json('external_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('external_transaction_id');
            $table->index('internal_transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};

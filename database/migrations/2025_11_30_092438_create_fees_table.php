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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // transfer, withdrawal, thrift, etc.
            $table->string('user_tier')->default('standard'); // standard, premium, vip
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->decimal('fee_fixed', 15, 2)->default(0);
            $table->decimal('min_fee', 15, 2)->default(0);
            $table->decimal('max_fee', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['transaction_type', 'user_tier']);
            $table->index('transaction_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};

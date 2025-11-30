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
        Schema::create('offline_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('amount', 15, 2);
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at');
            $table->boolean('redeemed')->default(false);
            $table->string('signature');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('expires_at');
            $table->index('redeemed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_vouchers');
    }
};

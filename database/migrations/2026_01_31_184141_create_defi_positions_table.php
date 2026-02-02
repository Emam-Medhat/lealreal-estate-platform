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
        if (!Schema::hasTable('defi_positions')) {
        Schema::create('defi_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pool_id')->constrained('defi_pools')->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('shares', 20, 8);
            $table->decimal('earned_rewards', 20, 8)->default(0);
            $table->enum('status', ['active', 'withdrawn', 'pending'])->default('active');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['pool_id', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('defi_positions');
    }
};

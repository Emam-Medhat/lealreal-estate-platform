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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('points_required')->default(0);
            $table->string('reward_type'); // discount, voucher, item, service
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('expires_at')->nullable();
            $table->string('category')->default('general');
            $table->string('icon')->nullable();
            $table->json('terms')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'expires_at']);
            $table->index('category');
            $table->index('reward_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};

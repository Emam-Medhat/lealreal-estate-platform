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
        Schema::create('user_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('total_users')->default(0);
            $table->integer('active_users')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('returning_users')->default(0);
            $table->integer('login_count')->default(0);
            $table->decimal('avg_session_duration', 8, 2)->default(0);
            $table->integer('page_views')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('revenue', 15, 2)->default(0);
            $table->integer('properties_viewed')->default(0);
            $table->integer('properties_saved')->default(0);
            $table->integer('leads_generated')->default(0);
            $table->integer('transactions_completed')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index(['date']);
            $table->index(['user_id', 'date']);
            $table->index(['active_users']);
            $table->index(['last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_analytics');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('promoted_listings')) {
        Schema::create('promoted_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('advertisement_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('promotion_type', ['featured', 'premium', 'spotlight']);
            $table->integer('duration');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->decimal('daily_budget', 10, 2);
            $table->decimal('total_budget', 12, 2);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('remaining_budget', 12, 2);
            $table->enum('status', ['active', 'paused', 'expired', 'cancelled'])->default('active');
            $table->text('promotion_text')->nullable();
            $table->json('highlight_features')->nullable();
            $table->string('call_to_action')->nullable();
            $table->integer('priority_level')->default(5);
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('conversions_count')->default(0);
            $table->datetime('featured_until')->nullable();
            $table->datetime('spotlight_until')->nullable();
            $table->datetime('premium_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['promotion_type', 'status']);
            $table->index(['priority_level']);
            $table->index(['start_date', 'end_date']);
            $table->index(['created_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('promoted_listings');
    }
};

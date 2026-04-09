<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('negotiations')) {
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('negotiation_number')->unique();
            $table->enum('status', ['active', 'paused', 'completed', 'failed', 'cancelled'])->default('active');
            $table->enum('type', ['price', 'terms', 'contingencies', 'closing', 'general'])->default('price');
            $table->text('subject')->nullable();
            $table->decimal('initial_price', 15, 2)->nullable();
            $table->decimal('current_price', 15, 2)->nullable();
            $table->decimal('target_price', 15, 2)->nullable();
            $table->json('negotiation_terms')->nullable();
            $table->json('agreement_points')->nullable();
            $table->json('disputed_points')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('initiated_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_mediated')->default(false);
            $table->foreignId('mediator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['last_activity_at', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};

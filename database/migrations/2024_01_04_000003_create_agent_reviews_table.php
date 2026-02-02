<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agent_reviews')) {
        Schema::create('agent_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('content');
            $table->integer('rating')->min(1)->max(5);
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->boolean('recommendation')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_anonymous')->default(false);
            $table->string('status')->default('pending');
            $table->string('sentiment')->nullable();
            $table->boolean('has_response')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agent_id', 'user_id']);
            $table->index('agent_id');
            $table->index('user_id');
            $table->index('property_id');
            $table->index('status');
            $table->index('rating');
            $table->index('sentiment');
            $table->index('is_verified');
            $table->index('created_at');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_reviews');
    }
};

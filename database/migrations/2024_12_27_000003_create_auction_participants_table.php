<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'approved', 'rejected', 'suspended'])->default('registered');
            $table->timestamp('registration_time');
            $table->text('verification_notes')->nullable();
            $table->json('participant_data')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('bid_count')->default(0);
            $table->decimal('total_bid_amount', 15, 2)->default(0);
            $table->timestamp('last_bid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['auction_id', 'user_id']);
            $table->index(['auction_id', 'status']);
            $table->index(['user_id', 'registration_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_participants');
    }
};

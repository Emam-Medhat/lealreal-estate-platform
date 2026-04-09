<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('auction_bids')) {
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidder_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('previous_bid', 15, 2)->nullable();
            $table->enum('status', ['active', 'outbid', 'winning', 'rejected'])->default('active');
            $table->text('notes')->nullable();
            $table->json('bid_data')->nullable();
            $table->timestamp('bid_time');
            $table->boolean('is_automatic')->default(false);
            $table->decimal('max_automatic_bid', 15, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['auction_id', 'amount']);
            $table->index(['bidder_id', 'auction_id']);
            $table->index(['status', 'bid_time']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
    }
};

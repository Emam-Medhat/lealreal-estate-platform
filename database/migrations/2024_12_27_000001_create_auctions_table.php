<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('auctions')) {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('starting_price', 15, 2);
            $table->decimal('reserve_price', 15, 2)->nullable();
            $table->decimal('current_bid', 15, 2)->nullable();
            $table->integer('bid_count')->default(0);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['draft', 'active', 'ended', 'cancelled'])->default('draft');
            $table->enum('auction_type', ['english', 'dutch', 'sealed_bid'])->default('english');
            $table->json('auction_settings')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('auto_extend')->default(false);
            $table->integer('extend_duration')->default(5); // minutes
            $table->decimal('bid_increment', 15, 2)->default(1000);
            $table->decimal('minimum_bid', 15, 2)->nullable();
            $table->boolean('requires_verification')->default(true);
            $table->decimal('buyer_fee_percentage', 5, 2)->default(2.5);
            $table->decimal('seller_fee_percentage', 5, 2)->default(2.5);
            $table->text('featured_image')->nullable();
            $table->json('images')->nullable();
            $table->json('documents')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_time', 'end_time']);
            $table->index(['property_id', 'status']);
            $table->index(['seller_id', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};

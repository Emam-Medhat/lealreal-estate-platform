<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tracks every shareholder's stake in a tokenised property asset.
     */
    public function up(): void
    {
        Schema::create('fractional_ownerships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_asset_id')
                ->constrained('property_assets')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Share details
            $table->unsignedInteger('shares_owned')->default(0);
            $table->decimal('percentage', 8, 5)->default(0)->comment('Ownership % (shares/total_shares * 100)');
            $table->decimal('purchase_price', 15, 2)->default(0)->comment('Total paid for these shares');
            $table->decimal('price_per_share', 15, 2)->default(0)->comment('Per-share price at time of purchase');

            // Earnings
            $table->decimal('dividends_earned', 15, 2)->default(0)
                ->comment('Cumulative dividends/rental income distributed to this owner');
            $table->decimal('dividends_withdrawn', 15, 2)->default(0);

            // Timestamps
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            // One user may hold shares in the same asset only once (merged on re-purchase)
            $table->unique(['property_asset_id', 'user_id']);

            $table->index('user_id');
            $table->index('property_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fractional_ownerships');
    }
};

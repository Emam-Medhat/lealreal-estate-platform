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
        Schema::create('metaverse_property_nfts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metaverse_property_id')->constrained();
            $table->string('blockchain');
            $table->string('contract_address');
            $table->string('token_id')->unique();
            $table->string('token_uri');
            $table->json('metadata')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 10)->default('ETH');
            $table->decimal('royalty_percentage', 5, 2)->default(10);
            $table->boolean('is_for_sale')->default(false);
            $table->json('auction_settings')->nullable();
            $table->string('verification_status')->default('pending');
            $table->string('status')->default('minted');
            $table->timestamp('minted_at')->nullable();
            $table->timestamp('last_sale_at')->nullable();
            $table->integer('total_sales_count')->default(0);
            $table->decimal('total_volume')->default(0);
            $table->decimal('highest_bid', 15, 2)->nullable();
            $table->timestamp('auction_end_time')->nullable();
            $table->decimal('buy_now_price', 15, 2)->nullable();
            $table->decimal('reserve_price', 15, 2)->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['metaverse_property_id', 'status']);
            $table->index(['blockchain', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['is_for_sale', 'status']);
            $table->index(['verification_status', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['price', 'currency', 'status']);
            $table->index(['view_count', 'status']);
            $table->index(['like_count', 'status']);
            $table->index(['share_count', 'status']);
            $table->index(['total_sales_count', 'status']);
            $table->index(['total_volume', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

            // Full-text search index
            $table->index(['token_id', 'contract_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metaverse_property_nfts');
    }
};

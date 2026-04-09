<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the property_assets table which links a property to its
     * on-chain tokenised representation (fractional/NFT token).
     */
    public function up(): void
    {
        Schema::create('property_assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            // Blockchain token identity
            $table->string('token_id')->unique()->comment('Unique blockchain token / NFT ID');
            $table->string('smart_contract_address', 42)->nullable()->comment('Deployed ERC-20 / ERC-1155 contract address');
            $table->string('network')->default('ethereum')->comment('Blockchain network: ethereum, polygon, bsc …');

            // Fractional share configuration
            $table->unsignedInteger('total_shares')->default(100)->comment('Total tokenised shares (denominates 100%)');
            $table->unsignedInteger('available_shares')->default(100)->comment('Shares not yet sold');
            $table->decimal('share_price', 15, 2)->default(0)->comment('Price per share in platform currency');
            $table->decimal('total_value', 15, 2)->default(0)->comment('Total asset valuation');

            // Status / lifecycle
            $table->enum('status', ['draft', 'active', 'locked', 'sold', 'delisted'])
                ->default('draft');

            // Extra metadata (IPFS URI, royalties, etc.)
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index('token_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_assets');
    }
};

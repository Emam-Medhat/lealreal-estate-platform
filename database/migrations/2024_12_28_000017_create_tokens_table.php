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
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('address', 42)->unique();
            $table->string('name');
            $table->string('symbol');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('whitepaper_url')->nullable();
            $table->enum('type', ['fungible', 'non_fungible', 'semi_fungible'])->default('fungible');
            $table->enum('standard', ['erc20', 'erc721', 'erc1155', 'custom'])->default('erc20');
            $table->decimal('total_supply', 30, 18);
            $table->decimal('circulating_supply', 30, 18);
            $table->integer('decimals')->default(18);
            $table->string('contract_address', 42)->nullable();
            $table->string('creator_address', 42);
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->decimal('price_usd', 20, 8)->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('volume_24h', 20, 2)->nullable();
            $table->integer('holders_count')->default(0);
            $table->json('features')->nullable();
            $table->json('social_links')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('symbol');
            $table->index('type');
            $table->index('standard');
            $table->index('status');
            $table->index('is_verified');
            $table->index('contract_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};

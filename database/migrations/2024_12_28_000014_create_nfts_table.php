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
        if (!Schema::hasTable('nfts')) {
        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->string('token_id', 78)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('animation_url')->nullable();
            $table->string('external_url')->nullable();
            $table->string('contract_address', 42);
            $table->integer('token_standard')->default(721);
            $table->string('category')->default('general');
            $table->json('attributes')->nullable();
            $table->json('properties')->nullable();
            $table->integer('supply')->default(1);
            $table->decimal('price', 20, 8)->nullable();
            $table->string('currency', 10)->default('ETH');
            $table->enum('status', ['available', 'sold', 'auction', 'burned'])->default('available');
            $table->string('owner_address', 42)->nullable();
            $table->string('creator_address', 42);
            $table->timestamp('minted_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->json('royalty')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('token_id');
            $table->index('contract_address');
            $table->index('owner_address');
            $table->index('creator_address');
            $table->index('status');
            $table->index('category');
            $table->index('is_verified');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfts');
    }
};

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
        if (!Schema::hasTable('property_tokens')) {
        Schema::create('property_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_address', 42)->unique();
            $table->string('token_name');
            $table->string('token_symbol');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->decimal('property_value', 20, 2);
            $table->decimal('property_value_usd', 20, 2);
            $table->integer('total_tokens');
            $table->integer('max_tokens');
            $table->decimal('token_price', 20, 2);
            $table->decimal('token_price_usd', 8, 4);
            $table->integer('tokens_sold')->default(0);
            $table->decimal('total_raised', 20, 2)->default(0);
            $table->decimal('total_raised_usd', 20, 2)->default(0);
            $table->decimal('min_token_price', 20, 2);
            $table->decimal('max_token_price', 20, 2)->nullable();
            $table->decimal('annual_rental_yield', 8, 4)->nullable();
            $table->decimal('expected_appreciation', 8, 4)->nullable();
            $table->timestamp('tokenization_date')->nullable();
            $table->timestamp('sale_start_date')->nullable();
            $table->timestamp('sale_end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'sold_out', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->integer('holder_count')->default(0);
            $table->json('tokenomics')->nullable();
            $table->json('legal_docs')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('token_address');
            $table->index('property_id');
            $table->index('status');
            $table->index('is_verified');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_tokens');
    }
};

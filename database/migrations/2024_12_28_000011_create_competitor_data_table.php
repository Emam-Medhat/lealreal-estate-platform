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
        Schema::create('competitor_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('industry');
            $table->decimal('market_share', 5, 2)->default(0);
            $table->decimal('revenue', 15, 2)->nullable();
            $table->integer('employees')->nullable();
            $table->json('products')->nullable();
            $table->json('pricing')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('threats')->nullable();
            $table->decimal('customer_satisfaction', 3, 2)->nullable();
            $table->decimal('brand_value', 15, 2)->nullable();
            $table->string('headquarters')->nullable();
            $table->string('founded_year')->nullable();
            $table->enum('status', ['active', 'inactive', 'acquired', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['name', 'industry']);
            $table->index('market_share');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_data');
    }
};

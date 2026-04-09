<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('investment_opportunities')) {
        Schema::create('investment_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type')->default('real_estate'); // real_estate, technology, fund, etc.
            $table->decimal('min_investment', 15, 2);
            $table->decimal('max_investment', 15, 2)->nullable();
            $table->decimal('expected_return', 8, 2); // percentage
            $table->string('duration'); // e.g., "24 شهر"
            $table->string('risk_level')->default('medium'); // low, medium, high
            $table->string('status')->default('active'); // active, inactive, closed
            $table->decimal('current_investment', 15, 2)->default(0);
            $table->integer('investors_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->json('features')->nullable(); // Additional features as JSON
            $table->text('terms_conditions')->nullable();
            $table->string('image_url')->nullable();
            $table->string('document_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_opportunities');
    }
};

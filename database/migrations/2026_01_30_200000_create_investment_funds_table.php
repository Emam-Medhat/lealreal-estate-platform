<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('investment_funds')) {
        Schema::create('investment_funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('type'); // real_estate, technology, renewable_energy, ecommerce, education
            $table->decimal('min_investment', 15, 2);
            $table->decimal('max_investment', 15, 2)->nullable();
            $table->decimal('expected_return', 8, 2); // percentage
            $table->string('duration'); // e.g., "12 شهر"
            $table->string('risk_level')->default('medium'); // low, medium, high
            $table->decimal('total_funded', 15, 2)->default(0);
            $table->decimal('funding_goal', 15, 2);
            $table->integer('investors_count')->default(0);
            $table->string('manager');
            $table->string('status')->default('active'); // active, inactive, closed
            $table->boolean('featured')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->json('holdings')->nullable(); // Portfolio holdings
            $table->text('terms_conditions')->nullable();
            $table->string('image_url')->nullable();
            $table->string('document_url')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_funds');
    }
};

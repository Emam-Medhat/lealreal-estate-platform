<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('property_financial_analyses')) {
        Schema::create('property_financial_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('analysis_date');
            $table->decimal('current_value', 12, 2);
            $table->decimal('purchase_price', 12, 2);
            $table->date('purchase_date');
            $table->decimal('annual_rental_income', 12, 2);
            $table->decimal('operating_expenses', 12, 2);
            $table->decimal('vacancy_rate', 5, 3);
            $table->decimal('appreciation_rate', 5, 3);
            $table->decimal('inflation_rate', 5, 3);
            $table->decimal('discount_rate', 5, 3);
            $table->integer('holding_period');
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 3);
            $table->integer('loan_term');
            $table->string('property_type');
            $table->string('location');
            $table->json('market_conditions')->nullable();
            $table->string('analysis_type');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'user_id']);
            $table->index('analysis_date');
            $table->index('analysis_type');
            $table->index('status');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_financial_analyses');
    }
};

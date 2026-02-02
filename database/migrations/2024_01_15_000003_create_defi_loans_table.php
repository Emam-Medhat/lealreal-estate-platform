<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('defi_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->string('borrower_name');
            $table->string('borrower_email');
            $table->string('borrower_phone');
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('loan_term_months');
            $table->text('purpose');
            $table->decimal('collateral_value', 15, 2);
            $table->decimal('monthly_income', 15, 2);
            $table->integer('credit_score');
            $table->decimal('monthly_payment', 15, 2);
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'defaulted', 'rejected'])->default('pending');
            $table->date('approved_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['borrower_email']);
            $table->index(['property_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('defi_loans');
    }
};

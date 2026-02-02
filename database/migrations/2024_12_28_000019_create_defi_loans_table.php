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
        if (!Schema::hasTable('defi_loans')) {
        Schema::create('defi_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_hash', 66)->unique();
            $table->string('borrower_address', 42);
            $table->string('lender_address', 42)->nullable();
            $table->string('collateral_address', 42)->nullable();
            $table->string('collateral_type')->nullable();
            $table->decimal('loan_amount', 30, 18);
            $table->string('loan_currency')->default('ETH');
            $table->decimal('collateral_amount', 30, 18)->nullable();
            $table->decimal('interest_rate', 8, 4);
            $table->string('interest_type')->default('fixed');
            $table->integer('loan_term_days');
            $table->timestamp('loan_start_date')->nullable();
            $table->timestamp('loan_end_date')->nullable();
            $table->decimal('repayment_amount', 30, 18)->nullable();
            $table->decimal('outstanding_balance', 30, 18)->default(0);
            $table->integer('repayments_made')->default(0);
            $table->integer('total_repayments')->default(0);
            $table->decimal('collateral_ratio', 8, 4)->nullable();
            $table->decimal('liquidation_price', 30, 18)->nullable();
            $table->enum('status', ['requested', 'active', 'repaid', 'defaulted', 'liquidated'])->default('requested');
            $table->timestamp('status_updated_at')->nullable();
            $table->json('terms')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('loan_hash');
            $table->index('borrower_address');
            $table->index('lender_address');
            $table->index('collateral_address');
            $table->index('status');
            $table->index('user_id');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defi_loans');
    }
};

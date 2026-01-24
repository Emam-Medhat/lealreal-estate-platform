<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('SAR');
            $table->date('expense_date');
            $table->string('receipt_number')->nullable();
            $table->string('vendor')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check'])->default('bank_transfer');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_budget_id', 'status']);
            $table->index(['category_id']);
            $table->index(['expense_date']);
            $table->index(['payment_method']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_expenses');
    }
};

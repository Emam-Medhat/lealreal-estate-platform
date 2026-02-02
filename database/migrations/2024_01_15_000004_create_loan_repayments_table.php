<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('defi_loans')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'late', 'missed'])->default('pending');
            $table->decimal('late_fee', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'due_date']);
            $table->index(['status']);
            $table->index(['due_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_repayments');
    }
};

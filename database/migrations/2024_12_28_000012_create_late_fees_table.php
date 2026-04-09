<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('late_fees')) {
        Schema::create('late_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('rent_payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('fee_number')->unique();
            $table->decimal('base_amount', 10, 2);
            $table->enum('fee_type', ['fixed', 'percentage', 'daily', 'compound']);
            $table->decimal('percentage_rate', 5, 2)->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->date('due_date');
            $table->date('grace_period_end');
            $table->date('calculated_from');
            $table->date('calculated_to');
            $table->integer('days_late');
            $table->decimal('total_fee', 10, 2);
            $table->enum('status', ['pending', 'applied', 'waived', 'partially_waived'])->default('pending');
            $table->datetime('applied_date')->nullable();
            $table->datetime('waived_date')->nullable();
            $table->foreignId('waived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('waiver_reason')->nullable();
            $table->decimal('waived_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('auto_calculated')->default(false);
            $table->boolean('compound_interest')->default(false);
            $table->decimal('max_fee', 10, 2)->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'status']);
            $table->index(['rent_payment_id']);
            $table->index(['status', 'due_date']);
            $table->index(['applied_date']);
            $table->index(['days_late']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('late_fees');
    }
};

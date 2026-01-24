<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lease_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->string('renewal_number')->unique();
            $table->date('old_end_date');
            $table->date('new_end_date');
            $table->decimal('old_rent_amount', 10, 2);
            $table->decimal('new_rent_amount', 10, 2);
            $table->enum('renewal_type', ['extension', 'rent_increase', 'rent_decrease', 'terms_change']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('renewal_terms')->nullable();
            $table->text('notes')->nullable();
            $table->datetime('requested_at')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->date('effective_date')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'status']);
            $table->index(['status', 'requested_at']);
            $table->index(['effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_renewals');
    }
};

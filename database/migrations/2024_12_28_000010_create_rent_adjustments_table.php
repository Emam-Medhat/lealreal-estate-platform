<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rent_adjustments')) {
        Schema::create('rent_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->string('adjustment_number')->unique();
            $table->decimal('old_rent', 10, 2);
            $table->decimal('new_rent', 10, 2);
            $table->enum('adjustment_type', ['increase', 'decrease']);
            $table->decimal('adjustment_amount', 10, 2);
            $table->decimal('adjustment_percentage', 5, 2);
            $table->date('effective_date');
            $table->enum('reason', ['market_rate', 'maintenance', 'improvements', 'inflation', 'negotiation', 'other']);
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'applied', 'rejected'])->default('pending');
            $table->datetime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('applied_at')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('documents')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['status', 'effective_date']);
            $table->index(['adjustment_type']);
            $table->index(['effective_date']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_adjustments');
    }
};

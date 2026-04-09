<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agent_commissions')) {
        Schema::create('agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('transaction_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('percentage', 5, 2);
            $table->enum('type', ['sale', 'rental', 'referral'])->default('sale');
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_commissions');
    }
};

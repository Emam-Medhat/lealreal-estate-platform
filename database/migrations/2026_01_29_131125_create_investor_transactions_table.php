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
        if (!Schema::hasTable('investor_transactions')) {
        Schema::create('investor_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->enum('type', ['investment', 'return', 'withdrawal', 'deposit']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
            
            $table->index(['investor_id', 'type', 'status']);
            $table->index(['transaction_date']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_transactions');
    }
};

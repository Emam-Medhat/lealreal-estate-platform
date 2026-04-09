<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Immutable ledger table — records are NEVER updated after creation.
     * Each row's `current_hash` is SHA-256(payload + previous_hash),
     * forming an unbroken hash chain detectable-tamper log.
     */
    public function up(): void
    {
        Schema::create('transaction_ledgers', function (Blueprint $table) {
            $table->id();

            // Polymorphic reference to whatever triggered the entry
            $table->string('reference_type')->nullable()->comment('e.g. App\\Models\\PropertyAsset');
            $table->unsignedBigInteger('reference_id')->nullable();

            // Action classification
            $table->string('action')->comment('share_purchase, escrow_locked, escrow_executed, dividend_distributed, property_sold …');

            // Hash chain
            $table->string('previous_hash', 64)->default('genesis')
                ->comment('current_hash of the immediately preceding ledger entry (or "genesis")');
            $table->string('current_hash', 64)->unique()
                ->comment('SHA-256(payload + previous_hash)');

            // Arbitrary JSON payload describing the transaction
            $table->json('payload');

            // Recorded by (system/user)
            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Only created_at — no updated_at (immutable record)
            $table->timestamp('created_at')->useCurrent();

            $table->index(['reference_type', 'reference_id']);
            $table->index('action');
            $table->index('previous_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_ledgers');
    }
};

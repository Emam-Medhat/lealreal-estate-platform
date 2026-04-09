<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Escrow contracts hold funds until inspection and legal conditions are met.
     */
    public function up(): void
    {
        Schema::create('escrow_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_asset_id')
                ->constrained('property_assets')
                ->cascadeOnDelete();

            // Linked on-chain smart contract address (optional — may be deployed later)
            $table->string('smart_contract_address', 42)->nullable();

            // Parties
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('seller_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Financials
            $table->decimal('amount', 15, 2)->comment('Funds held in escrow');
            $table->string('currency', 10)->default('USD');

            // State machine: pending → locked → executed | disputed
            $table->enum('state', ['pending', 'locked', 'executed', 'disputed'])
                ->default('pending');

            // Release / dispute conditions stored as JSON flags:
            // { "property_inspection": false, "legal_approval": false }
            $table->json('conditions')->nullable();

            // Remarks / dispute reason
            $table->text('notes')->nullable();

            // State timestamps
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('disputed_at')->nullable();

            $table->timestamps();

            $table->index(['property_asset_id', 'state']);
            $table->index('buyer_id');
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_contracts');
    }
};

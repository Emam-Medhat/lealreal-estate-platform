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
        Schema::create('metaverse_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('metaverse_property_id')->nullable()->constrained();
            $table->foreignId('virtual_land_id')->nullable()->constrained();
            $table->foreignId('metaverse_property_nft_id')->nullable()->constrained();
            $table->foreignId('metaverse_showroom_id')->nullable()->constrained();
            $table->foreignId('virtual_property_tour_id')->nullable()->constrained();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->string('blockchain')->nullable();
            $table->decimal('gas_fee', 15, 2)->nullable();
            $table->integer('confirmation_count')->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->string('refund_reason')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->json('additional_data')->nullable();
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->decimal('fraud_score', 3, 2)->default(0);
            $table->string('risk_level')->default('low');
            $table->string('compliance_status')->default('compliant');
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->string('tax_currency', 10)->nullable();
            $table->decimal('fee_amount', 15, 2)->nullable();
            $table->string('fee_currency', 10)->nullable();
            $table->decimal('net_amount', 15, 2)->nullable();
            $table->string('net_currency', 10)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'status']);
            $table->index(['metaverse_property_id', 'status']);
            $table->index(['virtual_land_id', 'status']);
            $table->index(['metaverse_property_nft_id', 'status']);
            $table->index(['metaverse_showroom_id', 'status']);
            $table->index(['virtual_property_tour_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['amount', 'currency', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['blockchain', 'status']);
            $table->index(['verification_status', 'status']);
            $table->index(['risk_level', 'status']);
            $table->index(['compliance_status', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('transaction_hash');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metaverse_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contracts')) {
            return;
        }
        if (!Schema::hasTable('contracts')) {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('contract_type', ['purchase', 'rent', 'lease_option', 'rental_agreement'])->default('purchase');
            $table->enum('status', ['draft', 'pending_review', 'awaiting_signature', 'signed', 'executed', 'completed', 'terminated', 'cancelled', 'expired'])->default('draft');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('earnest_money', 15, 2)->nullable();
            $table->date('contract_date');
            $table->date('closing_date');
            $table->date('possession_date')->nullable();
            $table->json('contract_terms');
            $table->json('contingencies')->nullable();
            $table->json('property_details')->nullable();
            $table->json('financial_terms')->nullable();
            $table->json('closing_costs')->nullable();
            $table->text('special_provisions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('contract_created_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['contract_number']);
            $table->index(['closing_date', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

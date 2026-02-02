<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offers')) {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('offer_amount', 15, 2);
            $table->enum('offer_type', ['purchase', 'rent', 'lease_option'])->default('purchase');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'accepted', 'rejected', 'withdrawn', 'expired'])->default('draft');
            $table->text('message')->nullable();
            $table->json('offer_terms')->nullable();
            $table->json('contingencies')->nullable();
            $table->date('offer_expiration_date');
            $table->decimal('earnest_money', 15, 2)->nullable();
            $table->string('earnest_money_status', 50)->default('pending');
            $table->date('proposed_closing_date')->nullable();
            $table->enum('financing_type', ['cash', 'conventional', 'fha', 'va', 'other'])->nullable();
            $table->boolean('is_contingent')->default(false);
            $table->json('contingency_details')->nullable();
            $table->text('seller_notes')->nullable();
            $table->text('buyer_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['offer_expiration_date', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};

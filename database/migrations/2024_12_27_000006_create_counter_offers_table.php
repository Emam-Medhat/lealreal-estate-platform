<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counter_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('countered_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('countered_to')->constrained('users')->onDelete('cascade');
            $table->decimal('counter_amount', 15, 2);
            $table->text('counter_message')->nullable();
            $table->json('counter_terms')->nullable();
            $table->json('modified_contingencies')->nullable();
            $table->date('proposed_closing_date')->nullable();
            $table->decimal('earnest_money', 15, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired', 'withdrawn'])->default('pending');
            $table->date('expiration_date');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->softDeletes();

            $table->index(['offer_id', 'status']);
            $table->index(['countered_by', 'created_at']);
            $table->index(['countered_to', 'status']);
            $table->index(['expiration_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counter_offers');
    }
};

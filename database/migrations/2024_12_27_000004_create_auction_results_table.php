<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('auction_results')) {
        Schema::create('auction_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('winning_bid', 15, 2);
            $table->decimal('reserve_price_met', 15, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('result_time');
            $table->text('result_notes')->nullable();
            $table->json('result_data')->nullable();
            $table->decimal('buyer_fee', 15, 2)->default(0);
            $table->decimal('seller_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_status', 50)->default('pending');
            $table->timestamp('payment_due_date')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['auction_id', 'status']);
            $table->index(['winner_id', 'result_time']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_results');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('commissions')) {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('commission_rate', 5, 2); // percentage
            $table->decimal('transaction_value', 15, 2);
            $table->enum('type', ['selling', 'renting', 'referral']);
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->date('earned_date');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('commissions');
    }
};

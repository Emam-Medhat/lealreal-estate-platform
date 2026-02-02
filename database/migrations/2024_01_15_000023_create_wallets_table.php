<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('wallets')) {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('frozen_balance', 15, 2)->default(0);
            $table->decimal('total_earned', 15, 2)->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->string('currency')->default('SAR');
            $table->enum('status', ['active', 'frozen', 'suspended'])->default('active');
            $table->json('settings')->nullable(); // auto-withdrawal, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};

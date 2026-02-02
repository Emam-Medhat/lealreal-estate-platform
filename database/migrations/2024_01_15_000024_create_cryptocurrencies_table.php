<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cryptocurrencies')) {
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('symbol'); // BTC, ETH, etc.
            $table->string('name'); // Bitcoin, Ethereum, etc.
            $table->decimal('balance', 20, 8)->default(0);
            $table->decimal('usd_value', 15, 2)->default(0);
            $table->decimal('buy_price', 20, 8)->nullable();
            $table->decimal('current_price', 20, 8)->nullable();
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->enum('type', ['holding', 'staking', 'lending', 'trading']);
            $table->string('wallet_address')->nullable();
            $table->json('metadata')->nullable(); // transaction history, etc.
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cryptocurrencies');
    }
};

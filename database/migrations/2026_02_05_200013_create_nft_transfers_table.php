<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nft_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_id')->constrained()->onDelete('cascade');
            $table->string('from_address', 42);
            $table->string('to_address', 42);
            $table->string('tx_hash', 66);
            $table->bigInteger('gas_used')->nullable();
            $table->decimal('cost', 15, 8)->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();

            $table->index(['nft_id']);
            $table->index(['from_address']);
            $table->index(['to_address']);
            $table->index(['tx_hash']);
            $table->index(['transferred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nft_transfers');
    }
};

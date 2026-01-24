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
        Schema::create('blockchain_records', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 64)->unique();
            $table->string('previous_hash', 64);
            $table->integer('height')->unique();
            $table->json('data');
            $table->string('type', 50)->default('block');
            $table->integer('difficulty')->default(0);
            $table->integer('nonce')->default(0);
            $table->string('miner')->nullable();
            $table->timestamp('timestamp');
            $table->integer('transaction_count')->default(0);
            $table->decimal('size', 10, 2)->default(0);
            $table->string('merkle_root', 64)->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'orphaned'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('height');
            $table->index('timestamp');
            $table->index('status');
            $table->index('miner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_records');
    }
};

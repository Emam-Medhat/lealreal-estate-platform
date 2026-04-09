<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('ipfs_hash')->nullable();
            $table->string('chain_tx')->nullable();
            $table->string('status')->default('draft');
            $table->string('party_a_ekyc_id')->nullable();
            $table->string('party_b_ekyc_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_contracts');
    }
};

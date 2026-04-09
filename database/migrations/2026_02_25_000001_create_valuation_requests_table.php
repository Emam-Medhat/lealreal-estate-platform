<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valuation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('city')->nullable();
            $table->json('inputs')->nullable();
            $table->json('result_summary')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valuation_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_simulations', function (Blueprint $table) {
            $table->id();
            $table->json('inputs_json')->nullable();
            $table->json('results_json')->nullable();
            $table->string('share_card_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_simulations');
    }
};

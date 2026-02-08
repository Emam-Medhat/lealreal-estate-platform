<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 15, 8);
            $table->date('date');
            $table->string('source', 50)->default('api');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['from_currency', 'to_currency', 'date'], 'currency_rates_unique');
            $table->index(['from_currency']);
            $table->index(['to_currency']);
            $table->index(['date']);
            $table->index(['source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};

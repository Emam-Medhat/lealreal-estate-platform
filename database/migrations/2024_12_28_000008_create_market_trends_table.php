<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('market_trends')) {
        Schema::create('market_trends', function (Blueprint $table) {
            $table->id();
            $table->string('trend_type', 50);
            $table->string('trend_name');
            $table->text('description')->nullable();
            $table->decimal('value', 12, 2);
            $table->decimal('previous_value', 12, 2)->nullable();
            $table->decimal('change_percentage', 5, 2);
            $table->string('trend_direction', 20);
            $table->decimal('confidence_level', 5, 2);
            $table->json('data_points')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('category', 50)->nullable();
            $table->string('region', 50)->nullable();
            $table->string('source')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['trend_type', 'category']);
            $table->index('region');
            $table->index('trend_direction');
            $table->index(['period_start', 'period_end']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('market_trends');
    }
};

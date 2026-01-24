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
        Schema::create('neighborhood_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('statistic_type', 50);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('period', 20)->default('monthly');
            $table->string('data_source', 255)->nullable();
            $table->string('collection_method', 100)->nullable();
            $table->date('collection_date')->nullable();
            $table->json('data_points')->nullable();
            $table->json('aggregated_data')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->json('comparative_data')->nullable();
            $table->json('forecast_data')->nullable();
            $table->json('visualization_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['statistic_type']);
            $table->index(['period']);
            $table->index(['collection_date']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhood_statistics');
    }
};

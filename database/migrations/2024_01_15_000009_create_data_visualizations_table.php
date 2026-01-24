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
        Schema::create('data_visualizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type'); // chart, graph, table, map, gauge
            $table->string('chart_type')->nullable(); // bar, line, pie, area, scatter, etc.
            $table->json('data_source');
            $table->json('chart_config'); // colors, axes, legends, etc.
            $table->json('data_series')->nullable();
            $table->text('description')->nullable();
            $table->integer('position_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['report_id', 'position_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_visualizations');
    }
};

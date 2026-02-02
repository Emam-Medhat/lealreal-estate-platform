<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('heatmaps')) {
        Schema::create('heatmaps', function (Blueprint $table) {
            $table->id();
            $table->string('page_url', 500);
            $table->string('heatmap_type', 50);
            $table->string('time_range', 20);
            $table->json('data');
            $table->timestamps();
            
            $table->index(['page_url', 'heatmap_type'], 'heatmaps_page_url_type_index');
            $table->index('heatmap_type');
            $table->index('time_range');
            $table->index('created_at');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('heatmaps');
    }
};

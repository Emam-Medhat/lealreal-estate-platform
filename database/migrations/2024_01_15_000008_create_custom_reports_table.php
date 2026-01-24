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
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('data_sources'); // tables, views, external APIs
            $table->json('query_config'); // SQL queries, filters, aggregations
            $table->json('visualization_config')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('report_data')->nullable();
            $table->boolean('is_template')->default(false);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index(['is_template', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_reports');
    }
};

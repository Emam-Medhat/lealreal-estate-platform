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
        if (!Schema::hasTable('reports')) {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // sales, performance, market, financial, compliance, custom
            $table->text('description')->nullable();
            $table->json('parameters')->nullable();
            $table->json('filters')->nullable();
            $table->json('data')->nullable();
            $table->string('status'); // generating, completed, failed, scheduled
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('generated_by')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('file_path')->nullable();
            $table->string('format')->default('json'); // json, pdf, excel, csv
            $table->boolean('is_public')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('generated_at');
            $table->index('generated_by');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

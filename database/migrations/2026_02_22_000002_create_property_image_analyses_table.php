<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * property_image_analyses
     *
     * Stores one record per image submitted for AI analysis.
     * Status lifecycle: pending → processing → completed | failed
     *
     * This table is written by:
     *  - ImageAnalysisController::upload()   (creates pending record)
     *  - AnalyzePropertyImageJob::handle()   (updates with results)
     */
    public function up(): void
    {
        if (Schema::hasTable('property_image_analyses')) {
            return;
        }

        Schema::create('property_image_analyses', function (Blueprint $table) {
            $table->id();

            // Nullable because admin can analyze arbitrary images
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->foreign('property_id')
                ->references('id')->on('properties')
                ->onDelete('set null');

            $table->string('image_path', 500);

            // Analysis results (written by the background job)
            $table->string('room_type', 50)->nullable();
            // living_room | bedroom | kitchen | bathroom | exterior | garden | pool | other

            $table->string('furniture_quality', 20)->nullable();
            // none | basic | standard | luxury

            $table->string('brightness_level', 20)->nullable();
            // dark | moderate | bright

            $table->unsignedTinyInteger('quality_score')->nullable();
            // 0–100

            $table->json('issues')->nullable();
            // ["blurry","dark","cluttered", …]

            // Which provider produced these results
            $table->string('source', 30)->nullable();
            // google_vision | azure_vision | openai_vision | fallback

            // Job lifecycle
            $table->string('status', 20)->default('pending')->index();
            // pending | processing | completed | failed

            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->foreign('submitted_by')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_image_analyses');
    }
};

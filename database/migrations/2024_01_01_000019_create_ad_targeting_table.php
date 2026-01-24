<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_targeting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('ad_campaigns')->onDelete('cascade');
            $table->enum('target_type', ['advertisement', 'campaign']);
            $table->json('audience_criteria')->nullable();
            $table->json('location_criteria')->nullable();
            $table->json('age_range')->nullable();
            $table->json('gender_criteria')->nullable();
            $table->json('interest_criteria')->nullable();
            $table->json('behavior_criteria')->nullable();
            $table->json('device_criteria')->nullable();
            $table->json('time_criteria')->nullable();
            $table->json('language_criteria')->nullable();
            $table->json('income_criteria')->nullable();
            $table->json('education_criteria')->nullable();
            $table->json('custom_criteria')->nullable();
            $table->json('exclusion_criteria')->nullable();
            $table->decimal('targeting_score', 5, 2)->default(0);
            $table->integer('estimated_reach')->default(0);
            $table->integer('actual_reach')->default(0);
            $table->decimal('match_rate', 5, 2)->default(0);
            $table->json('performance_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['advertisement_id']);
            $table->index(['campaign_id']);
            $table->index(['target_type']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_targeting');
    }
};

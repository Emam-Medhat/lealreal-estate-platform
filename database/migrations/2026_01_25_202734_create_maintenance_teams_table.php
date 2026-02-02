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
        if (!Schema::hasTable('maintenance_teams')) {
        Schema::create('maintenance_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('leader_name');
            $table->string('leader_email');
            $table->string('leader_phone')->nullable();
            $table->enum('specialization', ['general', 'electrical', 'plumbing', 'hvac', 'structural', 'painting', 'landscaping'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('max_concurrent_jobs')->default(3);
            $table->text('skills')->nullable(); // JSON array of skills
            $table->json('working_hours')->nullable(); // JSON with working schedule
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'specialization']);
            $table->unique(['leader_email']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_teams');
    }
};

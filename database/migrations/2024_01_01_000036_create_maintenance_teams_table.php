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
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('team_leader_name');
            $table->string('team_leader_phone');
            $table->string('team_leader_email');
            $table->json('members')->nullable();
            $table->json('specializations')->nullable();
            $table->json('certifications')->nullable();
            $table->json('service_areas')->nullable();
            $table->json('working_hours')->nullable();
            $table->integer('max_concurrent_jobs')->default(3);
            $table->integer('current_jobs')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_jobs')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->decimal('average_response_time', 5, 2)->nullable();
            $table->decimal('average_completion_time', 5, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('vehicle_info')->nullable();
            $table->json('equipment')->nullable();
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['is_available']);
            $table->index(['team_leader_email']);
            $table->index(['average_rating']);
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('construction_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('developer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('contractor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('project_name');
            $table->text('description')->nullable();
            $table->enum('project_type', ['residential', 'commercial', 'industrial', 'infrastructure']);
            $table->decimal('total_cost', 15, 2);
            $table->decimal('current_cost', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('planned_end_date');
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('planning');
            $table->integer('progress_percentage')->default(0);
            $table->json('team_members')->nullable(); // team member IDs and roles
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('construction_projects');
    }
};

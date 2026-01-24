<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamp('joined_at');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_team_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['role_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_members');
    }
};

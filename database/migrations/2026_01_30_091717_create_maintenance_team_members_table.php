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
        Schema::create('maintenance_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member'); // member, leader, supervisor
            $table->timestamp('joined_at')->default(now());
            $table->timestamps();
            
            // Unique constraint to prevent duplicate memberships
            $table->unique(['maintenance_team_id', 'user_id']);
            
            // Indexes for performance
            $table->index(['maintenance_team_id']);
            $table->index(['user_id']);
            $table->index(['role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_team_members');
    }
};

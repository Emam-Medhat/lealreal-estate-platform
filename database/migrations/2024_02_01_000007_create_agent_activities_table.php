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
        if (!Schema::hasTable('agent_activities')) {
        Schema::create('agent_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('value')->nullable();
            $table->enum('status', ['completed', 'active', 'pending', 'cancelled'])->default('completed');
            $table->string('icon')->default('fa-clipboard-list');
            $table->enum('type', ['sale', 'meeting', 'listing', 'offer', 'call', 'email', 'other'])->default('other');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('client_name')->nullable();
            $table->string('property_title')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['agent_id', 'status']);
            $table->index(['agent_id', 'created_at']);
            $table->index('status');
            $table->index('type');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_activities');
    }
};

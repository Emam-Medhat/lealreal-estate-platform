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
        if (!Schema::hasTable('emergency_repairs')) {
        Schema::create('emergency_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('category', ['plumbing', 'electrical', 'hvac', 'structural', 'fire', 'gas', 'security', 'other'])->default('other');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->dateTime('reported_at');
            $table->dateTime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['is_urgent', 'status']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_repairs');
    }
};

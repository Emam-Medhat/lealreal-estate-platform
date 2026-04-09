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
        if (!Schema::hasTable('negotiation_participants')) {
        Schema::create('negotiation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['buyer', 'seller', 'mediator', 'agent', 'witness'])->default('buyer');
            $table->enum('status', ['active', 'inactive', 'withdrawn'])->default('active');
            $table->boolean('can_negotiate')->default(true);
            $table->boolean('can_approve')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            
            $table->index(['negotiation_id', 'user_id']);
            $table->index(['negotiation_id', 'role']);
            $table->index(['user_id', 'status']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_participants');
    }
};

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
        if (Schema::hasTable('agent_territories')) {
            return;
        }

        if (!Schema::hasTable('agent_territories')) {
        Schema::create('agent_territories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // e.g., 'city', 'neighborhood', 'district'
            $table->string('status')->default('active');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->json('postal_codes')->nullable();
            $table->json('neighborhoods')->nullable();
            $table->json('boundaries')->nullable();
            $table->json('coordinates')->nullable();
            $table->string('population_density')->nullable();
            $table->string('average_income')->nullable();
            $table->json('property_types')->nullable();
            $table->json('price_range')->nullable();
            $table->string('competition_level')->nullable();
            $table->string('market_potential')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('assigned_date')->nullable();
            
            // Additional fields from Model
            $table->boolean('is_active')->default(true);
            $table->boolean('is_exclusive')->default(false);
            $table->json('extra_attributes')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_territories');
    }
};

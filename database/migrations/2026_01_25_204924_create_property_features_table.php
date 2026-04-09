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
        if (!Schema::hasTable('property_features')) {
        Schema::create('property_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->text('description')->nullable();
            $table->enum('category', ['general', 'exterior', 'interior', 'location', 'investment', 'sustainability', 'technology', 'security', 'other'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'category']);
            $table->index(['sort_order']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_features');
    }
};

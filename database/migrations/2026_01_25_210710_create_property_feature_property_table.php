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
        if (!Schema::hasTable('property_feature_property')) {
        Schema::create('property_feature_property', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_feature_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->primary(['property_id', 'property_feature_id']);
            $table->index(['property_id']);
            $table->index(['property_feature_id']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_feature_property');
    }
};

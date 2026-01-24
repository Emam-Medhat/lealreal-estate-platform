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
        Schema::create('buyer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->text('preferences')->nullable();
            $table->string('property_type')->nullable();
            $table->string('location_preference')->nullable();
            $table->integer('bedrooms_min')->nullable();
            $table->integer('bedrooms_max')->nullable();
            $table->integer('bathrooms_min')->nullable();
            $table->integer('bathrooms_max')->nullable();
            $table->decimal('area_min', 8, 2)->nullable();
            $table->decimal('area_max', 8, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_profiles');
    }
};

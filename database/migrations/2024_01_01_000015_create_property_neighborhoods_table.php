<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable(); // distance from property
            $table->integer('travel_time_minutes')->nullable();
            $table->string('transportation_method')->nullable(); // walking, driving, public_transport
            $table->json('amenities')->nullable(); // nearby schools, hospitals, etc.
            $table->json('demographics')->nullable();
            $table->decimal('safety_rating', 3, 2)->nullable(); // 0-10 scale
            $table->decimal('livability_score', 3, 2)->nullable(); // 0-10 scale
            $table->json('statistics')->nullable(); // population, avg income, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_neighborhoods');
    }
};

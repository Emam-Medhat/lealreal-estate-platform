<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('parking_spaces')->nullable();
            $table->year('year_built')->nullable();
            $table->decimal('area', 10, 2);
            $table->string('area_unit');
            $table->decimal('land_area', 10, 2)->nullable();
            $table->string('land_area_unit')->nullable();
            $table->json('specifications')->nullable();
            $table->json('materials')->nullable();
            $table->text('interior_features')->nullable();
            $table->text('exterior_features')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_details');
    }
};

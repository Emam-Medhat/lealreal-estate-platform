<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->string('code', 5)->unique();
            $table->timestamps();
        });

        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->foreignId('governorate_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('property_locations', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('governorate_id')->nullable()->constrained('governorates');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            // Keep the string columns for legacy support or caching, but rename or make nullable
            $table->string('country')->nullable()->change();
            $table->string('state')->nullable()->change(); // governorate
            $table->string('city')->nullable()->change();
            $table->string('district')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('property_locations', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['district_id']);
            $table->dropColumn(['country_id', 'governorate_id', 'city_id', 'district_id']);
            $table->string('country')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });

        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('governorates');
        Schema::dropIfExists('countries');
    }
};

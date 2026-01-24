<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('license_number')->unique();
            $table->json('specializations');
            $table->integer('experience_years');
            $table->json('certifications')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['email']);
            $table->index(['license_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspectors');
    }
};

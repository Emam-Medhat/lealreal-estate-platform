<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agent_profiles')) {
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->text('about_me')->nullable();
            $table->string('specialties')->nullable();
            $table->string('languages')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->json('achievements')->nullable();
            $table->json('certifications')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_phone')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_profiles');
    }
};

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
        Schema::create('appraisers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('license_number')->unique();
            $table->string('license_type');
            $table->date('license_expiry');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('bio')->nullable();
            $table->text('bio_ar')->nullable();
            $table->json('specializations')->nullable();
            $table->json('certifications')->nullable();
            $table->json('experience')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_appraisals')->default(0);
            $table->decimal('average_fee', 10, 2)->nullable();
            $table->json('service_areas')->nullable();
            $table->json('languages')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index(['status', 'is_verified']);
            $table->index(['license_type']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisers');
    }
};

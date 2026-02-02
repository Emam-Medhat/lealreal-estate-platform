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
        if (!Schema::hasTable('developers')) {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('license_number')->unique();
            $table->string('commercial_register')->unique();
            $table->string('tax_number')->nullable();
            $table->enum('developer_type', ['residential', 'commercial', 'mixed', 'industrial']);
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('logo')->nullable();
            $table->json('address');
            $table->json('contact_person');
            $table->year('established_year')->nullable();
            $table->integer('total_projects')->default(0);
            $table->decimal('total_investment', 15, 2)->default(0);
            $table->json('specializations')->nullable();
            $table->json('certifications')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->json('social_media')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'is_verified']);
            $table->index(['developer_type']);
            $table->index(['rating']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};

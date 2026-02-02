<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('license_number');
            $table->string('commercial_register');
            $table->string('tax_number')->nullable();
            $table->enum('developer_type', ['residential', 'commercial', 'mixed', 'industrial']);
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
            $table->integer('established_year')->nullable();
            $table->string('website')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->integer('total_projects')->default(0);
            $table->integer('completed_projects')->default(0);
            $table->integer('ongoing_projects')->default(0);
            $table->decimal('total_investment', 15, 2)->nullable();
            $table->integer('review_count')->default(0);
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('address')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index(['status', 'is_verified']);
            $table->index(['developer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};

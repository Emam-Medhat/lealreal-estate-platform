<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('documents')) {
            if (!Schema::hasTable('documents')) {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->integer('file_size');
            $table->json('tags')->nullable();
            $table->string('confidentiality_level')->default('public'); // public, internal, confidential, restricted
            $table->date('expiration_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['category_id']);
            $table->index(['confidentiality_level']);
            $table->index(['expiration_date']);
            $table->index(['is_active']);
        });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};

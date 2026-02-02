<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('document_templates')) {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('content'); // template content with variables
            $table->json('variables')->nullable(); // template variables definition
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['category']);
            $table->index(['is_active']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('document_templates');
    }
};

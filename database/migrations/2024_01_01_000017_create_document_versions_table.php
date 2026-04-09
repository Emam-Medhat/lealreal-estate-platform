<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('document_versions')) {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('version_number');
            $table->text('changes')->nullable(); // description of changes
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->integer('file_size');
            $table->boolean('is_current')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['document_id']);
            $table->index(['is_current']);
            $table->unique(['document_id', 'version_number']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('document_versions');
    }
};

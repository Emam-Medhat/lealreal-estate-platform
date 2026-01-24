<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('action'); // view, download, edit, delete
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
            
            $table->index(['document_id']);
            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_access_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('signature_type'); // digital, electronic, handwritten
            $table->text('signature_data'); // signature image or data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->datetime('signed_at');
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->timestamps();
            
            $table->index(['document_id']);
            $table->index(['user_id']);
            $table->index(['signed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_signatures');
    }
};

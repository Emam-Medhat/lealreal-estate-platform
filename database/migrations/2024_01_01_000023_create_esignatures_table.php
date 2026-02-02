<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('esignatures')) {
        Schema::create('esignatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('signer_id')->constrained('users');
            $table->string('signer_email');
            $table->string('signer_name');
            $table->string('status')->default('pending'); // pending, signed, declined, expired
            $table->string('token')->unique(); // unique token for signing
            $table->datetime('sent_at');
            $table->datetime('signed_at')->nullable();
            $table->datetime('expires_at');
            $table->text('signature_data')->nullable(); // signature image/data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['document_id']);
            $table->index(['signer_id']);
            $table->index(['status']);
            $table->index(['token']);
            $table->index(['expires_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('esignatures');
    }
};

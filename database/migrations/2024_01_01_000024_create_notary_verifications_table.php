<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notary_verifications')) {
        Schema::create('notary_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('verification_code')->unique();
            $table->string('notary_name');
            $table->string('notary_license');
            $table->datetime('verified_at');
            $table->datetime('expires_at');
            $table->string('status')->default('valid'); // valid, expired, revoked
            $table->text('notes')->nullable();
            $table->string('certificate_path')->nullable();
            $table->foreignId('verified_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['document_id']);
            $table->index(['verification_code']);
            $table->index(['status']);
            $table->index(['expires_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notary_verifications');
    }
};

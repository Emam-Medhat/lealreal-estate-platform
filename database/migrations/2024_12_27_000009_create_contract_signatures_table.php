<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contract_signatures')) {
        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('party_type', ['buyer', 'seller', 'agent', 'witness', 'notary', 'other'])->default('buyer');
            $table->enum('status', ['pending', 'signed', 'declined', 'expired'])->default('pending');
            $table->text('signature_data')->nullable();
            $table->string('signature_method', 50)->default('digital'); // digital, wet_ink, electronic
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->string('signature_hash')->nullable();
            $table->json('signature_metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contract_id', 'user_id']);
            $table->index(['contract_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['sent_at', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_signatures');
    }
};

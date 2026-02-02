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
        Schema::create('notary_documents', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('signed');
            $table->string('document_type');
            $table->string('title');
            $table->string('client_name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            
            $table->index(['status', 'created_at']);
            $table->index(['client_name']);
        });

        Schema::create('notary_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
        });

        Schema::create('notary_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->string('title');
            $table->string('type');
            $table->string('status');
            $table->string('icon')->default('file-contract');
            $table->string('color')->default('blue');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
            $table->index(['request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notary_requests');
        Schema::dropIfExists('notary_clients');
        Schema::dropIfExists('notary_documents');
    }
};

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
        if (!Schema::hasTable('sustainability_documentation')) {
        Schema::create('sustainability_documentation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('document_type', 100);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 50);
            $table->integer('file_size')->default(0);
            $table->string('document_number', 255)->nullable();
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuer', 255)->nullable();
            $table->string('status', 50)->default('active');
            $table->string('version', 20)->default('1.0');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('requires_signature')->default(false);
            $table->date('signed_date')->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['document_type']);
            $table->index(['status']);
            $table->index(['document_date']);
            $table->index(['expiry_date']);
            $table->index(['uploaded_by']);
            $table->index(['approved_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sustainability_documentation');
    }
};

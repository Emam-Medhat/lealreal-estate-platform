<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_documents')) {
        Schema::create('tax_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // tax_certificate, assessment_report, exemption_certificate, payment_receipt, filing_confirmation
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('file_type');
            $table->date('expires_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['document_type', 'expires_at']);
            $table->index('user_id');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_documents');
    }
};

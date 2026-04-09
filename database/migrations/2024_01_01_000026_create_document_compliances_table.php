<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('document_compliances')) {
            if (!Schema::hasTable('document_compliances')) {
        Schema::create('document_compliances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
                $table->enum('overall_status', ['compliant', 'non_compliant', 'needs_review'])->default('needs_review');
                $table->text('compliance_notes');
                $table->json('compliance_checks')->nullable();
                $table->decimal('compliance_score', 5, 2)->nullable();
                $table->foreignId('checked_by')->constrained('users');
                $table->timestamp('checked_at');
                $table->date('next_review_date')->nullable();
                $table->timestamps();
                
                $table->index(['document_id']);
                $table->index(['overall_status']);
                $table->index(['checked_by']);
                $table->index(['checked_at']);
                $table->index(['next_review_date']);
                $table->index(['compliance_score']);
            });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('document_compliances');
    }
};

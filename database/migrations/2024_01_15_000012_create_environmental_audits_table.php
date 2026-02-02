<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('environmental_audits')) {
        Schema::create('environmental_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('smart_properties')->onDelete('cascade');
            $table->string('audit_title');
            $table->enum('audit_type', ['comprehensive', 'energy', 'water', 'waste', 'materials', 'compliance'])->default('comprehensive');
            $table->date('audit_date');
            $table->foreignId('auditor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('audit_criteria')->nullable();
            $table->json('findings')->nullable();
            $table->json('non_compliance_issues')->nullable();
            $table->json('recommendations')->nullable();
            $table->decimal('compliance_score', 5, 2)->default(0);
            $table->enum('audit_status', ['scheduled', 'in_progress', 'completed', 'reviewed', 'approved'])->default('scheduled');
            $table->date('follow_up_date')->nullable();
            $table->json('corrective_actions')->nullable();
            $table->string('audit_report_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['property_id', 'audit_date']);
            $table->index('audit_type');
            $table->index('audit_status');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('environmental_audits');
    }
};

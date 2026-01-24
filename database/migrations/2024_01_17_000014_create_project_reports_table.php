<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('report_type', ['progress', 'financial', 'risk', 'performance', 'summary'])->default('progress');
            $table->date('report_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('data')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'generated', 'sent'])->default('draft');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'report_type']);
            $table->index(['report_date']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_reports');
    }
};

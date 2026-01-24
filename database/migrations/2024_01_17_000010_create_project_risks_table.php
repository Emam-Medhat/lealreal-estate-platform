<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['technical', 'financial', 'schedule', 'resource', 'external', 'legal', 'environmental', 'safety']);
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['active', 'mitigated', 'closed'])->default('active');
            $table->text('mitigation_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->date('identified_date');
            $table->date('review_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['category', 'risk_level']);
            $table->index(['owner_id']);
            $table->index(['identified_date', 'review_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_risks');
    }
};

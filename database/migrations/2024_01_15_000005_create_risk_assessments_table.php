<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('risk_assessments')) {
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->enum('assessment_type', ['investment', 'loan', 'crowdfunding']);
            $table->json('criteria');
            $table->decimal('overall_score', 5, 2);
            $table->enum('risk_level', ['منخفض', 'متوسط', 'مرتفع', 'مرتفع جداً']);
            $table->json('recommendations');
            $table->foreignId('assessed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['property_id', 'assessment_type']);
            $table->index(['risk_level']);
            $table->index(['assessed_by']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('risk_assessments');
    }
};

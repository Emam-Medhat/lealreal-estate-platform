<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('energy_efficiency_ratings')) {
        Schema::create('energy_efficiency_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('smart_properties')->onDelete('cascade');
            $table->decimal('efficiency_score', 5, 2)->default(0);
            $table->decimal('energy_consumption_baseline', 10, 2)->default(0);
            $table->decimal('current_consumption', 10, 2)->default(0);
            $table->decimal('savings_percentage', 5, 2)->default(0);
            $table->json('efficiency_metrics')->nullable();
            $table->json('recommendations')->nullable();
            $table->enum('efficiency_level', ['poor', 'fair', 'good', 'excellent', 'outstanding'])->default('poor');
            $table->json('applied_measures')->nullable();
            $table->decimal('cost_savings', 10, 2)->default(0);
            $table->date('assessment_date');
            $table->date('next_assessment_date')->nullable();
            $table->enum('status', ['pending', 'assessed', 'improving', 'certified'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['property_id', 'assessment_date']);
            $table->index('efficiency_score');
            $table->index('efficiency_level');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('energy_efficiency_ratings');
    }
};

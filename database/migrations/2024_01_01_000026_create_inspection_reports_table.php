<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inspection_reports')) {
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained()->onDelete('cascade');
            $table->enum('overall_condition', ['excellent', 'good', 'fair', 'poor']);
            $table->text('summary');
            $table->text('recommendations')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->decimal('estimated_repair_cost', 10, 2)->nullable();
            $table->boolean('urgent_repairs')->default(false);
            $table->dateTime('report_date');
            $table->timestamps();

            $table->index(['inspection_id']);
            $table->index(['inspector_id']);
            $table->index(['report_date']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inspection_reports');
    }
};

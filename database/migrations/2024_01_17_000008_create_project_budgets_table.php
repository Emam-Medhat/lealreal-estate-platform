<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_budgets')) {
        Schema::create('project_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('total_budget', 15, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('budget_type', ['initial', 'revised', 'contingency'])->default('initial');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_budgets');
    }
};

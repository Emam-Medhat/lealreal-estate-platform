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
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('type', ['material', 'labor', 'equipment', 'subcontractor', 'other'])->default('other');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('vendor')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_image')->nullable(); // File path
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null'); // expense_categories table may not exist yet
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['project_id', 'status']);
            $table->index(['expense_date']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};

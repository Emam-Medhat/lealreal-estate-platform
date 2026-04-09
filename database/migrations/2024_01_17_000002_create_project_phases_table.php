<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_phases')) {
        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled']);
            $table->integer('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'order']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_phases');
    }
};

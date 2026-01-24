<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('scheduled_date');
            $table->enum('inspection_type', ['routine', 'detailed', 'pre_sale', 'post_repair']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('estimated_duration')->default(60); // in minutes
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->dateTime('rescheduled_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['scheduled_date', 'status']);
            $table->index(['inspector_id', 'scheduled_date']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};

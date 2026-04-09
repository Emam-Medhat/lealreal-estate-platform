<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('maintenance_tickets')) {
        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency']);
            $table->enum('status', ['open', 'assigned', 'in_progress', 'closed', 'reopened'])->default('open');
            $table->enum('category', ['bug', 'feature', 'request', 'info', 'other']);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('reopened_at')->nullable();
            $table->foreignId('reopened_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reopened_reason')->nullable();
            $table->text('resolution')->nullable();
            $table->integer('satisfaction_rating')->nullable()->comment('1-5 stars');
            $table->text('feedback')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['maintenance_request_id', 'status']);
            $table->index('ticket_number');
            $table->index(['created_at', 'status']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_tickets');
    }
};

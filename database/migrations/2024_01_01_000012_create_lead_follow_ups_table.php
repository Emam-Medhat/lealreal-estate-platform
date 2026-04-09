<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lead_follow_ups')) {
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('type'); // call, email, meeting, task
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('scheduled_date');
            $table->datetime('completed_date')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled, rescheduled
            $table->text('result')->nullable(); // outcome of follow-up
            $table->datetime('next_follow_up')->nullable();
            $table->timestamps();
            
            $table->index(['lead_id', 'scheduled_date']);
            $table->index(['user_id']);
            $table->index(['status']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('type'); // call, email, meeting, note, task
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('activity_date');
            $table->integer('duration_minutes')->nullable(); // for calls/meetings
            $table->string('status')->default('completed'); // completed, scheduled, cancelled
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            
            $table->index(['lead_id', 'activity_date']);
            $table->index(['user_id']);
            $table->index(['type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_activities');
    }
};

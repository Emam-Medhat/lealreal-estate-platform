<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->text('note');
            $table->string('type')->default('general'); // general, important, follow_up
            $table->boolean('is_private')->default(false);
            $table->timestamps();
            
            $table->index(['lead_id']);
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_notes');
    }
};

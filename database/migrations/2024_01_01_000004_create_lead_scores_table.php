<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->integer('score'); // 0-100
            $table->json('factors')->nullable(); // scoring factors and weights
            $table->text('notes')->nullable();
            $table->foreignId('calculated_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['lead_id']);
            $table->index(['score']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_scores');
    }
};

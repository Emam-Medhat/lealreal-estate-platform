<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // views, inquiries, favorites, shares
            $table->integer('count')->default(0);
            $table->date('date');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['property_id', 'metric_type', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_analytics');
    }
};

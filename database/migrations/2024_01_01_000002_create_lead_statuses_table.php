<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // hex color for UI
            $table->integer('order')->default(0); // for pipeline ordering
            $table->boolean('is_active')->default(true);
            $table->boolean('is_converted')->default(false); // indicates this status means lead is converted
            $table->timestamps();
            
            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_statuses');
    }
};

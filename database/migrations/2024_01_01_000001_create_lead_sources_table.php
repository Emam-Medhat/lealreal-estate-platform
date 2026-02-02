<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lead_sources')) {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('weight')->default(1); // for scoring
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('name');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lead_sources');
    }
};

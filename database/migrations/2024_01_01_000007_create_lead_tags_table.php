<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lead_tags')) {
        Schema::create('lead_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable(); // hex color for UI
            $table->text('description')->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('lead_tag_pivot')) {
        Schema::create('lead_tag_pivot', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('lead_tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['lead_id', 'tag_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lead_tag_pivot');
        Schema::dropIfExists('lead_tags');
    }
};

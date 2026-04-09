<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('funnels')) {
        Schema::create('funnels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 50);
            $table->string('status', 20)->default('active');
            $table->timestamps();
            
            $table->index('type');
            $table->index('status');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funnels');
    }
};

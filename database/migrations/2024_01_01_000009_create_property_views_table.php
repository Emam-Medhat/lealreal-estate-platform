<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_views')) {
        Schema::create('property_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referrer')->nullable();
            $table->string('view_type')->default('detail'); // detail, gallery, virtual_tour
            $table->integer('duration_seconds')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_views');
    }
};

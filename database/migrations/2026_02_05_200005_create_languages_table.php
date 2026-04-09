<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique(); // en, ar, etc.
            $table->string('name');
            $table->string('native_name');
            $table->string('direction', 3)->default('ltr'); // ltr, rtl
            $table->string('locale', 10);
            $table->string('flag', 10);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_rtl')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['is_default']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};

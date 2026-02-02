<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cohorts')) {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            
            $table->index('type');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};

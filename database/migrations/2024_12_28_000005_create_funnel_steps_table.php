<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('funnel_steps')) {
        Schema::create('funnel_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('event_name', 100);
            $table->text('description')->nullable();
            $table->integer('order');
            $table->boolean('is_required')->default(true);
            $table->decimal('expected_conversion_rate', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['funnel_id', 'order']);
            $table->index('event_name');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_steps');
    }
};

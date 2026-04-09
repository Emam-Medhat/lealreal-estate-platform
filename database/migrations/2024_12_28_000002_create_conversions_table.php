<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('conversions')) {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('conversion_type', 50);
            $table->decimal('conversion_value', 12, 2)->nullable();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('conversion_step', 50)->nullable();
            $table->string('source')->nullable();
            $table->string('medium')->nullable();
            $table->string('campaign')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
            
            $table->index(['conversion_type', 'converted_at']);
            $table->index('user_session_id');
            $table->index('user_id');
            $table->index('property_id');
            $table->index('converted_at');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};

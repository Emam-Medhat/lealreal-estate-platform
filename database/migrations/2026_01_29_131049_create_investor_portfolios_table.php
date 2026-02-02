<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investor_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('total_value', 15, 2)->default(0);
            $table->decimal('total_invested', 15, 2)->default(0);
            $table->decimal('total_returns', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->json('properties')->nullable(); // Store property IDs
            $table->timestamps();
            
            $table->index(['investor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_portfolios');
    }
};

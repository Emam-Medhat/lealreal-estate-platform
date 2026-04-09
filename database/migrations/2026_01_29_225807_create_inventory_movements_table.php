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
        if (!Schema::hasTable('inventory_movements')) {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('type'); // in, out, transfer
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->nullable(); // Purchase order, sale order, etc.
            $table->text('notes')->nullable();
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['inventory_id', 'type']);
            $table->index(['type', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};

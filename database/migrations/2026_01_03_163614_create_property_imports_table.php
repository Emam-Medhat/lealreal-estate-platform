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
        if (!Schema::hasTable('property_imports')) {
        Schema::create('property_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('source');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->integer('records_processed')->default(0);
            $table->integer('records_imported')->default(0);
            $table->integer('records_failed')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_imports');
    }
};

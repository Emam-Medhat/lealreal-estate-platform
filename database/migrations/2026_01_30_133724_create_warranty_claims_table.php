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
        if (!Schema::hasTable('warranty_claims')) {
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained()->onDelete('cascade');
            $table->string('claim_number')->unique();
            $table->text('description');
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('claim_date');
            $table->date('incident_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed'])->default('pending');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['warranty_id', 'status']);
            $table->index('claim_date');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};

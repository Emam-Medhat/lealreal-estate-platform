<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('assessment_value', 12, 2);
            $table->decimal('market_value', 12, 2);
            $table->date('assessment_date');
            $table->foreignId('assessor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, completed
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_id', 'assessment_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_assessments');
    }
};

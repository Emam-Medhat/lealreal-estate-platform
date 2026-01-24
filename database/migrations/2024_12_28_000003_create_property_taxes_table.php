<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_rate_id')->constrained()->onDelete('restrict');
            $table->decimal('assessment_value', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->integer('tax_year');
            $table->string('status')->default('pending'); // pending, paid, overdue, exempt
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('tax_number')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_id', 'tax_year']);
            $table->index('status');
            $table->index('due_date');
            $table->index('tax_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_taxes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('tax_type');
            $table->decimal('rate', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tax_type', 'is_active']);
            $table->index('effective_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};

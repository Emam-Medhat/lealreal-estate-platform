<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('document_templates');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->string('status')->default('draft'); // draft, active, expired, terminated
            $table->json('terms')->nullable(); // contract terms
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['template_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};

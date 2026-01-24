<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->date('assigned_date');
            $table->date('unassigned_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['lead_id']);
            $table->index(['assigned_to']);
            $table->index(['assigned_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_assignments');
    }
};

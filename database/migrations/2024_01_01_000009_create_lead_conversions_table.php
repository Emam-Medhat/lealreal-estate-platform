<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lead_conversions')) {
        Schema::create('lead_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('converted_to_type'); // client, opportunity, property
            $table->foreignId('converted_to_id')->nullable();
            $table->decimal('conversion_value', 10, 2)->nullable();
            $table->date('conversion_date');
            $table->text('notes')->nullable();
            $table->foreignId('converted_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['lead_id']);
            $table->index(['converted_to_type', 'converted_to_id']);
            $table->index(['conversion_date']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lead_conversions');
    }
};

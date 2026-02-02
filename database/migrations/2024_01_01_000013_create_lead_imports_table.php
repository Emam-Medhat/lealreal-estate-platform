<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lead_imports')) {
        Schema::create('lead_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('total_records');
            $table->integer('imported_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->json('mapping')->nullable(); // column mapping configuration
            $table->json('errors')->nullable(); // import errors
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->foreignId('imported_by')->constrained('users');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['imported_by']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lead_imports');
    }
};

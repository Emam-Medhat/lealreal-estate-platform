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
        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('format'); // pdf, excel, csv, json
            $table->string('filename');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('status'); // processing, completed, failed
            $table->text('error_message')->nullable();
            $table->integer('download_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->string('requested_by')->nullable();
            $table->timestamps();

            $table->index(['report_id', 'format']);
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};

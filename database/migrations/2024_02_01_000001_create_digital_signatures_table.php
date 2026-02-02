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
        if (!Schema::hasTable('digital_signatures')) {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('document_title');
            $table->string('signer_name');
            $table->string('status')->default('active');
            $table->string('verified')->default('متحقق');
            $table->string('icon')->default('check');
            $table->string('color')->default('green');
            $table->string('encryption_level')->default('256-bit');
            $table->string('type');
            $table->string('validity');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
            $table->index(['signer_name']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};

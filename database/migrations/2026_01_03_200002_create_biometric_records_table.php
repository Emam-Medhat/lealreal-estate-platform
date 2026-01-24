<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biometric_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('biometric_type'); // fingerprint, face, voice, iris
            $table->text('biometric_data'); // encrypted biometric template
            $table->string('device_identifier')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'biometric_type', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_records');
    }
};

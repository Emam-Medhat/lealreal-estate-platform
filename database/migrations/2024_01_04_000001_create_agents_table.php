<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agents')) {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('license_number')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->date('hire_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};

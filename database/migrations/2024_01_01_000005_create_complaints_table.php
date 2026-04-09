<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('complaints')) {
            if (!Schema::hasTable('complaints')) {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('complaintable');
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('urgency_level')->default('medium');
            $table->text('expected_resolution')->nullable();
            $table->string('contact_preference')->default('email');
            $table->string('contact_details')->nullable();
            $table->string('status')->default('pending');
            $table->string('reference_number')->unique();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('internal_notes')->nullable();
            $table->text('resolution_details')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'complaintable_type', 'complaintable_id']);
            $table->index('status');
            $table->index('type');
            $table->index('urgency_level');
            $table->index('assigned_to');
            $table->index('reference_number');
            $table->index('last_activity_at');
        });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};

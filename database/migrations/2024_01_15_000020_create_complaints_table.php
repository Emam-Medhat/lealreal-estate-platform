<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('complaints')) {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complainant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('category', ['service', 'property', 'payment', 'contract', 'harassment', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->string('subject');
            $table->text('description');
            $table->json('evidence')->nullable(); // uploaded documents, screenshots
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed', 'dismissed'])->default('open');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};

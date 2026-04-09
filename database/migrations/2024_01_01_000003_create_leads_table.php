<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('leads')) {
            if (!Schema::hasTable('leads')) {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users');
            $table->foreignId('source_id')->nullable()->constrained('lead_sources');
            $table->foreignId('status_id')->nullable()->constrained('lead_statuses');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->integer('priority')->default(1); // 1=low, 2=medium, 3=high
            $table->string('lead_score')->nullable();
            $table->date('last_contact_date')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->boolean('is_converted')->default(false);
            $table->date('converted_date')->nullable();
            $table->foreignId('converted_by')->nullable()->constrained('users');
            $table->string('converted_to_type')->nullable(); // client, opportunity, property
            $table->foreignId('converted_to_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['status_id', 'assigned_to']);
            $table->index(['agent_id']);
            $table->index(['source_id']);
            $table->index(['expected_close_date']);
            $table->index(['priority']);
            $table->index(['is_converted']);
        });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};

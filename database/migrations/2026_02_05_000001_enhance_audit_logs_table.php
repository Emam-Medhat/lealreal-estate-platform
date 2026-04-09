<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action'); // created, updated, deleted, login, etc.
                $table->nullableMorphs('auditable'); // auditable_id, auditable_type
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('details')->nullable(); // For extra info
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('risk_level')->default('low'); // low, medium, high, critical
                $table->boolean('success')->default(true);
                
                // Performance & Context
                $table->string('session_id')->nullable();
                $table->string('request_id')->nullable();
                $table->float('response_time')->nullable(); // in ms
                $table->integer('memory_usage')->nullable(); // in bytes
                
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['user_id', 'created_at']);
                // $table->index(['auditable_type', 'auditable_id']); // Created by nullableMorphs
                $table->index('action');
                $table->index('risk_level');
            });
        } else {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('audit_logs', 'auditable_id')) {
                    $table->nullableMorphs('auditable');
                }
                if (!Schema::hasColumn('audit_logs', 'old_values')) {
                    $table->json('old_values')->nullable();
                }
                if (!Schema::hasColumn('audit_logs', 'new_values')) {
                    $table->json('new_values')->nullable();
                }
                if (!Schema::hasColumn('audit_logs', 'risk_level')) {
                    $table->string('risk_level')->default('low');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};

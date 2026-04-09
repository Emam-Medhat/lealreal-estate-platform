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


        if (!Schema::hasTable('risk_assessments')) {
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('identified');
            $table->string('risk_level');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
        });
        }

        if (!Schema::hasTable('compliance_documents')) {
        Schema::create('compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('completed');
            $table->string('document_type');
            $table->string('title');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
        });
        }

        if (!Schema::hasTable('compliance_reviews')) {
        Schema::create('compliance_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->string('review_type');
            $table->string('title');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
        });
        }

        if (!Schema::hasTable('regulatory_compliance')) {
        Schema::create('regulatory_compliance', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->integer('percentage')->default(100);
            $table->string('icon')->default('check');
            $table->string('color')->default('green');
            $table->timestamps();
            
            $table->index(['status']);
        });
        }

        if (!Schema::hasTable('internal_compliance')) {
        Schema::create('internal_compliance', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('icon')->default('check');
            $table->string('color')->default('green');
            $table->timestamps();
            
            $table->index(['status']);
        });
        }

        if (!Schema::hasTable('compliance_activities')) {
        Schema::create('compliance_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('time');
            $table->string('status');
            $table->string('icon')->default('check');
            $table->string('color')->default('green');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['status', 'created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_activities');
        Schema::dropIfExists('internal_compliance');
        Schema::dropIfExists('regulatory_compliance');
        Schema::dropIfExists('compliance_reviews');
        Schema::dropIfExists('compliance_documents');
        Schema::dropIfExists('risk_assessments');

    }
};

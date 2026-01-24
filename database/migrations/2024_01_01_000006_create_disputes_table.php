<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('respondent_id')->constrained('users')->onDelete('cascade');
                $table->morphs('disputable');
                $table->string('type');
                $table->string('title');
                $table->text('description');
                $table->decimal('dispute_amount', 10, 2)->nullable();
                $table->text('desired_outcome')->nullable();
                $table->text('evidence_description')->nullable();
                $table->string('preferred_resolution_method')->nullable();
                $table->boolean('willing_to_mediate')->default(true);
                $table->string('status')->default('pending');
                $table->string('reference_number')->unique();
                $table->foreignId('mediator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('mediation_started_at')->nullable();
                $table->string('resolution_method')->nullable();
                $table->text('resolution_details')->nullable();
                $table->decimal('resolution_amount', 10, 2)->nullable();
                $table->text('agreement_terms')->nullable();
                $table->timestamp('escalated_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['initiator_id', 'respondent_id']);
                $table->index('status');
                $table->index('type');
                $table->index('mediator_id');
                $table->index('reference_number');
                $table->index('last_activity_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('disputes');
    }
};

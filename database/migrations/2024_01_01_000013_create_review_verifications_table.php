<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('verification_method');
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('review_id');
            $table->index('verified_by');
            $table->index('verification_status');
            $table->index('verification_method');
            $table->unique('review_id', 'review_verifications_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_verifications');
    }
};

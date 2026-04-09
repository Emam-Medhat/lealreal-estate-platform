<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('feedbacks')) {
            if (!Schema::hasTable('feedbacks')) {
        Schema::create('feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->morphs('feedbackable');
                $table->string('type');
                $table->string('category');
                $table->string('title');
                $table->text('content');
                $table->integer('rating')->nullable();
                $table->string('priority')->default('medium');
                $table->json('tags')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->string('status')->default('pending');
                $table->text('admin_notes')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->text('response')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'feedbackable_type', 'feedbackable_id']);
                $table->index('status');
                $table->index('type');
                $table->index('category');
                $table->index('priority');
                $table->index('assigned_to');
                $table->index('rating');
            });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};

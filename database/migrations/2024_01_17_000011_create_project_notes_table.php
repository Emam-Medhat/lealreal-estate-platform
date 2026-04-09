<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_notes')) {
        Schema::create('project_notes', function (Blueprint $table) {
            $table->id();
            $table->string('noteable_type');
            $table->unsignedBigInteger('noteable_id');
            $table->text('content');
            $table->enum('note_type', ['general', 'meeting', 'decision', 'issue', 'update', 'reminder', 'action'])->default('general');
            $table->boolean('is_private')->default(false);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['noteable_type', 'noteable_id']);
            $table->index(['author_id', 'note_type']);
            $table->index(['is_private']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_notes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bim_models')) {
        Schema::create('bim_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('developer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type'); // ifc, rvt, 3dm, etc.
            $table->decimal('file_size', 10, 2); // in MB
            $table->string('version')->default('1.0');
            $table->enum('status', ['draft', 'review', 'approved', 'rejected'])->default('draft');
            $table->json('metadata')->nullable(); // additional BIM data
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bim_models');
    }
};

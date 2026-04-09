<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('building_permits')) {
        Schema::create('building_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('developer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('permit_number')->unique();
            $table->string('permit_type'); // construction, renovation, demolition, etc.
            $table->text('description')->nullable();
            $table->decimal('permit_value', 15, 2);
            $table->date('application_date');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('documents')->nullable(); // uploaded documents paths
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('building_permits');
    }
};

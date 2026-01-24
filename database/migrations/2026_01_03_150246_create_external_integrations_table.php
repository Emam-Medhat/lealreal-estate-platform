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
        Schema::create('external_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->enum('type', ['MLS', 'Mapping', 'Other'])->default('Other');
            $table->foreignId('property_api_id')->nullable()->constrained('property_apis')->onDelete('set null');
            $table->json('configuration')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_integrations');
    }
};

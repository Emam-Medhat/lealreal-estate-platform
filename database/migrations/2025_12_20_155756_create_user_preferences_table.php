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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('language', 2)->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('24h');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('marketing_emails')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->json('theme_preferences')->nullable();
            $table->json('dashboard_settings')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};

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
        Schema::table('service_providers', function (Blueprint $table) {
            if (!Schema::hasColumn('service_providers', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('contact_person');
            $table->dropColumn('contact_email');
            $table->dropColumn('contact_phone');
        });
    }
};

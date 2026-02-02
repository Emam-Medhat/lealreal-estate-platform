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
            if (!Schema::hasColumn('service_providers', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'services')) {
                $table->text('services')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('service_providers', 'license_number')) {
                $table->string('license_number')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'contact_person', 'description', 'services', 'website', 'license_number']);
        });
    }
};

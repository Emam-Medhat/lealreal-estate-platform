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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'type')) {
                $table->string('type')->default('agency')->after('website');
            }
            if (!Schema::hasColumn('companies', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('type');
            }
            if (!Schema::hasColumn('companies', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('registration_number');
            }
            if (!Schema::hasColumn('companies', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('status')->constrained('users');
            }
            if (!Schema::hasColumn('companies', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['type', 'registration_number', 'tax_id', 'created_by', 'slug']);
        });
    }
};

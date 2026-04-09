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
            if (!Schema::hasColumn('companies', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('companies', 'status')) {
                $table->string('status')->default('active')->after('name');
            }
            if (!Schema::hasColumn('companies', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
            if (!Schema::hasColumn('companies', 'email')) {
                $table->string('email')->nullable()->after('description');
            }
            if (!Schema::hasColumn('companies', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('companies', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('companies', 'address')) {
                $table->string('address')->nullable()->after('website');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['name', 'status', 'description', 'email', 'phone', 'website', 'address']);
        });
    }
};

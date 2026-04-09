<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('type')->default('boolean')->after('icon');
            $table->string('unit')->nullable()->after('type');
            $table->decimal('default_value', 10, 2)->nullable()->after('unit');
            $table->boolean('is_included_in_free')->default(false)->after('default_value');
            $table->boolean('is_required')->default(false)->after('is_included_in_free');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->dropColumn(['category', 'type', 'unit', 'default_value', 'is_included_in_free', 'is_required']);
            $table->dropSoftDeletes();
        });
    }
};

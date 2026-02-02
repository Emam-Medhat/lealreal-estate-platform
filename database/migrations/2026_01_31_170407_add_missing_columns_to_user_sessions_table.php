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
        Schema::table('user_sessions', function (Blueprint $table) {
            // Add missing columns
            $table->timestamp('last_activity_at')->nullable()->after('user_agent');
            $table->string('login_method')->default('password')->after('is_active');
            $table->boolean('two_factor_verified')->default(false)->after('login_method');
            $table->boolean('biometric_verified')->default(false)->after('two_factor_verified');
            $table->json('security_flags')->nullable()->after('biometric_verified');
            $table->json('metadata')->nullable()->after('security_flags');
            
            // Add indexes
            $table->index(['user_id', 'last_activity_at']);
            $table->index(['expires_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'last_activity_at']);
            $table->dropIndex(['expires_at', 'is_active']);
            
            $table->dropColumn([
                'last_activity_at',
                'login_method',
                'two_factor_verified',
                'biometric_verified',
                'security_flags',
                'metadata'
            ]);
        });
    }
};

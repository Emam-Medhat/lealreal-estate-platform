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
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'response_code')) {
                $table->integer('response_code')->nullable();
            }
            if (!Schema::hasColumn('requests', 'response_time')) {
                $table->float('response_time')->nullable();
            }
            if (!Schema::hasColumn('requests', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('requests', 'error_message')) {
                $table->text('error_message')->nullable();
            }
            
            $table->index('response_code');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['response_code']);
            $table->dropIndex(['completed_at']);
            $table->dropColumn(['response_code', 'response_time', 'completed_at', 'error_message']);
        });
    }
};

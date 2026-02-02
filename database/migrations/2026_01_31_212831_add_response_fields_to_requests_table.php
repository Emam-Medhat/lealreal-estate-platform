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
            $table->integer('response_code')->nullable();
            $table->float('response_time')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            
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

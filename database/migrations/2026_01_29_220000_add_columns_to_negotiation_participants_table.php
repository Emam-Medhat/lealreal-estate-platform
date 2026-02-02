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
        Schema::table('negotiation_participants', function (Blueprint $table) {
            $table->foreignId('negotiation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negotiation_participants', function (Blueprint $table) {
            $table->dropForeign(['negotiation_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['negotiation_id', 'user_id', 'joined_at', 'status', 'last_read_at']);
        });
    }
};

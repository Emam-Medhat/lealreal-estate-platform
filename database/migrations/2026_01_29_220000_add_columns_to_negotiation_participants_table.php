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
            if (!Schema::hasColumn('negotiation_participants', 'negotiation_id')) {
                $table->foreignId('negotiation_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('negotiation_participants', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('negotiation_participants', 'joined_at')) {
                $table->timestamp('joined_at')->nullable();
            }
            if (!Schema::hasColumn('negotiation_participants', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('negotiation_participants', 'last_read_at')) {
                $table->timestamp('last_read_at')->nullable();
            }
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add agent_id column if it doesn't exist
            if (!Schema::hasColumn('leads', 'agent_id')) {
                $table->foreignId('agent_id')->nullable()->after('country')->constrained('users');
            }
            
            // Add index for agent_id if it doesn't exist
            if (!Schema::hasIndex('leads', 'leads_agent_id_index')) {
                $table->index(['agent_id']);
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropIndex(['agent_id']);
            $table->dropColumn('agent_id');
        });
    }
};
